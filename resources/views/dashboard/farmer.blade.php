@extends('layouts.app')

@section('page-title', 'Farmer Dashboard')

@section('content')
    <x-ui.page-header
        title="Welcome back, {{ auth()->user()->name }}"
        subtitle="Quick overview of your products, inventory, orders, and sales. Use Decision Support for deeper analysis and recommendations."
        action-url="{{ route('farmer.products.create') }}"
        action-label="Add Product"
    />

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <x-ui.stat-card label="Total Products" :value="$totalProducts ?? 0" icon="products" trend="All marketplace listings under your account." />
        <x-ui.stat-card label="Total Inventory" :value="$totalInventoryQuantity ?? 0" icon="inventory" tone="blue" trend="Combined available quantity across listings." />
        <x-ui.stat-card label="Low-Stock Products" :value="$lowStockProductsCount ?? 0" icon="alert" tone="amber" trend="Products with 1 to 10 units remaining." />
        <x-ui.stat-card label="Pending Orders" :value="$pendingOrders ?? 0" icon="clock" tone="amber" trend="Buyer orders waiting for action." />
        <x-ui.stat-card label="Completed Orders" :value="$completedOrders ?? 0" icon="orders" tone="green" trend="Fulfilled farmer-to-buyer transactions." />
        <x-ui.stat-card label="Total Sales" value="PHP {{ number_format($totalSales ?? 0, 2) }}" icon="money" tone="green" trend="Revenue from completed orders." />
        <x-ui.stat-card label="Monthly Sales" value="PHP {{ number_format($monthlySales ?? 0, 2) }}" icon="chart" tone="blue" trend="Income summary for {{ now()->format('F Y') }}." />
        <x-ui.stat-card label="Recent Products" :value="($recentProducts ?? collect())->count()" icon="check" trend="Latest listings or product updates." />
    </div>

    <div class="mt-5 grid gap-4 xl:grid-cols-[1.2fr_0.8fr]">
        <x-ui.table-card title="Recent Orders" subtitle="Latest order fulfillment activity from buyers.">
            <thead class="bg-slate-50">
                <tr class="text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                    <th class="px-5 py-3">Order</th>
                    <th class="px-5 py-3">Buyer</th>
                    <th class="px-5 py-3">Items</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3">Amount</th>
                    <th class="px-5 py-3">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($recentOrders ?? [] as $order)
                    <tr class="hover:bg-slate-50">
                        <td class="px-5 py-4 text-sm font-bold text-slate-900">#{{ $order->id }}</td>
                        <td class="px-5 py-4 text-sm text-slate-600">{{ $order->consumer->name ?? 'Buyer' }}</td>
                        <td class="px-5 py-4 text-sm text-slate-600">{{ $order->items->where('farmer_id', auth()->id())->count() }}</td>
                        <td class="px-5 py-4"><x-ui.status-badge :status="$order->status" /></td>
                        <td class="px-5 py-4 text-sm font-bold text-slate-900">PHP {{ number_format($order->total, 2) }}</td>
                        <td class="px-5 py-4"><a href="{{ route('orders.show', $order) }}" class="rounded-lg bg-emerald-50 px-3 py-1.5 text-xs font-bold text-emerald-800">View</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-5 py-5"><x-ui.empty-state title="No orders yet" message="New buyer orders will appear here." icon="orders" /></td></tr>
                @endforelse
            </tbody>
        </x-ui.table-card>

        <x-ui.dashboard-card title="Quick Actions" subtitle="Common farmer workflows.">
            <div class="grid gap-3">
                <x-ui.quick-action-card href="{{ route('farmer.products.create') }}" title="Add Product" description="Create a new marketplace listing." icon="products" />
                <x-ui.quick-action-card href="{{ route('orders.index') }}" title="View Orders" description="Review buyer orders and fulfillment status." icon="orders" />
                <x-ui.quick-action-card href="{{ route('farmer.inventory.index') }}" title="Manage Inventory" description="Update stock quantities and availability." icon="inventory" />
                <x-ui.quick-action-card href="{{ route('farmer.decision-support') }}" title="Decision Support" description="Open insights and restock recommendations." icon="chart" />
            </div>
        </x-ui.dashboard-card>
    </div>

    <div class="mt-5 grid gap-4 xl:grid-cols-2">
        <x-ui.table-card title="Recent Product Activity" subtitle="Latest product listings and product record updates.">
            <thead class="bg-slate-50">
                <tr class="text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                    <th class="px-5 py-3">Product</th>
                    <th class="px-5 py-3">Category</th>
                    <th class="px-5 py-3">Price</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3">Updated</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($recentProducts ?? [] as $product)
                    <tr class="hover:bg-slate-50">
                        <td class="px-5 py-4 font-bold text-slate-900">{{ $product->name }}</td>
                        <td class="px-5 py-4 text-sm text-slate-600">{{ $product->category }}</td>
                        <td class="px-5 py-4 text-sm font-bold text-slate-900">PHP {{ number_format($product->price, 2) }} / {{ $product->unit ?? 'piece' }}</td>
                        <td class="px-5 py-4"><x-ui.status-badge :status="$product->quantity > 0 ? 'Active' : 'Out of Stock'" /></td>
                        <td class="px-5 py-4 text-sm text-slate-500">{{ $product->updated_at->format('M d, Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-5 py-5"><x-ui.empty-state title="No products yet" message="Add your first product to see activity here." icon="products" /></td></tr>
                @endforelse
            </tbody>
        </x-ui.table-card>

        <x-ui.dashboard-card title="Overview Notes" subtitle="Snapshot reminders for day-to-day operations.">
            <div class="space-y-3">
                <x-ui.alert-card title="Dashboard is for quick review" message="Use this page to check totals, recent orders, product activity, and common actions." tone="green" />
                <x-ui.alert-card title="Use Decision Support for analysis" message="Restock advice, product performance, and slow-moving product signals are now separated into Decision Support." tone="gray" />
                <x-ui.alert-card title="Use Inventory for stock changes" message="Quantity updates and stock status management are handled in the Inventory page." tone="amber" />
            </div>
        </x-ui.dashboard-card>
    </div>
@endsection
