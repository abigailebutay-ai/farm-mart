@extends('layouts.app')

@section('page-title', 'Consumer Dashboard')

@section('content')
    <x-ui.page-header
        title="Welcome to SariwaLink, {{ auth()->user()->name }}"
        subtitle="Browse products, check your cart, and track your orders in one place."
        action-url="{{ route('consumer.marketplace') }}"
        action-label="Browse Marketplace"
    />

    <div class="grid gap-4 md:grid-cols-3">
        <x-ui.stat-card label="Available Products" :value="$availableProducts ?? 0" icon="products" trend="Products you can buy today." />
        <x-ui.stat-card label="Cart Items" :value="$cartItemsCount ?? 0" icon="cart" tone="amber" trend="Items waiting for checkout." />
        <x-ui.stat-card label="Recent Orders" :value="($recentOrders ?? collect())->count()" icon="orders" tone="green" trend="Orders you can track." />
    </div>

    @if(($completedOrdersForFeedback ?? 0) > 0)
        <div class="mt-5 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-amber-900">
            <div class="flex gap-3">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-amber-700"><x-ui.icon name="star" /></span>
                <div>
                    <p class="font-bold">Feedback Reminder</p>
                    <p class="mt-1 text-sm leading-relaxed">Help farmers improve by rating your completed orders.</p>
                </div>
            </div>
        </div>
    @endif

    <div class="mt-5 grid gap-4 xl:grid-cols-[1.15fr_0.85fr]">
        <x-ui.dashboard-card class="buyer-card" title="Order Tracking" subtitle="Your latest orders and their current status.">
            <div class="space-y-3">
                @forelse($recentOrders ?? [] as $order)
                    <div class="order-card rounded-2xl border p-4">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p class="buyer-text text-lg font-bold">Order #{{ $order->id }}</p>
                                <p class="buyer-muted mt-1">{{ $order->created_at->timezone(config('app.timezone'))->format('M d, Y') }} - PHP {{ number_format($order->total, 2) }}</p>
                                <p class="buyer-muted mt-1">{{ $order->paymentMethodLabel() }}</p>
                            </div>
                            <div class="flex flex-wrap items-center gap-2">
                                <x-ui.status-badge :status="$order->status" />
                                <a href="{{ route('orders.show', $order) }}" class="rounded-lg bg-emerald-700 px-4 py-2 text-sm font-bold text-white hover:bg-emerald-800">View Tracking</a>
                                @if($order->status === 'completed')
                                    <a href="{{ route('consumer.orders.receipt', $order) }}" class="rounded-lg border border-emerald-700 px-4 py-2 text-sm font-bold text-emerald-700 hover:bg-emerald-50 dark:text-emerald-300 dark:hover:bg-emerald-900/40">Receipt</a>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <x-ui.empty-state title="No orders yet" message="Your order tracking details will appear after checkout." action-url="{{ route('consumer.marketplace') }}" action-label="Browse Marketplace" icon="orders" />
                @endforelse
            </div>
        </x-ui.dashboard-card>

        <x-ui.dashboard-card title="Quick Actions" subtitle="Common things you may want to do.">
            <div class="grid gap-3">
                <x-ui.quick-action-card href="{{ route('consumer.marketplace') }}" title="Browse Marketplace" description="Find fresh local products." icon="products" />
                <x-ui.quick-action-card href="{{ route('cart.index') }}" title="View Cart" description="Review items before checkout." icon="cart" />
                <x-ui.quick-action-card href="{{ route('orders.index') }}" title="My Orders" description="Track orders and view receipts." icon="orders" />
            </div>
        </x-ui.dashboard-card>
    </div>
@endsection
