@extends('layouts.app')

@section('page-title', 'Orders')

@section('content')
    @php
        $isBuyerOrders = auth()->user()->isConsumer();
        $currentStatus = $status ?? 'all';
        $statusLabels = [
            'all' => 'All',
            'pending' => 'Pending',
            'accepted' => 'Accepted',
            'preparing' => 'Preparing',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ];
    @endphp

    <x-ui.page-header
        title="{{ $isBuyerOrders ? 'My Orders' : 'Order Fulfillment' }}"
        subtitle="{{ $isBuyerOrders ? 'Track every order status, review completed purchases, and open printable receipts.' : 'Track farmer-to-buyer transactions and monitor each order status clearly.' }}"
    />

    @if(auth()->user()->isAdmin())
        <div class="mb-5 flex justify-end">
            <x-ui.secondary-button href="{{ route('admin.orders.print') }}">
                Print Orders Report
            </x-ui.secondary-button>
        </div>
    @endif

    @if($isBuyerOrders)
        <div class="mb-5 flex flex-wrap gap-2">
            @foreach($statusLabels as $value => $label)
                <a
                    href="{{ route('orders.index', $value === 'all' ? [] : ['status' => $value]) }}"
                    class="rounded-xl border px-3 py-2 text-sm font-bold transition {{ $currentStatus === $value ? 'border-emerald-700 bg-emerald-700 text-white' : 'border-slate-200 bg-white text-slate-600 hover:bg-emerald-50 hover:text-emerald-800 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800' }}"
                >
                    {{ $label }}
                </a>
            @endforeach
        </div>
    @endif

    @if($orders->count() > 0)
        <div class="{{ $isBuyerOrders ? 'receipt-card' : 'bg-white dark:bg-gray-800 ring-gray-100' }} overflow-hidden rounded-xl shadow-sm ring-1">
            <div class="overflow-x-auto">
                <table class="{{ $isBuyerOrders ? 'buyer-table' : '' }} w-full">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Order ID</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                                @if(auth()->user()->isFarmer())
                                    Customer
                                @else
                                    Items
                                @endif
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($orders as $order)
                            <tr>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-800">#{{ $order->id }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    @if(auth()->user()->isFarmer())
                                        {{ $order->consumer->name }}
                                    @else
                                        {{ $order->items_count ?? $order->items()->count() }} items
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-800">PHP {{ number_format($order->total, 2) }}</td>
                                <td class="px-6 py-4 text-sm">
                                    <x-ui.status-badge :status="$order->status" />
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $order->created_at->timezone(config('app.timezone'))->format('M d, Y h:i A') }}</td>
                                <td class="px-6 py-4 text-sm">
                                    <div class="flex flex-wrap gap-2">
                                        <a href="{{ route('orders.show', $order) }}" class="rounded-lg border border-emerald-200 px-3 py-1.5 text-xs font-bold text-emerald-800 hover:bg-emerald-50 dark:hover:bg-emerald-900/40">View</a>
                                        @if($isBuyerOrders)
                                            @if($order->status === 'completed')
                                                <a href="{{ route('consumer.orders.receipt', $order) }}" class="rounded-lg bg-emerald-700 px-3 py-1.5 text-xs font-bold text-white hover:bg-emerald-800">Receipt</a>
                                            @else
                                                <span class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-500">Receipt after completion</span>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-8">
            {{ $orders->links() }}
        </div>
    @else
        <x-ui.empty-state
            title="{{ auth()->user()->isFarmer() ? 'No orders yet' : 'No purchases yet' }}"
            message="{{ auth()->user()->isFarmer() ? 'Customer orders will appear here when buyers purchase your products.' : ($currentStatus === 'all' ? 'Start browsing the marketplace to place your first order.' : 'No orders match this status filter yet.') }}"
            action-url="{{ auth()->user()->isConsumer() ? route('consumer.marketplace') : null }}"
            action-label="{{ auth()->user()->isConsumer() ? 'Start Shopping' : null }}"
        />
    @endif
@endsection
