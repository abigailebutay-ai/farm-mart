@extends('layouts.app')

@section('page-title', 'Inventory')

@section('content')
    <x-ui.page-header
        title="Inventory"
        subtitle="Manage stock quantities, low-stock status, and availability without changing product descriptions, images, or pricing."
    />

    <div class="mb-5 flex justify-end">
        <x-ui.secondary-button href="{{ route('farmer.inventory.print') }}">
            Print Inventory
        </x-ui.secondary-button>
    </div>

    <div class="grid gap-4 md:grid-cols-3">
        <x-ui.stat-card label="Total Stock Quantity" :value="$totalInventoryQuantity ?? 0" icon="inventory" tone="blue" trend="Combined stock across all listings." />
        <x-ui.stat-card label="Low-Stock Products" :value="$lowStockCount ?? 0" icon="alert" tone="amber" trend="Products with 1 to 10 units remaining." />
        <x-ui.stat-card label="Out-of-Stock Products" :value="$outOfStockCount ?? 0" icon="x" tone="red" trend="Products currently unavailable for buyers." />
    </div>

    <div class="mt-5 grid gap-4 xl:grid-cols-[1.25fr_0.75fr]">
        <x-ui.table-card title="Stock Levels" subtitle="Update current quantity only. Product details stay in My Products.">
            <thead class="bg-slate-50">
                <tr class="text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                    <th class="px-5 py-3">Product</th>
                    <th class="px-5 py-3">Current Stock</th>
                    <th class="px-5 py-3">Low-Stock Threshold</th>
                    <th class="px-5 py-3">Stock Status</th>
                    <th class="px-5 py-3">Stock Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($products ?? [] as $product)
                    <tr class="hover:bg-slate-50">
                        <td class="px-5 py-4">
                            <p class="font-bold text-slate-900">{{ $product->name }}</p>
                            <p class="text-sm text-slate-500">{{ $product->category }}</p>
                        </td>
                        <td class="px-5 py-4 text-sm font-black text-slate-900">{{ $product->quantity }} {{ $product->unit ?? 'piece' }}</td>
                        <td class="px-5 py-4 text-sm text-slate-600">10 units</td>
                        <td class="px-5 py-4">
                            <x-ui.status-badge :status="$product->quantity > 10 ? 'In Stock' : ($product->quantity > 0 ? 'Low Stock' : 'Out of Stock')" />
                        </td>
                        <td class="px-5 py-4">
                            <form method="POST" action="{{ route('farmer.inventory.update', $product) }}" class="flex min-w-[300px] flex-wrap gap-2">
                                @csrf
                                @method('PATCH')
                                <select name="mode" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 focus:border-emerald-600 focus:outline-none focus:ring-4 focus:ring-emerald-100">
                                    <option value="set">Set stock</option>
                                    <option value="add">Add stock</option>
                                    <option value="reduce">Reduce stock</option>
                                </select>
                                <input name="quantity" type="number" min="0" value="{{ $product->quantity }}" class="w-24 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 focus:border-emerald-600 focus:outline-none focus:ring-4 focus:ring-emerald-100">
                                <button type="submit" class="rounded-xl bg-emerald-700 px-3 py-2 text-sm font-bold text-white hover:bg-emerald-800">Update</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-5 py-5"><x-ui.empty-state title="No inventory yet" message="Add a product before managing stock levels." icon="inventory" /></td></tr>
                @endforelse
            </tbody>
        </x-ui.table-card>

        <x-ui.dashboard-card title="Inventory Guidance" subtitle="Stock rules used across the farmer workflow.">
            <div class="space-y-3">
                <x-ui.alert-card title="Add stock" message="Increase the quantity when new harvest or supply is available." tone="green" />
                <x-ui.alert-card title="Reduce stock" message="Lower the quantity when stock is damaged, reserved, or no longer available." tone="gray" />
                <x-ui.alert-card title="Low-stock threshold" message="Products with 10 units or fewer are marked Low Stock." tone="amber" />
                <x-ui.alert-card title="Out of stock" message="Products with 0 quantity are unavailable until stock is updated." tone="red" />
            </div>
        </x-ui.dashboard-card>
    </div>

    <x-ui.table-card class="mt-5" title="Recent Inventory Activity" subtitle="Most recently updated stock records.">
        <thead class="bg-slate-50">
            <tr class="text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                <th class="px-5 py-3">Product</th>
                <th class="px-5 py-3">Stock</th>
                <th class="px-5 py-3">Status</th>
                <th class="px-5 py-3">Last Updated</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($recentStockActivity ?? [] as $product)
                <tr class="hover:bg-slate-50">
                    <td class="px-5 py-4 font-bold text-slate-900">{{ $product->name }}</td>
                    <td class="px-5 py-4 text-sm text-slate-600">{{ $product->quantity }} {{ $product->unit ?? 'piece' }}</td>
                    <td class="px-5 py-4"><x-ui.status-badge :status="$product->quantity > 10 ? 'In Stock' : ($product->quantity > 0 ? 'Low Stock' : 'Out of Stock')" /></td>
                    <td class="px-5 py-4 text-sm text-slate-500">{{ $product->updated_at->timezone(config('app.timezone'))->format('M d, Y h:i A') }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-5 py-5"><x-ui.empty-state title="No stock activity yet" message="Inventory updates will appear here." icon="inventory" /></td></tr>
            @endforelse
        </tbody>
    </x-ui.table-card>

    <div class="mt-8">
        {{ $products->links() }}
    </div>
@endsection
