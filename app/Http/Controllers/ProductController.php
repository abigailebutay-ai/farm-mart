<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of products (for consumers).
     */
    public function index(Request $request)
    {
        $query = $this->marketplaceQuery($request);

        $products = $query->latest()->paginate(12)->withQueryString();
        $categories = Product::where(function ($query) {
            $query->whereNull('status')
                ->orWhere('status', 'active');
        })->select('category')->distinct()->get();

        return view('products.index', [
            'products' => $products,
            'categories' => $categories,
            'search' => $request->search ?? '',
            'availability' => $request->availability ?? '',
            'marketplaceRoute' => 'marketplace',
        ]);
    }

    /**
     * Display available products inside the consumer dashboard shell.
     */
    public function consumerMarketplace(Request $request)
    {
        $query = $this->marketplaceQuery($request)->where('quantity', '>', 0);

        $products = $query->latest()->paginate(12)->withQueryString();
        $categories = Product::where('quantity', '>', 0)
            ->where(function ($query) {
                $query->whereNull('status')
                    ->orWhere('status', 'active');
            })
            ->select('category')
            ->distinct()
            ->get();

        return view('consumer.marketplace', [
            'products' => $products,
            'categories' => $categories,
            'search' => $request->search ?? '',
            'availability' => $request->availability ?? '',
        ]);
    }

    public function redirectToMarketplace()
    {
        return redirect()->route('marketplace');
    }

    /**
     * Show the form for creating a new product (farmer).
     */
    public function create()
    {
        return view('products.create');
    }

    /**
     * Store a newly created product (farmer).
     */
    public function store(Request $request, NotificationService $notifications)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string|max:100',
            'price' => 'required|numeric|min:0.01',
            'quantity' => 'required|integer|min:1',
            'unit' => 'required|in:' . implode(',', Product::UNITS),
            'image' => $this->imageValidationRule(),
        ], $this->imageValidationMessages());

        $validated['user_id'] = auth()->id();

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')
                ->storePublicly('products', config('filesystems.default'));

            $this->logProductImageUpload($validated['image']);
        }

        $product = Product::create($validated);

        $notifications->sendToAdmins(
            'product.created',
            'New product listed',
            auth()->user()->name . " listed {$product->name}.",
            'products',
            route('admin.products.show', $product),
            ['product_id' => $product->id]
        );

        return redirect()->route('farmer.products.index')->with('success', 'Product created successfully!');
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product)
    {
        return view('products.show', ['product' => $product]);
    }

    /**
     * Show the form for editing the specified product (farmer).
     */
    public function edit(Product $product)
    {
        $this->authorize('update', $product);
        return view('products.edit', ['product' => $product]);
    }

    /**
     * Update the specified product (farmer).
     */
    public function update(Request $request, Product $product, NotificationService $notifications)
    {
        $this->authorize('update', $product);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string|max:100',
            'price' => 'required|numeric|min:0.01',
            'quantity' => 'required|integer|min:0',
            'unit' => 'required|in:' . implode(',', Product::UNITS),
            'image' => $this->imageValidationRule(),
        ], $this->imageValidationMessages());

        if ($request->hasFile('image')) {
            if ($product->image_storage_path) {
                Storage::disk(config('filesystems.default'))->delete($product->image_storage_path);
            }

            $validated['image'] = $request->file('image')
                ->storePublicly('products', config('filesystems.default'));

            $this->logProductImageUpload($validated['image']);
        }

        $oldQuantity = $product->quantity;

        $product->update($validated);

        if ($oldQuantity !== $product->quantity) {
            $this->notifyStockStatus($product->fresh(), $notifications, $oldQuantity);
        }

        return redirect()->route('farmer.products.index')->with('success', 'Product updated successfully!');
    }

    /**
     * Update only inventory quantities for a farmer product.
     */
    public function updateInventory(Request $request, Product $product, NotificationService $notifications)
    {
        $this->authorize('update', $product);

        $validated = $request->validate([
            'mode' => 'nullable|in:set,add,reduce',
            'quantity' => 'required|integer|min:0',
        ]);

        $mode = $validated['mode'] ?? 'set';
        $quantity = (int) $validated['quantity'];

        $newQuantity = match ($mode) {
            'add' => $product->quantity + $quantity,
            'reduce' => max($product->quantity - $quantity, 0),
            default => $quantity,
        };

        $oldQuantity = $product->quantity;

        $product->update(['quantity' => $newQuantity]);

        $this->notifyStockStatus($product->fresh(), $notifications, $oldQuantity);

        return redirect()->route('farmer.inventory.index')->with('success', 'Inventory updated successfully!');
    }

    public function restock(Request $request, Product $product, NotificationService $notifications)
    {
        $this->authorize('update', $product);

        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ], [
            'quantity.required' => 'Please enter a valid quantity.',
            'quantity.integer' => 'Please enter a valid quantity.',
            'quantity.min' => 'Please enter a valid quantity.',
        ]);

        $oldQuantity = $product->quantity;

        $product->increment('quantity', $validated['quantity']);
        $product->refresh();

        if ($oldQuantity <= 0 && $product->quantity > 0) {
            $notifications->send(
                auth()->user(),
                'product.restocked',
                'Product restocked',
                "{$product->name} has been restocked to {$product->quantity} {$product->unit}.",
                'inventory',
                route('farmer.inventory.index'),
                ['product_id' => $product->id]
            );
        }

        return back()->with('success', 'Product restocked successfully.');
    }

    /**
     * Remove the specified product (farmer).
     */
    public function destroy(Product $product)
    {
        $this->authorize('delete', $product);

        try {
            if ($product->orderItems()->exists()) {
                $product->cartItems()->delete();

                $product->update([
                    'status' => 'inactive',
                    'quantity' => 0,
                ]);

                return redirect()
                    ->route('farmer.products.index')
                    ->with('success', 'Product archived because it already has order history.');
            }

            $product->cartItems()->delete();

            if ($product->image_storage_path) {
                try {
                    Storage::disk(config('filesystems.default'))->delete($product->image_storage_path);
                } catch (\Throwable $e) {
                    report($e);
                }
            }

            $product->delete();

            return redirect()->route('farmer.products.index')->with('success', 'Product deleted successfully!');
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', 'Product could not be deleted because it is connected to existing records.');
        }
    }

    /**
     * Show farmer's products list.
     */
    public function farmerProducts()
    {
        $products = auth()->user()->products()->withCount('orderItems')->paginate(10);
        return view('products.farmer-list', ['products' => $products]);
    }

    /**
     * Show farmer inventory controls.
     */
    public function inventory()
    {
        $products = auth()->user()->products()->orderBy('quantity')->paginate(10);

        return view('products.inventory', [
            'products' => $products,
            'totalInventoryQuantity' => auth()->user()->products()->sum('quantity'),
            'lowStockCount' => auth()->user()->products()->whereBetween('quantity', [1, 10])->count(),
            'outOfStockCount' => auth()->user()->products()->where('quantity', '<=', 0)->count(),
            'recentStockActivity' => auth()->user()->products()->latest('updated_at')->limit(5)->get(),
        ]);
    }

    /**
     * Printable farmer inventory report.
     */
    public function printInventory()
    {
        $user = auth()->user();

        return view('prints.farmer-inventory', [
            'farmer' => $user,
            'reportDate' => now(),
            'products' => $user->products()->orderBy('name')->get(),
        ]);
    }

    private function marketplaceQuery(Request $request)
    {
        $query = Product::with('farmer')
            ->where(function ($query) {
                $query->whereNull('status')
                    ->orWhere('status', 'active');
            });

        if ($request->filled('search')) {
            $query->where(function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->availability === 'in_stock') {
            $query->where('quantity', '>', 10);
        } elseif ($request->availability === 'low_stock') {
            $query->whereBetween('quantity', [1, 10]);
        } elseif ($request->availability === 'out_of_stock') {
            $query->where('quantity', '<=', 0);
        }

        return $query;
    }

    private function logProductImageUpload(string $path): void
    {
        Log::info('Product image uploaded', [
            'disk' => config('filesystems.default'),
            'path' => $path,
            'exists' => Storage::disk(config('filesystems.default'))->exists($path),
        ]);
    }

    private function imageValidationRule(): string
    {
        return 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120';
    }

    private function imageValidationMessages(): array
    {
        return [
            'image.image' => 'Please upload a JPG, PNG, GIF, or WEBP image up to 5MB.',
            'image.mimes' => 'Please upload a JPG, PNG, GIF, or WEBP image up to 5MB.',
            'image.max' => 'Please upload a JPG, PNG, GIF, or WEBP image up to 5MB.',
        ];
    }

    private function notifyStockStatus(Product $product, NotificationService $notifications, ?int $oldQuantity = null): void
    {
        $farmer = $product->farmer;

        if (! $farmer) {
            return;
        }

        if ($product->quantity <= 0 && ($oldQuantity === null || $oldQuantity > 0)) {
            $notifications->send(
                $farmer,
                'product.out_of_stock',
                'Product out of stock',
                "{$product->name} is now out of stock.",
                'alert',
                route('farmer.inventory.index'),
                ['product_id' => $product->id]
            );

            return;
        }

        if ($product->quantity <= 10 && $product->quantity > 0 && ($oldQuantity === null || $oldQuantity > 10)) {
            $notifications->send(
                $farmer,
                'product.low_stock',
                'Low stock alert',
                "{$product->name} is down to {$product->quantity} {$product->unit}.",
                'inventory',
                route('farmer.inventory.index'),
                ['product_id' => $product->id]
            );
        }
    }
}
