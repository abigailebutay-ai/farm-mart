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
        subtitle="{{ $isBuyerOrders ? 'Track your orders and view receipts.' : 'View buyer orders and update their status.' }}"
    />

    @if(auth()->user()->isFarmer())
        <x-ui.dashboard-card class="mb-5" title="Status Guide" subtitle="Use this guide to understand what each order status means.">
            <div class="grid gap-3 md:grid-cols-5">
                @foreach([
                    ['Pending', 'Waiting for farmer response'],
                    ['Accepted', 'Order accepted'],
                    ['Preparing', 'Products are being prepared'],
                    ['Completed', 'Order finished'],
                    ['Cancelled', 'Order cancelled'],
                ] as [$label, $copy])
                    <div class="rounded-xl border border-slate-100 p-3 dark:border-gray-800">
                        <x-ui.status-badge :status="$label" />
                        <p class="mt-2 text-sm leading-relaxed text-slate-500 dark:text-gray-400">{{ $copy }}</p>
                    </div>
                @endforeach
            </div>
        </x-ui.dashboard-card>
    @elseif($isBuyerOrders)
        <x-ui.dashboard-card class="mb-5" title="Order Status Guide" subtitle="Simple meanings for your order status.">
            <div class="grid gap-3 md:grid-cols-5">
                @foreach([
                    ['Pending', 'Your order has been placed.'],
                    ['Accepted', 'Farmer accepted your order.'],
                    ['Preparing', 'Farmer is preparing your order.'],
                    ['Completed', 'Order completed.'],
                    ['Cancelled', 'Order cancelled.'],
                ] as [$label, $copy])
                    <div class="rounded-xl border border-slate-100 p-3 dark:border-gray-800">
                        <x-ui.status-badge :status="$label" />
                        <p class="mt-2 text-sm leading-relaxed text-slate-500 dark:text-gray-400">{{ $copy }}</p>
                    </div>
                @endforeach
            </div>
        </x-ui.dashboard-card>
    @endif

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
                            <th class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wide text-gray-500">Order Number</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wide text-gray-500">
                                @if(auth()->user()->isFarmer())
                                    Customer
                                @else
                                    Items
                                @endif
                            </th>
                            <th class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wide text-gray-500">Total Amount</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wide text-gray-500">Current Status</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wide text-gray-500">Order Date</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wide text-gray-500">Next Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($orders as $order)
                            @php
                                $farmerItems = auth()->user()->isFarmer()
                                    ? $order->items->where('farmer_id', auth()->id())
                                    : collect();
                                $farmerSubtotal = $farmerItems->sum('subtotal');
                            @endphp
                            <tr>
                                <td class="px-6 py-4 font-semibold text-gray-800">#{{ $order->id }}</td>
                                <td class="px-6 py-4 text-gray-600">
                                    @if(auth()->user()->isFarmer())
                                        <div class="font-semibold text-gray-800 dark:text-gray-100">{{ $order->consumer->name ?? 'Buyer' }}</div>
                                        <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                            @foreach($farmerItems as $item)
                                                <div>{{ optional($item->product)->name ?? 'Product unavailable' }} x {{ $item->quantity }} {{ optional($item->product)->unit ?? 'piece' }}</div>
                                            @endforeach
                                        </div>
                                    @else
                                        {{ $order->items_count ?? $order->items()->count() }} items
                                    @endif
                                </td>
                                <td class="px-6 py-4 font-semibold text-gray-800">PHP {{ number_format(auth()->user()->isFarmer() ? $farmerSubtotal : $order->total, 2) }}</td>
                                <td class="px-6 py-4">
                                    <x-ui.status-badge :status="$order->status" />
                                    <div class="mt-2 text-xs font-semibold text-gray-500 dark:text-gray-400">
                                        @if($order->payment_method === 'gcash')
                                            GCash - {{ $order->paymentStatusLabel() }}
                                        @else
                                            Cash on Delivery - {{ $order->paymentStatusLabel() }}
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-500">{{ $order->created_at->timezone(config('app.timezone'))->format('M d, Y h:i A') }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-2">
                                        <a href="{{ route('orders.show', $order) }}" class="rounded-lg border border-emerald-200 px-4 py-2 text-sm font-bold text-emerald-800 hover:bg-emerald-50 dark:hover:bg-emerald-900/40">{{ $isBuyerOrders ? 'View Tracking' : 'View Details' }}</a>
                                        @if($isBuyerOrders)
                                            @if($order->canBeCancelledByConsumer())
                                                <form method="POST" action="{{ route('consumer.orders.cancel', $order) }}" onsubmit="return confirm('Are you sure you want to cancel this order?')">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-bold text-white hover:bg-red-700">
                                                        Cancel Order
                                                    </button>
                                                </form>
                                            @elseif($order->status !== 'completed')
                                                <span class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-500">{{ $order->consumerCancellationMessage() }}</span>
                                            @endif
                                            @if($order->status === 'completed')
                                                <a href="{{ route('consumer.orders.receipt', $order) }}" class="rounded-lg bg-emerald-700 px-4 py-2 text-sm font-bold text-white hover:bg-emerald-800">Receipt</a>
                                            @else
                                                <span class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-500">Receipt after completion</span>
                                            @endif
                                        @elseif(auth()->user()->isFarmer())
                                            @if($order->status === 'pending')
                                                <form method="POST" action="{{ route('farmer.orders.accept', $order) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="rounded-lg bg-emerald-700 px-4 py-2 text-sm font-bold text-white hover:bg-emerald-800">Accept Order</button>
                                                </form>
                                                <form method="POST" action="{{ route('farmer.orders.cancel', $order) }}" onsubmit="return confirm('Are you sure you want to cancel this order?')">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-bold text-white hover:bg-red-700">Cancel Order</button>
                                                </form>
                                            @elseif($order->status === 'accepted')
                                                <form method="POST" action="{{ route('farmer.orders.preparing', $order) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-bold text-white hover:bg-indigo-700">Mark as Preparing</button>
                                                </form>
                                                <form method="POST" action="{{ route('farmer.orders.cancel', $order) }}" onsubmit="return confirm('Are you sure you want to cancel this order?')">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-bold text-white hover:bg-red-700">Cancel Order</button>
                                                </form>
                                            @elseif($order->status === 'preparing')
                                                @if($order->payment_method === 'gcash' && $order->payment_status !== 'paid')
                                                    <span class="rounded-lg border border-amber-200 px-4 py-2 text-sm font-semibold text-amber-700 dark:border-amber-800 dark:text-amber-200">
                                                        Payment must be verified before completion
                                                    </span>
                                                @else
                                                    <form method="POST" action="{{ route('farmer.orders.complete', $order) }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="rounded-lg bg-emerald-700 px-4 py-2 text-sm font-bold text-white hover:bg-emerald-800">Mark as Completed</button>
                                                    </form>
                                                @endif
                                            @elseif($order->status === 'completed')
                                                <span class="rounded-lg bg-emerald-100 px-4 py-2 text-sm font-bold text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-200">Completed</span>
                                            @elseif($order->status === 'cancelled')
                                                <span class="rounded-lg bg-red-100 px-4 py-2 text-sm font-bold text-red-800 dark:bg-red-900/40 dark:text-red-200">Cancelled</span>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @if($isBuyerOrders)
                                <tr>
                                    <td colspan="6" class="px-6 pb-5">
                                        <x-ui.order-progress :order="$order" />
                                    </td>
                                </tr>
                            @endif
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
            title="No orders yet"
            message="{{ auth()->user()->isFarmer() ? 'Customer orders will appear here when buyers purchase your products.' : ($currentStatus === 'all' ? 'Start browsing the marketplace to place your first order.' : 'No orders match this status filter yet.') }}"
            action-url="{{ auth()->user()->isConsumer() ? route('consumer.marketplace') : null }}"
            action-label="{{ auth()->user()->isConsumer() ? 'Browse Marketplace' : null }}"
        />
    @endif
@endsection
