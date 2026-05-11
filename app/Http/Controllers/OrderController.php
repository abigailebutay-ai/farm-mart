<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Show orders for the current user.
     */
    public function index()
    {
        $user = auth()->user();
        
        if ($user->isFarmer()) {
            // Farmer sees orders containing their products
            $orders = Order::whereHas('items', function ($query) use ($user) {
                $query->where('farmer_id', $user->id);
            })->latest()->paginate(10);
        } else {
            // Consumer sees their own orders
            $orders = $user->orders()->latest()->paginate(10);
        }

        return view('orders.index', ['orders' => $orders]);
    }

    /**
     * Show the specified order.
     */
    public function show(Order $order)
    {
        $user = auth()->user();

        if ($user->isConsumer() && $order->user_id !== $user->id) {
            abort(403);
        }

        if ($user->isFarmer() && !$order->items()->where('farmer_id', $user->id)->exists()) {
            abort(403);
        }

        return view('orders.show', ['order' => $order]);
    }

    /**
     * Checkout - create order from cart.
     */
    public function checkout(Request $request)
    {
        $user = auth()->user();
        
        if ($user->isFarmer()) {
            return redirect()->route('dashboard')->with('error', 'Farmers cannot place orders.');
        }

        $cart = $user->cart;
        
        if (!$cart || $cart->items()->count() === 0) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty!');
        }

        // Create order
        $order = Order::create([
            'user_id' => $user->id,
            'subtotal' => $cart->subtotal,
            'total' => $cart->total,
            'status' => 'pending',
            'notes' => $request->input('notes'),
        ]);

        // Create order items from cart
        foreach ($cart->items as $cartItem) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $cartItem->product_id,
                'farmer_id' => $cartItem->product->user_id,
                'quantity' => $cartItem->quantity,
                'price' => $cartItem->price,
                'subtotal' => $cartItem->subtotal,
            ]);
        }

        // Clear cart
        $cart->items()->delete();
        $cart->calculateTotals();

        return redirect()->route('orders.show', $order)->with('success', 'Order placed successfully!');
    }

    /**
     * Update order status (for farmers).
     */
    public function updateStatus(Request $request, Order $order)
    {
        $user = auth()->user();

        if (!$user->isFarmer() || !$order->items()->where('farmer_id', $user->id)->exists()) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,accepted,completed,cancelled',
        ]);

        $order->status = $validated['status'];
        $order->save();

        return redirect()->route('orders.show', $order)->with('success', 'Order status updated!');
    }

    /**
     * Show checkout form.
     */
    public function showCheckout()
    {
        $user = auth()->user();
        
        if ($user->isFarmer()) {
            return redirect()->route('dashboard')->with('error', 'Farmers cannot place orders.');
        }

        $cart = $user->cart;
        
        if (!$cart || $cart->items()->count() === 0) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty!');
        }

        return view('orders.checkout', ['cart' => $cart]);
    }
}
