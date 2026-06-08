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

        $cart->loadMissing('items.product');
        $totalKg = $this->cartTotalKg($cart);
        $eligibleDiscount = $discountService->getEligibleDiscount($totalKg, (float) $cart->subtotal);
        $appliedDiscount = session('cart_discount');

        return view('cart.index', [
            'cart' => $cart,
            'totalKg' => $totalKg,
            'eligibleDiscount' => $eligibleDiscount,
            'appliedDiscount' => $appliedDiscount,
            'discountAmount' => $appliedDiscount ? (float) ($eligibleDiscount['discount_amount'] ?? 0) : 0,
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
        session()->forget(['cart_discount', 'checkout_coupon_id']);

        return redirect()
            ->route('cart.index')
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
        session()->forget(['cart_discount', 'checkout_coupon_id']);

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
        session()->forget(['cart_discount', 'checkout_coupon_id']);

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

        session()->forget(['cart_discount', 'checkout_coupon_id']);

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

        $cart->calculateTotals();
        $eligibleDiscount = $discountService->getEligibleDiscount(
            $this->cartTotalKg($cart),
            (float) $cart->subtotal
        );

        if (! ($eligibleDiscount['eligible'] ?? false)) {
            session()->forget(['cart_discount', 'checkout_coupon_id']);

            return back()->with('error', 'No bulk discount available yet. Add more kg to qualify for a discount.');
        }

        session(['cart_discount' => [
            'label' => $eligibleDiscount['label'],
            'discount_type' => $eligibleDiscount['discount_type'],
            'discount_rate' => $eligibleDiscount['discount_rate'],
            'minimum_kg' => $eligibleDiscount['minimum_kg'],
        ]]);
        session()->forget('checkout_coupon_id');

        return back()->with('success', 'Bulk discount applied successfully.');
    }

    public function removeDiscount()
    {
        session()->forget(['cart_discount', 'checkout_coupon_id']);

        return back()->with('success', 'Bulk discount removed.');
    }

    private function cartTotalKg(Cart $cart): float
    {
        $cart->loadMissing('items.product');

        return (float) $cart->items->sum(fn ($item) => (float) $item->quantity);
    }
}
