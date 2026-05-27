<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

            })
                ->where(function ($query) {
                    $query->whereNull('payment_method')
                        ->orWhere('payment_method', 'cod')
                        ->orWhere(function ($query) {
                            $query->where('payment_method', 'gcash')
                                ->whereNotNull('payment_proof');
                        });
                })
                ->with(['consumer', 'items.product'])
                ->withCount('items')
                ->latest()
                ->paginate(10);

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
    public function checkout(Request $request, NotificationService $notifications)
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

        $validated = $request->validate([
            'notes' => 'nullable|string|max:1000',
            'payment_method' => 'required|in:cod,gcash',
            'payment_reference' => 'nullable|string',
            'payment_proof' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:5120',
        ]);

        if ($validated['payment_method'] === 'gcash') {
            if (empty($validated['payment_reference']) || ! preg_match('/^\d{11}$/', $validated['payment_reference'])) {
                return back()
                    ->withErrors(['payment_reference' => 'GCash reference number must be exactly 11 digits.'])
                    ->withInput();
            }

            if (! $request->hasFile('payment_proof')) {
                return back()
                    ->withErrors(['payment_proof' => 'Proof of payment is required for GCash orders.'])
                    ->withInput();
            }
        }

        $paymentData = [
            'payment_method' => 'cod',
            'payment_status' => 'pending',
            'payment_reference' => null,
            'payment_proof' => null,
        ];

        if ($validated['payment_method'] === 'gcash') {
            $paymentData = [
                'payment_method' => 'gcash',
                'payment_status' => 'pending_verification',
                'payment_reference' => $validated['payment_reference'],
                'payment_proof' => $request->file('payment_proof')->storePublicly('payment_proofs', config('filesystems.default')),
            ];
        }

        $order = DB::transaction(function () use ($cart, $paymentData, $user, $validated) {
            $order = Order::create([
                'user_id' => $user->id,
                'subtotal' => $cart->subtotal,
                'total' => $cart->total,
                'status' => 'pending',
                'notes' => $validated['notes'] ?? null,
            ] + $paymentData);

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

            $cart->items()->delete();
            $cart->calculateTotals();

            return $order;
        });

        $order->loadMissing(['consumer', 'items.farmer']);

        $order->items
            ->pluck('farmer')
            ->filter()
            ->unique('id')
            ->each(function ($farmer) use ($order, $notifications) {
                $notifications->send(
                    $farmer,
                    'order.created',
                    'New order received',
                    "Order #{$order->id} includes one or more of your products.",
                    'orders',
                    route('orders.show', $order),
                    ['order_id' => $order->id]
                );
            });

        $notifications->sendToAdmins(
            'order.created',
            'New order placed',
            ($order->consumer->name ?? 'A buyer') . " placed Order #{$order->id}.",
            'orders',
            route('orders.show', $order),
            ['order_id' => $order->id]
        );

        if ($order->payment_method === 'gcash') {
            $notifications->sendToAdmins(
                'payment.proof_uploaded',
                'GCash payment proof uploaded',
                ($order->consumer->name ?? 'A buyer') . " uploaded proof for Order #{$order->id}.",
                'money',
                route('orders.show', $order),
                ['order_id' => $order->id, 'payment_status' => $order->payment_status]
            );
        }

        $successMessage = $order->payment_method === 'gcash'
            ? 'Order placed successfully. Your payment is pending verification.'
            : 'Order placed successfully. Please prepare cash upon delivery.';

        return redirect()
            ->route('orders.show', $order)
            ->with('success', $successMessage);
    }

    /**
     * Update order status (for farmers).
     */
    public function updateStatus(Request $request, Order $order, NotificationService $notifications)
    {
        $this->ensureFarmerOwnsOrder($order);

        $validated = $request->validate([
            'status' => 'required|in:pending,accepted,preparing,completed,cancelled',
        ]);

        if ($validated['status'] === $order->status) {
            return redirect()
                ->route('orders.show', $order)
                ->with('success', 'Order status updated!');
        }

        $result = match ($validated['status']) {
            'accepted' => $this->transitionOrder(
                $order,
                'accepted',
                ['pending'],
                'Only pending orders can be accepted.',
                'Your order has been accepted.',
                $notifications
            ),
            'preparing' => $this->transitionOrder(
                $order,
                'preparing',
                ['accepted'],
                'This order must be accepted before preparing.',
                'Your order is now being prepared.',
                $notifications
            ),
            'completed' => $this->transitionOrder(
                $order,
                'completed',
                ['preparing'],
                'This order must be preparing before completion.',
                'Your order has been completed.',
                $notifications
            ),
            'cancelled' => $this->transitionOrder(
                $order,
                'cancelled',
                ['pending', 'accepted'],
                'This order can no longer be cancelled.',
                'Your order was cancelled.',
                $notifications
            ),
            default => ['ok' => false, 'message' => 'Invalid order status update.'],
        };

        if (! $result['ok']) {
            return back()->with('error', $result['message']);
        }

        return redirect()
            ->route('orders.show', $order)
            ->with('success', 'Order status updated!');
    }

    public function cancelByConsumer(Order $order, NotificationService $notifications)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        if (! in_array($order->status, ['pending', 'accepted'], true)) {
            return back()->with('error', 'This order can no longer be cancelled.');
        }

        if ($order->created_at->lt(now()->subDay())) {
            return back()->with('error', 'Cancellation is only allowed within 24 hours after ordering.');
        }

        $order->update([
            'status' => 'cancelled',
        ]);

        $order->loadMissing('items.farmer');

        $order->items
            ->pluck('farmer')
            ->filter()
            ->unique('id')
            ->each(function ($farmer) use ($order, $notifications) {
                $notifications->send(
                    $farmer,
                    'order.cancelled',
                    'Order cancelled',
                    "Order #{$order->id} was cancelled by the buyer.",
                    'alert',
                    route('orders.show', $order),
                    ['order_id' => $order->id, 'status' => 'cancelled']
                );
            });

        return back()->with('success', 'Order cancelled successfully.');
    }

    public function acceptOrder(Order $order, NotificationService $notifications)
    {
        $this->ensureFarmerOwnsOrder($order);

        $result = $this->transitionOrder(
            $order,
            'accepted',
            ['pending'],
            'Only pending orders can be accepted.',
            'Your order has been accepted.',
            $notifications
        );

        return $this->redirectAfterFarmerTransition($order, $result, 'Order accepted successfully.');
    }

    public function markPreparing(Order $order, NotificationService $notifications)
    {
        $this->ensureFarmerOwnsOrder($order);

        $result = $this->transitionOrder(
            $order,
            'preparing',
            ['accepted'],
            'This order must be accepted before preparing.',
            'Your order is now being prepared.',
            $notifications
        );

        return $this->redirectAfterFarmerTransition($order, $result, 'Order marked as preparing.');
    }

    public function markCompleted(Order $order, NotificationService $notifications)
    {
        $this->ensureFarmerOwnsOrder($order);

        $result = $this->transitionOrder(
            $order,
            'completed',
            ['preparing'],
            'This order must be preparing before completion.',
            'Your order has been completed.',
            $notifications
        );

        return $this->redirectAfterFarmerTransition($order, $result, 'Order marked as completed.');
    }

    public function cancelByFarmer(Order $order, NotificationService $notifications)
    {
        $this->ensureFarmerOwnsOrder($order);

        $result = $this->transitionOrder(
            $order,
            'cancelled',
            ['pending', 'accepted'],
            'This order can no longer be cancelled.',
            'Your order was cancelled.',
            $notifications
        );

        return $this->redirectAfterFarmerTransition($order, $result, 'Order cancelled successfully.');
    }

    private function ensureFarmerOwnsOrder(Order $order): void
    {
        $user = auth()->user();

        if (
            ! $user?->isFarmer() ||
            ! $order->items()
                ->where('farmer_id', $user->id)
                ->exists()
        ) {
            abort(403);
        }
    }

    private function transitionOrder(
        Order $order,
        string $nextStatus,
        array $allowedCurrentStatuses,
        string $failureMessage,
        string $buyerMessage,
        NotificationService $notifications
    ): array {
        if (! in_array($order->status, $allowedCurrentStatuses, true)) {
            return ['ok' => false, 'message' => $failureMessage];
        }

        if ($nextStatus === 'completed' && $order->payment_method === 'gcash' && $order->payment_status !== 'paid') {
            return [
                'ok' => false,
                'message' => $order->payment_status === 'rejected'
                    ? 'Payment proof was rejected. Please wait for valid payment before completing this order.'
                    : 'GCash payment must be verified by admin before completing this order.',
            ];
        }

        $oldStatus = $order->status;
        $updates = ['status' => $nextStatus];

        if ($nextStatus === 'completed' && in_array($order->payment_method, ['cod', null], true)) {
            $updates['payment_status'] = 'paid';
        }

        $order->update($updates);
        $order->loadMissing(['consumer', 'items.product']);

        if ($oldStatus !== $order->status && $order->consumer) {
            $notifications->send(
                $order->consumer,
                'order.status_updated',
                'Order status updated',
                $buyerMessage,
                $order->status === 'cancelled' ? 'alert' : 'orders',
                route('orders.show', $order),
                ['order_id' => $order->id, 'status' => $order->status]
            );
        }

        if ($nextStatus === 'completed') {
            $this->reduceOrderStock($order, $notifications);
        }

        return ['ok' => true, 'message' => null];
    }

    private function redirectAfterFarmerTransition(Order $order, array $result, string $successMessage)
    {
        if (! $result['ok']) {
            return back()->with('error', $result['message']);
        }

        return redirect()
            ->route('orders.show', $order)
            ->with('success', $successMessage);
    }

    private function reduceOrderStock(Order $order, NotificationService $notifications): void
    {
        foreach ($order->items as $item) {
            $product = $item->product;

            if (! $product) {
                continue;
            }

            $oldQuantity = $product->quantity;
            $product->quantity = max($product->quantity - $item->quantity, 0);
            $product->save();

            $this->notifyStockStatus($product, $notifications, $oldQuantity);
        }
    }

    private function notifyStockStatus($product, NotificationService $notifications, ?int $oldQuantity = null): void
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
