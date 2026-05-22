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
    public function index(Request $request)
    {
        $user = auth()->user();
        $status = $request->query('status', 'all');
        $allowedStatuses = ['all', 'pending', 'accepted', 'preparing', 'completed', 'cancelled'];

        if (! in_array($status, $allowedStatuses, true)) {
            $status = 'all';
        }

        if ($user->isAdmin()) {

            // Admin sees all marketplace orders for monitoring.
            $orders = Order::with('consumer')
                ->withCount('items')
                ->latest()
                ->paginate(10);

        } elseif ($user->isFarmer()) {

            // Farmer sees orders containing their products
            $orders = Order::whereHas('items', function ($query) use ($user) {

                $query->where('farmer_id', $user->id);

            })->withCount('items')->latest()->paginate(10);

        } else {

            // Consumer sees their own orders
            $ordersQuery = $user->orders()->withCount('items');

            if ($status !== 'all') {
                $ordersQuery->where('status', $status);
            }

            $orders = $ordersQuery->latest()->paginate(10)->withQueryString();
        }

        return view('orders.index', [
            'orders' => $orders,
            'status' => $status,
            'statusFilters' => $allowedStatuses,
        ]);
    }

    /**
     * Show completed purchases for the current consumer.
     */
    public function purchaseHistory()
    {
        return redirect()->route('orders.index', ['status' => 'completed']);
    }

    /**
     * Show the specified order.
     */
    public function show(Order $order)
    {
        $user = auth()->user();

        // Consumer protection
        if (
            !$user->isAdmin() &&
            $user->isConsumer() &&
            $order->user_id !== $user->id
        ) {
            abort(403);
        }

        // Farmer protection
        if (
            !$user->isAdmin() &&
            $user->isFarmer() &&
            !$order->items()
                ->where('farmer_id', $user->id)
                ->exists()
        ) {
            abort(403);
        }

        return view('orders.show', [
            'order' => $order->loadMissing(['consumer', 'items.product', 'items.farmer'])
        ]);
    }

    /**
     * Show a printable receipt for a completed consumer order.
     */
    public function receipt(Order $order)
    {
        $user = auth()->user();

        if (! $user->isConsumer() || $order->user_id !== $user->id) {
            abort(403);
        }

        if ($order->status !== 'completed') {
            abort(404, 'Receipt is available after order completion.');
        }

        return view('orders.receipt', [
            'order' => $order->load(['consumer', 'items.product', 'items.farmer']),
        ]);
    }

    /**
     * Checkout - create order from cart.
     */
    public function checkout(Request $request)
    {
        $user = auth()->user();

        // Farmers cannot checkout
        if ($user->isFarmer()) {

            return redirect()
                ->route('dashboard')
                ->with('error', 'Farmers cannot place orders.');
        }

        $cart = $user->cart;

        // Empty cart check
        if (
            !$cart ||
            $cart->items()->count() === 0
        ) {

            return redirect()
                ->route('cart.index')
                ->with('error', 'Your cart is empty!');
        }

        // Create order
        $order = Order::create([
            'user_id' => $user->id,
            'subtotal' => $cart->subtotal,
            'total' => $cart->total,
            'status' => 'pending',
            'notes' => $request->input('notes'),
        ]);

        // Create order items
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

        return redirect()
            ->route('orders.show', $order)
            ->with('success', 'Order placed successfully!');
    }

    /**
     * Update order status (for farmers).
     */
    public function updateStatus(Request $request, Order $order)
    {
        $user = auth()->user();

        // Verify farmer owns products in this order
        if (
            !$user->isFarmer() ||
            !$order->items()
                ->where('farmer_id', $user->id)
                ->exists()
        ) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,accepted,completed,cancelled',
        ]);

        // Store old status
        $oldStatus = $order->status;

        // Update status
        $order->status = $validated['status'];

        $order->save();

        /**
         * Reduce product quantity
         * ONLY when changing to completed
         * and only once
         */
        if (
            $validated['status'] === 'completed' &&
            $oldStatus !== 'completed'
        ) {

            foreach ($order->items as $item) {

                $product = $item->product;

                if ($product) {

                    // Prevent negative stock
                    if ($product->quantity >= $item->quantity) {

                        $product->quantity -= $item->quantity;

                    } else {

                        $product->quantity = 0;
                    }

                    $product->save();
                }
            }
        }

        return redirect()
            ->route('orders.show', $order)
            ->with('success', 'Order status updated!');
    }

    /**
     * Show checkout form.
     */
    public function showCheckout()
    {
        $user = auth()->user();

        // Farmers cannot checkout
        if ($user->isFarmer()) {

            return redirect()
                ->route('dashboard')
                ->with('error', 'Farmers cannot place orders.');
        }

        $cart = $user->cart;

        // Empty cart check
        if (
            !$cart ||
            $cart->items()->count() === 0
        ) {

            return redirect()
                ->route('cart.index')
                ->with('error', 'Your cart is empty!');
        }

        return view('orders.checkout', [
            'cart' => $cart
        ]);
    }
}
