@extends('layouts.app')

@section('page-title', 'Decision Support')

@section('content')
    <x-ui.page-header
        title="Decision Support"
        subtitle="See helpful suggestions based on your products and sales."
    />

    <div class="grid gap-4 md:grid-cols-3">
        <x-ui.stat-card label="Monthly Sales" value="PHP {{ number_format($monthlySales ?? 0, 2) }}" icon="money" tone="green" trend="Completed order income for {{ now()->format('F Y') }}." />
        <x-ui.stat-card label="Products to Restock" :value="$productsToRestockCount ?? 0" icon="inventory" tone="amber" trend="Products with 10 kg or less remaining." />
        <x-ui.stat-card label="Products To Review" :value="($slowMovingProducts ?? collect())->count()" icon="chart" tone="blue" trend="Products with low sales movement." />
    </div>

    <div class="mt-5 grid gap-4 lg:grid-cols-3">
        <x-ui.dashboard-card title="Low-Stock Alerts" subtitle="Products that may affect order fulfillment soon.">
            <div class="space-y-3">
                @forelse($lowStockProducts ?? [] as $product)
                    <x-ui.alert-card title="Restock this product soon" message="{{ $product->name }} has only {{ $product->quantity }} kg remaining." tone="amber" />
                @empty
                    <x-ui.empty-state title="No low-stock products" message="Your current inventory is above the low-stock threshold." icon="check" />
                @endforelse

                @foreach($outOfStockProducts ?? [] as $product)
                    <x-ui.alert-card title="Out of stock" message="{{ $product->name }} needs stock before buyers can order it." tone="red" />
                @endforeach
            </div>
        </x-ui.dashboard-card>

        <x-ui.dashboard-card title="Best-Selling Products" subtitle="Listings with stronger sales movement.">
            <div class="space-y-3">
                @forelse($bestSellingProducts ?? [] as $product)
                    <div class="rounded-2xl border border-slate-100 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="font-bold text-slate-900">{{ $product->name }}</p>
                                <p class="text-sm text-slate-500">{{ (int) $product->sold_quantity }} kg sold</p>
                            </div>
                            <x-ui.status-badge status="High" />
                        </div>
                        <p class="mt-3 text-sm text-slate-500">This product sells well this month. Keep stock available and monitor demand.</p>
                    </div>
                @empty
                    <x-ui.empty-state title="No sales signals yet" message="Best-selling products appear after completed buyer orders." icon="star" />
                @endforelse
            </div>
        </x-ui.dashboard-card>

        <x-ui.dashboard-card title="Slow-Moving Products" subtitle="Listings with low or no buyer movement.">
            <div class="space-y-3">
                @forelse($slowMovingProducts ?? [] as $product)
                    <div class="rounded-2xl border border-slate-100 p-4">
                        <p class="font-bold text-slate-900">{{ $product->name }}</p>
                        <p class="text-sm text-slate-500">{{ (int) ($product->sold_quantity ?? 0) }} kg sold</p>
                        <p class="mt-3 text-sm text-slate-500">Consider lowering stock for slow-moving products or improving the listing photo, price, or description.</p>
                    </div>
                @empty
                    <x-ui.empty-state title="No slow-moving signals" message="Slow-moving products will appear as sales data grows." icon="check" />
                @endforelse
            </div>
        </x-ui.dashboard-card>
    </div>

    <div class="mt-5 grid gap-4 xl:grid-cols-[1.2fr_0.8fr]">
        <x-ui.table-card title="Product Performance Insights" subtitle="Sales amount, sold quantity, stock, and demand level per product.">
            <thead class="bg-slate-50">
                <tr class="text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                    <th class="px-5 py-3">Product</th>
                    <th class="px-5 py-3">Stock</th>
                    <th class="px-5 py-3">Kg Sold</th>
                    <th class="px-5 py-3">Sales</th>
                    <th class="px-5 py-3">Recommendation</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($productPerformance ?? [] as $product)
                    @php
                        $sold = (int) ($product->sold_quantity ?? 0);
                        $recommendation = $product->quantity <= 10
                            ? 'Restock this product soon'
                            : ($sold >= 5 ? 'This product sells well this month' : 'Review listing performance');
                    @endphp
                    <tr class="hover:bg-slate-50">
                        <td class="px-5 py-4">
                            <p class="font-bold text-slate-900">{{ $product->name }}</p>
                            <p class="text-sm text-slate-500">{{ $product->category }}</p>
                        </td>
                        <td class="px-5 py-4"><x-ui.status-badge :status="$product->quantity > 10 ? 'In Stock' : ($product->quantity > 0 ? 'Low Stock' : 'Out of Stock')" /></td>
                        <td class="px-5 py-4 text-sm text-slate-600">{{ $sold }} kg</td>
                        <td class="px-5 py-4 text-sm font-bold text-slate-900">PHP {{ number_format($product->sales_amount ?? 0, 2) }}</td>
                        <td class="px-5 py-4 text-sm text-slate-600">{{ $recommendation }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-5 py-5"><x-ui.empty-state title="No product performance yet" message="Performance insights appear after buyer activity." icon="chart" /></td></tr>
                @endforelse
            </tbody>
        </x-ui.table-card>

        <x-ui.dashboard-card title="Recommendation Messages" subtitle="Simple signals for business decisions.">
            <div class="space-y-3">
                @if(($lowStockProducts ?? collect())->count() > 0)
                    <x-ui.alert-card title="Restock this product soon" message="{{ $lowStockProducts->first()->name }} is close to running out." tone="amber" />
                @endif
                @if($bestSellingProduct ?? null)
                    <x-ui.alert-card title="This product sells well this month" message="{{ $bestSellingProduct->name }} is your strongest sales signal." tone="green" />
                @endif
                @if(($slowMovingProducts ?? collect())->count() > 0)
                    <x-ui.alert-card title="Consider lowering stock" message="Review slow-moving products before increasing inventory." tone="gray" />
                @endif
                @if(($lowStockProducts ?? collect())->isEmpty() && empty($bestSellingProduct) && ($slowMovingProducts ?? collect())->isEmpty())
                    <x-ui.empty-state title="No recommendations yet" message="Recommendations will appear after product and order activity grows." icon="chart" />
                @endif
            </div>
        </x-ui.dashboard-card>
    </div>
@endsection
