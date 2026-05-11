<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of products (for consumers).
     */
    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        if ($request->has('category') && $request->category !== '') {
            $query->where('category', $request->category);
        }

        $products = $query->paginate(12);
        $categories = Product::select('category')->distinct()->get();

        return view('products.index', [
            'products' => $products,
            'categories' => $categories,
            'search' => $request->search ?? '',
        ]);
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
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string|max:100',
            'price' => 'required|numeric|min:0.01',
            'quantity' => 'required|integer|min:1',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $validated['user_id'] = auth()->id();

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        Product::create($validated);

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
    public function update(Request $request, Product $product)
    {
        $this->authorize('update', $product);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string|max:100',
            'price' => 'required|numeric|min:0.01',
            'quantity' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($validated);

        return redirect()->route('farmer.products.index')->with('success', 'Product updated successfully!');
    }

    /**
     * Remove the specified product (farmer).
     */
    public function destroy(Product $product)
    {
        $this->authorize('delete', $product);

        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('farmer.products.index')->with('success', 'Product deleted successfully!');
    }

    /**
     * Show farmer's products list.
     */
    public function farmerProducts()
    {
        $products = auth()->user()->products()->paginate(10);
        return view('products.farmer-list', ['products' => $products]);
    }
}
