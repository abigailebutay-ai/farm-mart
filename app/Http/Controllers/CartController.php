<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Services\DiscountService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Show the cart.
     */
    public function index(DiscountService $discountService)
    {
        $user = auth()->user();

        $cart = $user->cart ?? Cart::create([
            'user_id' => $user->id
        ]);

        $cart->calculateTotals();

        $cart->loadMissing('items.product.farmer');
        $farmerGroups = $this->farmerGroups($cart, $discountService);

        return view('cart.index', [
            'cart' => $cart,
            'farmerGroups' => $farmerGroups,
        ]);
    }

    /**
     * Add item to cart.
     */
    public function add(Request $request, Product $product)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $user = auth()->user();

        $cart = $user->cart ?? Cart::create([
            'user_id' => $user->id
        ]);

        $cartItem = $cart->items()
            ->where('product_id', $product->id)
            ->first();

        if ($cartItem) {

            $cartItem->quantity += $validated['quantity'];

            $cartItem->subtotal =
                $cartItem->price * $cartItem->quantity;

            $cartItem->save();

        } else {

            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'quantity' => $validated['quantity'],
                'price' => $product->price,
                'subtotal' => $product->price * $validated['quantity'],
            ]);
        }

        $cart->calculateTotals();
        session()->forget(['cart_discount', 'cart_discounts', 'checkout_coupon_id']);

        $redirectRoute = auth()->user()->isConsumer()
            ? route('consumer.marketplace.farmer', $product->farmer)
            : route('cart.index');

        return redirect($redirectRoute)
            ->with('success', 'Product added to cart!');
    }

    /**
     * Update cart item quantity.
     */
    public function updateQuantity(Request $request, CartItem $cartItem)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $this->authorize('update', $cartItem);

        $cartItem->quantity = $validated['quantity'];

        $cartItem->subtotal =
            $cartItem->price * $validated['quantity'];

        $cartItem->save();

        $cartItem->cart->calculateTotals();
        session()->forget(['cart_discount', 'cart_discounts', 'checkout_coupon_id']);

        return redirect()
            ->route('cart.index')
            ->with('success', 'Cart updated!');
    }

    /**
     * Remove item from cart.
     */
    public function remove(CartItem $cartItem)
    {
        $this->authorize('delete', $cartItem);

        $cart = $cartItem->cart;

        $cartItem->delete();

        $cart->calculateTotals();
        session()->forget(['cart_discount', 'cart_discounts', 'checkout_coupon_id']);

        return redirect()
            ->route('cart.index')
            ->with('success', 'Item removed from cart!');
    }

    /**
     * Clear the entire cart.
     */
    public function clear()
    {
        $cart = auth()->user()->cart;

        if ($cart) {

            $cart->items()->delete();

            $cart->calculateTotals();
        }

        session()->forget(['cart_discount', 'cart_discounts', 'checkout_coupon_id']);

        return redirect()
            ->route('cart.index')
            ->with('success', 'Cart cleared!');
    }

    public function applyDiscount(DiscountService $discountService)
    {
        $cart = auth()->user()->cart?->loadMissing('items.product');

        if (! $cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty!');
        }

        $validated = request()->validate([
            'farmer_id' => 'required|integer',
        ]);

        $farmerId = (int) $validated['farmer_id'];
        $items = $this->itemsForFarmer($cart, $farmerId);

        if ($items->isEmpty()) {
            return back()->with('error', 'Please choose which farmer you want to apply the discount for.');
        }

        $eligibleDiscount = $discountService->getEligibleDiscount(
            (float) $items->sum('quantity'),
            (float) $items->sum('subtotal')
        );

        if (! ($eligibleDiscount['eligible'] ?? false)) {
            $this->forgetFarmerDiscount($farmerId);

            return back()->with('error', 'No bulk discount available yet for this farmer. Add more kg from this farmer to qualify.');
        }

        $discounts = session('cart_discounts', []);
        $discounts[$farmerId] = [
            'label' => $eligibleDiscount['label'],
            'discount_type' => $eligibleDiscount['discount_type'],
            'discount_rate' => $eligibleDiscount['discount_rate'],
            'minimum_kg' => $eligibleDiscount['minimum_kg'],
        ];

        session(['cart_discounts' => $discounts]);
        session()->forget('checkout_coupon_id');

        return back()->with('success', 'Bulk discount applied successfully.');
    }

    public function removeDiscount()
    {
        $farmerId = request()->integer('farmer_id');

        if ($farmerId) {
            $this->forgetFarmerDiscount($farmerId);
        } else {
            session()->forget(['cart_discount', 'cart_discounts', 'checkout_coupon_id']);
        }

        return back()->with('success', 'Bulk discount removed.');
    }

    private function farmerGroups(Cart $cart, DiscountService $discountService)
    {
        $cart->loadMissing('items.product.farmer');
        $appliedDiscounts = session('cart_discounts', []);

        return $cart->items
            ->filter(fn ($item) => $item->product?->farmer)
            ->groupBy(fn ($item) => $item->product->user_id)
            ->map(function ($items, $farmerId) use ($discountService, $appliedDiscounts) {
                $subtotal = (float) $items->sum('subtotal');
                $totalKg = (float) $items->sum('quantity');
                $eligibleDiscount = $discountService->getEligibleDiscount($totalKg, $subtotal);
                $hasAppliedDiscount = isset($appliedDiscounts[$farmerId]) && ($eligibleDiscount['eligible'] ?? false);

                return [
                    'farmer' => $items->first()->product->farmer,
                    'items' => $items,
                    'subtotal' => $subtotal,
                    'totalKg' => $totalKg,
                    'eligibleDiscount' => $eligibleDiscount,
                    'appliedDiscount' => $hasAppliedDiscount ? $appliedDiscounts[$farmerId] : null,
                    'discountAmount' => $hasAppliedDiscount ? (float) ($eligibleDiscount['discount_amount'] ?? 0) : 0,
                    'total' => max($subtotal - ($hasAppliedDiscount ? (float) ($eligibleDiscount['discount_amount'] ?? 0) : 0), 0),
                ];
            })
            ->values();
    }

    private function itemsForFarmer(Cart $cart, int $farmerId)
    {
        $cart->loadMissing('items.product');

        return $cart->items->filter(fn ($item) => (int) ($item->product?->user_id) === $farmerId);
    }

    private function forgetFarmerDiscount(int $farmerId): void
    {
        $discounts = session('cart_discounts', []);
        unset($discounts[$farmerId]);

        if ($discounts) {
            session(['cart_discounts' => $discounts]);
        } else {
            session()->forget('cart_discounts');
        }

        session()->forget(['cart_discount', 'checkout_coupon_id']);
    }
}
