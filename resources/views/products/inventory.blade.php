@extends('layouts.app')

@section('page-title', 'Inventory')

@section('content')
    <x-ui.page-header
        title="Inventory"
        subtitle="Check your stock and restock products when needed."
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
        <x-ui.table-card title="Stock Levels" subtitle="Add stock when products are low or out of stock. Product details stay in My Products.">
            <thead class="bg-slate-50">
                <tr class="text-left text-sm font-bold uppercase tracking-wide text-slate-500">
                    <th class="px-5 py-3">Product</th>
                    <th class="px-5 py-3">Current Stock</th>
                    <th class="px-5 py-3">Stock Status</th>
                    <th class="px-5 py-3">Add Stock Quantity</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($products ?? [] as $product)
                    <tr class="hover:bg-slate-50">
                        <td class="px-5 py-4">
                            <p class="font-bold text-slate-900">{{ $product->name }}</p>
                            <p class="text-sm text-slate-500">{{ $product->category }}</p>
                        </td>
                        <td class="px-5 py-4 font-black text-slate-900">{{ $product->quantity }} {{ $product->unit ?? 'piece' }}</td>
                        <td class="px-5 py-4">
                            <x-ui.status-badge :status="$product->quantity > 10 ? 'In Stock' : ($product->quantity > 0 ? 'Low Stock' : 'Out of Stock')" />
                        </td>
                        <td class="px-5 py-4">
                            <div>
                                <form method="POST" action="{{ route('farmer.products.restock', $product) }}" class="flex min-w-[280px] flex-wrap items-center gap-2 rounded-xl border border-emerald-100 bg-emerald-50/70 p-3 dark:border-emerald-900 dark:bg-emerald-950/30">
                                    @csrf
                                    @method('PATCH')
                                    <span class="text-sm font-bold text-emerald-900 dark:text-emerald-200">Current: {{ $product->quantity }} {{ $product->unit ?? 'piece' }}</span>
                                    <input
                                        name="quantity"
                                        type="number"
                                        min="1"
                                        placeholder="Add stock"
                                        aria-label="Quantity to add for {{ $product->name }}"
                                        class="w-32 rounded-xl border border-emerald-200 bg-white px-3 py-2 text-base text-slate-900 focus:border-emerald-600 focus:outline-none focus:ring-4 focus:ring-emerald-100 dark:border-emerald-800 dark:bg-gray-900 dark:text-white"
                                    >
                                    <span class="text-xs font-semibold text-slate-500 dark:text-gray-300">{{ $product->unit ?? 'piece' }}</span>
                                    <button type="submit" class="rounded-xl bg-emerald-700 px-4 py-2 text-base font-bold text-white hover:bg-emerald-800">Add Stock</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-5 py-5"><x-ui.empty-state title="No products yet" message="You have not added any products yet." action-url="{{ route('farmer.products.create') }}" action-label="Add Product" icon="inventory" /></td></tr>
                @endforelse
            </tbody>
        </x-ui.table-card>

        <x-ui.dashboard-card title="Inventory Guide" subtitle="Simple stock meanings.">
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
