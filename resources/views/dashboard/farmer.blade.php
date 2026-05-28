@extends('layouts.app')

@section('page-title', 'Farmer Dashboard')

@section('content')
    @php($displayName = html_entity_decode(auth()->user()->name, ENT_QUOTES | ENT_HTML5, 'UTF-8'))

    <x-ui.page-header
        :title="'Welcome back, ' . $displayName"
        subtitle="See what needs your attention today: products, pending orders, low stock, and completed sales."
        action-url="{{ route('farmer.products.create') }}"
        action-label="Add Product"
    />

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <x-ui.stat-card label="Total Products" :value="$totalProducts ?? 0" icon="products" trend="Products you are selling." />
        <x-ui.stat-card label="Pending Orders" :value="$pendingOrders ?? 0" icon="clock" tone="amber" trend="Buyer orders waiting for action." />
        <x-ui.stat-card label="Products to Restock" :value="$productsToRestockCount ?? $lowStockProductsCount ?? 0" icon="alert" tone="amber" trend="Products with 10 kg or less remaining." />
        <x-ui.stat-card label="Total Sales" value="PHP {{ number_format($totalSales ?? 0, 2) }}" icon="money" tone="green" trend="Earnings from completed orders." />
    </div>

    <div class="mt-5 space-y-6">
        <x-ui.table-card title="Recent Orders" subtitle="Check each order and choose the next action." table-class="min-w-[900px] w-full">
            <thead class="bg-slate-50">
                <tr class="text-left text-sm font-bold uppercase tracking-wide text-slate-500">
                    <th class="px-5 py-3">Order Number</th>
                    <th class="px-5 py-3">Buyer</th>
                    <th class="px-5 py-3">Items</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3">Total Amount</th>
                    <th class="px-5 py-3 min-w-36">Next Step</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($recentOrders ?? [] as $order)
                    <tr class="hover:bg-slate-50">
                        <td class="px-5 py-4 font-bold text-slate-900">#{{ $order->id }}</td>
                        <td class="px-5 py-4 text-slate-600">{{ $order->consumer->name ?? 'Buyer' }}</td>
                        <td class="px-5 py-4 text-slate-600">{{ $order->items->where('farmer_id', auth()->id())->count() }}</td>
                        <td class="px-5 py-4">
                            <x-ui.status-badge :status="$order->status" />
                            <div class="mt-2 text-xs font-semibold text-slate-500 dark:text-gray-400">
                                @if($order->payment_method === 'gcash')
                                    GCash - {{ $order->paymentStatusLabel() }}
                                @else
                                    Cash on Delivery
                                @endif
                            </div>
                        </td>
                        <td class="px-5 py-4 font-bold text-slate-900">PHP {{ number_format($order->total, 2) }}</td>
                        <td class="px-5 py-4">
                            <a href="{{ route('orders.show', $order) }}" class="inline-flex items-center justify-center whitespace-nowrap rounded-lg bg-emerald-700 px-4 py-2 text-sm font-bold text-white hover:bg-emerald-800">View Details</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-5 py-5"><x-ui.empty-state title="No orders yet" message="No orders available at the moment." icon="orders" /></td></tr>
                @endforelse
            </tbody>
        </x-ui.table-card>

        <x-ui.dashboard-card title="Quick Actions" subtitle="Start with these common tasks.">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <x-ui.quick-action-card href="{{ route('farmer.products.create') }}" title="Add Product" description="Create a new marketplace listing." icon="products" />
                <x-ui.quick-action-card href="{{ route('orders.index') }}" title="View Orders" description="Review buyer orders and fulfillment status." icon="orders" />
                <x-ui.quick-action-card href="{{ route('farmer.inventory.index') }}" title="Restock Products" description="Add stock for products that are running low." icon="inventory" />
            </div>
        </x-ui.dashboard-card>
    </div>
@endsection
