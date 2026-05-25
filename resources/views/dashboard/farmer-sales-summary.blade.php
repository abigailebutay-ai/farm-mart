@extends('layouts.app')

@section('page-title', 'Sales Summary')

@section('content')
    <x-ui.page-header
        title="Sales Summary"
        subtitle="Revenue-focused summary of completed farmer orders and recent product sales."
    />

    <div class="mb-5 flex justify-end">
        <x-ui.secondary-button href="{{ route('farmer.sales-summary.print') }}">
            Print Report
        </x-ui.secondary-button>
    </div>

    <div class="grid gap-4 md:grid-cols-3">
        <x-ui.stat-card label="Total Sales" value="PHP {{ number_format($totalSales ?? 0, 2) }}" icon="money" tone="green" trend="Revenue from completed orders." />
        <x-ui.stat-card label="Completed Orders" :value="$completedOrderCount ?? 0" icon="orders" tone="green" trend="Unique completed orders containing your products." />
        <x-ui.stat-card label="Units Sold" :value="$totalSoldQuantity ?? 0" icon="products" tone="blue" trend="Total quantity sold in completed orders." />
    </div>

    <div class="mt-5 grid gap-4 xl:grid-cols-2">
        <x-ui.table-card title="Monthly Sales Summary" subtitle="Completed sales grouped by month.">
            <thead class="bg-slate-50">
                <tr class="text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                    <th class="px-5 py-3">Month</th>
                    <th class="px-5 py-3">Orders</th>
                    <th class="px-5 py-3">Units Sold</th>
                    <th class="px-5 py-3">Sales</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($monthlyRows ?? [] as $row)
                    <tr class="hover:bg-slate-50">
                        <td class="px-5 py-4 font-bold text-slate-900">{{ $row['month'] }}</td>
                        <td class="px-5 py-4 text-sm text-slate-600">{{ $row['orders'] }}</td>
                        <td class="px-5 py-4 text-sm text-slate-600">{{ $row['quantity'] }}</td>
                        <td class="px-5 py-4 text-sm font-bold text-slate-900">PHP {{ number_format($row['sales'], 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-5 py-5"><x-ui.empty-state title="No completed sales yet" message="Monthly sales appear after completed orders." icon="money" /></td></tr>
                @endforelse
            </tbody>
        </x-ui.table-card>

        <x-ui.table-card title="Recent Sales" subtitle="Latest completed order items for your products.">
            <thead class="bg-slate-50">
                <tr class="text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                    <th class="px-5 py-3">Product</th>
                    <th class="px-5 py-3">Qty</th>
                    <th class="px-5 py-3">Amount</th>
                    <th class="px-5 py-3">Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($recentSales ?? [] as $item)
                    <tr class="hover:bg-slate-50">
                        <td class="px-5 py-4 font-bold text-slate-900">{{ $item->product->name ?? 'Product' }}</td>
                        <td class="px-5 py-4 text-sm text-slate-600">{{ $item->quantity }}</td>
                        <td class="px-5 py-4 text-sm font-bold text-slate-900">PHP {{ number_format($item->subtotal, 2) }}</td>
                        <td class="px-5 py-4 text-sm text-slate-500">{{ $item->created_at->timezone(config('app.timezone'))->format('M d, Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-5 py-5"><x-ui.empty-state title="No recent sales" message="Completed sales will appear here." icon="orders" /></td></tr>
                @endforelse
            </tbody>
        </x-ui.table-card>
    </div>
@endsection
