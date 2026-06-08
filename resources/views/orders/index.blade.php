@extends('layouts.app')

@section('page-title', 'Orders')

@section('content')
    @php
        $isBuyerOrders = auth()->user()->isConsumer();
        $isAdminOrders = auth()->user()->isAdmin();
        $currentStatus = $status ?? 'all';
        $statusLabels = [
            'all' => 'All',
            'pending' => 'Pending',
            'accepted' => 'Accepted',
            'preparing' => 'Preparing',
            'ready_for_pickup' => 'Ready for Pickup',
            'out_for_delivery' => 'Out for Delivery',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ];
    @endphp

    <x-ui.page-header
        title="{{ $isBuyerOrders ? 'My Orders' : ($isAdminOrders ? 'Orders' : 'Order Fulfillment') }}"
        subtitle="{{ $isBuyerOrders ? 'Track your orders and view receipts.' : ($isAdminOrders ? 'Review buyer orders, order status, and payment information.' : 'View buyer orders and update their status.') }}"
    />

    @if(auth()->user()->isFarmer())
        <x-ui.dashboard-card class="mb-5" title="Status Guide" subtitle="Use this guide to understand what each order status means.">
            <div class="grid gap-3 md:grid-cols-7">
                @foreach([
                    ['Pending', 'Waiting for farmer response'],
                    ['Accepted', 'Order accepted'],
                    ['Preparing', 'Products are being prepared'],
                    ['Ready for Pickup', 'Ready for buyer pickup'],
                    ['Out for Delivery', 'Ready and on the way to the buyer'],
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
            <div class="grid gap-3 md:grid-cols-7">
                @foreach([
                    ['Pending', 'Your order has been placed.'],
                    ['Accepted', 'Farmer accepted your order.'],
                    ['Preparing', 'Farmer is preparing your order.'],
                    ['Ready for Pickup', 'Your order is ready for pickup.'],
                    ['Out for Delivery', 'Your order is on the way.'],
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
                            @if($isAdminOrders)
                                <th class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wide text-gray-500">Order No.</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wide text-gray-500">Buyer</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wide text-gray-500">Total Amount</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wide text-gray-500">Payment Method</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wide text-gray-500">Payment Status</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wide text-gray-500">Order Status</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wide text-gray-500">Order Date</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wide text-gray-500">Action</th>
                            @else
                                <th class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wide text-gray-500">Order Number</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wide text-gray-500">{{ auth()->user()->isFarmer() ? 'Customer' : 'Items' }}</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wide text-gray-500">Total Amount</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wide text-gray-500">Current Status</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wide text-gray-500">Order Date</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wide text-gray-500">Next Action</th>
                            @endif
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
                                    @if(auth()->user()->isFarmer() || $isAdminOrders)
                                        <div class="font-semibold text-gray-800 dark:text-gray-100">{{ $order->consumer->name ?? 'Buyer' }}</div>
                                        @if(auth()->user()->isFarmer())
                                            <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                                @foreach($farmerItems as $item)
                                                    <div>{{ optional($item->product)->name ?? 'Product unavailable' }} x {{ $item->quantity }} kg</div>
                                                @endforeach
                                            </div>
                                        @endif
                                    @else
                                        {{ $order->items_count ?? $order->items()->count() }} items
                                    @endif
                                </td>
                                <td class="px-6 py-4 font-semibold text-gray-800">PHP {{ number_format(auth()->user()->isFarmer() ? $farmerSubtotal : $order->total, 2) }}</td>
                                @if($isAdminOrders)
                                    <td class="px-6 py-4 text-gray-600">{{ $order->paymentMethodLabel() }}</td>
                                    <td class="px-6 py-4"><x-ui.status-badge :status="$order->paymentStatusLabel()" /></td>
                                    <td class="px-6 py-4"><x-ui.status-badge :status="$order->status" /></td>
                                @else
                                    <td class="px-6 py-4">
                                        <x-ui.status-badge :status="$order->status" />
                                @endif
                                    @unless($isAdminOrders)
                                        <div class="mt-2 text-xs font-semibold text-gray-500 dark:text-gray-400">
                                            @if($order->payment_method === 'gcash')
                                                GCash - {{ $order->paymentStatusLabel() }}
                                            @else
                                                Cash on Delivery - {{ $order->paymentStatusLabel() }}
                                            @endif
                                        </div>
                                    @endunless
                                    @unless($isAdminOrders)
                                        <div class="mt-1 text-xs font-semibold text-gray-500 dark:text-gray-400">
                                            {{ $order->fulfillmentMethodLabel() }}
                                        </div>
                                    @endunless
                                @unless($isAdminOrders)
                                    </td>
                                @endunless
                                <td class="px-6 py-4 text-gray-500">{{ $order->created_at->timezone(config('app.timezone'))->format('M d, Y h:i A') }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-2">
                                        <a href="{{ route('orders.show', $order) }}" class="rounded-lg border border-emerald-200 px-4 py-2 text-sm font-bold text-emerald-800 hover:bg-emerald-50 dark:hover:bg-emerald-900/40">{{ $isBuyerOrders ? 'View Tracking' : 'View Details' }}</a>
                                        @if($isBuyerOrders)
                                            @if($order->canBeCancelledByConsumer())
                                                <form method="POST" action="{{ route('consumer.orders.cancel', $order) }}" onsubmit="return confirm('{{ $order->payment_method === 'gcash' && $order->payment_status === 'paid' ? 'This order has already been paid through GCash. Cancelling it will require a refund. Continue?' : 'Are you sure you want to cancel this order?' }}')">
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
                                                <span class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-500">Receipt available after completion</span>
                                            @endif
                                        @elseif(auth()->user()->isFarmer())
                                            @if($order->status === 'pending')
                                                @if($order->payment_method === 'gcash' && $order->payment_status !== 'paid')
                                                    <span class="rounded-lg border border-amber-200 px-4 py-2 text-sm font-semibold text-amber-700 dark:border-amber-800 dark:text-amber-200">
                                                        {{ $order->payment_status === 'rejected' ? 'Payment proof rejected' : 'Confirm GCash payment first' }}
                                                    </span>
                                                @else
                                                    <form method="POST" action="{{ route('farmer.orders.accept', $order) }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="rounded-lg bg-emerald-700 px-4 py-2 text-sm font-bold text-white hover:bg-emerald-800">Accept Order</button>
                                                    </form>
                                                @endif
                                                <form method="POST" action="{{ route('farmer.orders.cancel', $order) }}" onsubmit="return confirm('{{ $order->payment_method === 'gcash' && $order->payment_status === 'paid' ? 'This order has already been paid through GCash. Cancelling it will require a refund. Continue?' : 'Are you sure you want to cancel this order?' }}')">
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
                                                <form method="POST" action="{{ route('farmer.orders.cancel', $order) }}" onsubmit="return confirm('{{ $order->payment_method === 'gcash' && $order->payment_status === 'paid' ? 'This order has already been paid through GCash. Cancelling it will require a refund. Continue?' : 'Are you sure you want to cancel this order?' }}')">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-bold text-white hover:bg-red-700">Cancel Order</button>
                                                </form>
                                            @elseif($order->status === 'preparing')
                                                @if($order->payment_method === 'gcash' && $order->payment_status !== 'paid')
                                                    <span class="rounded-lg border border-amber-200 px-4 py-2 text-sm font-semibold text-amber-700 dark:border-amber-800 dark:text-amber-200">
                                                        Confirm GCash payment before continuing
                                                    </span>
                                                @else
                                                    @if($order->fulfillment_method === 'pickup')
                                                        <form method="POST" action="{{ route('farmer.orders.ready-for-pickup', $order) }}" onsubmit="return confirm('Mark this order as ready for pickup?')">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="rounded-lg bg-amber-600 px-4 py-2 text-sm font-bold text-white hover:bg-amber-700">Mark as Ready for Pickup</button>
                                                        </form>
                                                    @else
                                                        <form method="POST" action="{{ route('farmer.orders.out-for-delivery', $order) }}" onsubmit="return confirm('Mark this order as out for delivery?')">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="rounded-lg bg-sky-600 px-4 py-2 text-sm font-bold text-white hover:bg-sky-700">Mark as Out for Delivery</button>
                                                        </form>
                                                    @endif
                                                @endif
                                            @elseif(in_array($order->status, ['ready_for_pickup', 'out_for_delivery'], true))
                                                @if($order->payment_method === 'gcash' && $order->payment_status !== 'paid')
                                                    <span class="rounded-lg border border-amber-200 px-4 py-2 text-sm font-semibold text-amber-700 dark:border-amber-800 dark:text-amber-200">
                                                        Confirm GCash payment before completion
                                                    </span>
                                                @else
                                                    <span class="rounded-lg border border-emerald-200 px-4 py-2 text-sm font-semibold text-emerald-800 dark:border-emerald-800 dark:text-emerald-200">
                                                        Open details to upload proof and complete
                                                    </span>
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
