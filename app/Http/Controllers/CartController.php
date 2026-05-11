<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Show the cart.
     */
    public function index()
    {
        $user = auth()->user();
        $cart = $user->cart ?? Cart::create(['user_id' => $user->id]);
        $cart->calculateTotals();

        return view('cart.index', ['cart' => $cart]);
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
        $cart = $user->cart ?? Cart::create(['user_id' => $user->id]);

        $cartItem = $cart->items()->where('product_id', $product->id)->first();

        if ($cartItem) {
            $cartItem->quantity += $validated['quantity'];
            $cartItem->updateSubtotal();
        } else {
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'quantity' => $validated['quantity'],
                'price' => $product->price,
                'subtotal' => $product->price * $validated['quantity'],
            ]);
            $cart->calculateTotals();
        }

        return redirect()->route('cart.index')->with('success', 'Product added to cart!');
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
        $cartItem->updateSubtotal();

        return redirect()->route('cart.index')->with('success', 'Cart updated!');
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

        return redirect()->route('cart.index')->with('success', 'Item removed from cart!');
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

        return redirect()->route('cart.index')->with('success', 'Cart cleared!');
    }
}
