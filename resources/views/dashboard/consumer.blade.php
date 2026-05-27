@extends('layouts.app')

@section('page-title', 'Buyer Dashboard')

@section('content')
    <x-ui.page-header
        title="Welcome to SariwaLink, {{ auth()->user()->name }}"
        subtitle="Browse fresh local products, manage your cart, and track farmer-to-buyer orders from checkout to completion."
        action-url="{{ route('consumer.marketplace') }}"
        action-label="Browse Marketplace"
    />

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
        <x-ui.stat-card label="Available Products" :value="$availableProducts ?? 0" icon="products" trend="Fresh listings ready for buyers." />
        <x-ui.stat-card label="Cart Items" :value="$cartItemsCount ?? 0" icon="cart" tone="amber" trend="Items ready for checkout." />
        <x-ui.stat-card label="Pending Orders" :value="$pendingOrders ?? 0" icon="clock" tone="amber" trend="Orders waiting for confirmation." />
        <x-ui.stat-card label="Completed Orders" :value="$completedOrders ?? 0" icon="check" tone="green" trend="Delivered or fulfilled orders." />
        <x-ui.stat-card label="Total Purchases" :value="$totalPurchases ?? 0" icon="orders" tone="blue" trend="All buyer order records." />
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

    <div class="mt-5 grid gap-4 xl:grid-cols-[1.25fr_0.75fr]">
        <x-ui.dashboard-card title="Recommended Products" subtitle="Fresh agricultural products available from local farmers.">
            @if(($recommendedProducts ?? collect())->count() > 0)
                <div class="grid gap-4 md:grid-cols-2">
                    @foreach($recommendedProducts as $product)
                        <x-ui.product-card :product="$product" compact />
                    @endforeach
                </div>
            @else
                <x-ui.empty-state title="No recommended products" message="Recommended products will appear once farmers list available inventory." icon="products" />
            @endif
        </x-ui.dashboard-card>

        <x-ui.dashboard-card title="Quick Actions" subtitle="Buyer shortcuts for common marketplace tasks.">
            <div class="grid gap-3">
                <x-ui.quick-action-card href="{{ route('consumer.marketplace') }}" title="Browse Marketplace" description="Find fresh local products." icon="products" />
                <x-ui.quick-action-card href="{{ route('cart.index') }}" title="View Cart" description="Review items before checkout." icon="cart" />
                <x-ui.quick-action-card href="{{ route('orders.index') }}" title="Track Orders" description="See pending and confirmed orders." icon="orders" />
                <x-ui.quick-action-card href="{{ route('orders.index', ['status' => 'completed']) }}" title="Completed Orders" description="Review completed purchases and receipts." icon="check" />
                <x-ui.quick-action-card href="{{ route('consumer.feedback') }}" title="Give Feedback" description="Share your SariwaLink experience." icon="star" />
            </div>
        </x-ui.dashboard-card>
    </div>

    <div class="mt-5 grid gap-4 xl:grid-cols-[1.1fr_0.9fr]">
        <x-ui.dashboard-card class="buyer-card" title="Order Tracking" subtitle="Recent order status from pending to completion.">
            <div class="space-y-3">
                @forelse($recentOrders ?? [] as $order)
                    <div class="order-card rounded-2xl border p-4">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p class="buyer-text font-bold">Order #{{ $order->id }}</p>
                                <p class="buyer-muted text-sm">{{ $order->items->count() }} items - PHP {{ number_format($order->total, 2) }}</p>
                            </div>
                            <div class="flex flex-wrap items-center gap-2">
                                <x-ui.status-badge :status="$order->status" />
                                <a href="{{ route('orders.show', $order) }}" class="rounded-lg bg-emerald-50 px-3 py-1.5 text-xs font-bold text-emerald-800">View</a>
                                @if($order->status === 'completed')
                                    <a href="{{ route('consumer.orders.receipt', $order) }}" class="rounded-lg bg-emerald-700 px-3 py-1.5 text-xs font-bold text-white hover:bg-emerald-800">Receipt</a>
                                @endif
                            </div>
                        </div>
                        <div class="mt-4 grid grid-cols-4 gap-2 text-xs font-semibold text-slate-400">
                            @foreach(['Pending', 'Accepted', 'Preparing', 'Completed'] as $step)
                                <div class="rounded-full px-2 py-1 text-center {{ strtolower($order->status) === strtolower($step) ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-200' : 'buyer-card buyer-muted' }}">{{ $step }}</div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <x-ui.empty-state title="No orders yet" message="Your order tracking details will appear after checkout." action-url="{{ route('consumer.marketplace') }}" action-label="Browse Products" icon="orders" />
                @endforelse
            </div>
        </x-ui.dashboard-card>

        <x-ui.table-card class="receipt-card" title="Recent Completed Orders" subtitle="Completed purchases with receipt access.">
            <thead>
                <tr class="text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                    <th class="px-5 py-3">Order</th>
                    <th class="px-5 py-3">Date</th>
                    <th class="px-5 py-3">Amount</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3">Receipt</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($recentCompletedOrders ?? [] as $order)
                    <tr>
                        <td class="px-5 py-4 text-sm font-bold">#{{ $order->id }}</td>
                        <td class="purchase-date px-5 py-4 text-sm">{{ optional($order->created_at)->timezone(config('app.timezone'))->format('M d, Y') }}</td>
                        <td class="px-5 py-4 text-sm font-bold">PHP {{ number_format($order->total, 2) }}</td>
                        <td class="px-5 py-4"><x-ui.status-badge :status="$order->status" /></td>
                        <td class="px-5 py-4">
                            <div class="flex flex-wrap gap-2">
                                <a href="{{ route('orders.show', $order) }}" class="rounded-lg border border-emerald-200 px-3 py-1.5 text-xs font-bold text-emerald-800 hover:bg-emerald-50 dark:hover:bg-emerald-900/40">View</a>
                                <a href="{{ route('consumer.orders.receipt', $order) }}" class="rounded-lg bg-emerald-700 px-3 py-1.5 text-xs font-bold text-white hover:bg-emerald-800">Receipt</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-5 py-5"><x-ui.empty-state title="No purchase history" message="Completed purchases will appear here." icon="orders" /></td></tr>
                @endforelse
            </tbody>
        </x-ui.table-card>

        <div class="xl:col-start-2">
            <x-ui.secondary-button href="{{ route('orders.index', ['status' => 'completed']) }}" class="w-full">View All Completed Orders</x-ui.secondary-button>
        </div>
    </div>
@endsection
