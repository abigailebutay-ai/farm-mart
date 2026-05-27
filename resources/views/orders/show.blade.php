@extends('layouts.app')

@section('page-title', 'Order #' . $order->id)

@section('content')
    @php
        $isBuyerOrder = auth()->user()->isConsumer();
        $visibleItems = auth()->user()->isFarmer()
            ? $order->items->where('farmer_id', auth()->id())
            : $order->items;
        $visibleSubtotal = $visibleItems->sum('subtotal');
        $trackingSteps = $order->status === 'cancelled'
            ? ['pending' => 'Order Placed', 'cancelled' => 'Cancelled']
            : ['pending' => 'Order Placed', 'accepted' => 'Accepted', 'preparing' => 'Preparing', 'completed' => 'Completed'];
        $statusOrder = array_keys($trackingSteps);
        $currentStepIndex = array_search($order->status, $statusOrder, true);

        if ($currentStepIndex === false) {
            $currentStepIndex = 0;
        }
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-6">
            <div class="{{ $isBuyerOrder ? 'order-card' : 'bg-white dark:bg-gray-800' }} rounded-lg shadow overflow-hidden">
                <div class="buyer-divider px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Order #{{ $order->id }}</h2>
                </div>

                <div class="p-6">
                    <div class="mb-6">
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Status</p>
                        <x-ui.status-badge :status="$order->status" />
                    </div>

                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-600 dark:text-gray-400 mb-1">Ordered At</p>
                            <p class="font-semibold text-gray-900 dark:text-white">{{ $order->created_at->timezone(config('app.timezone'))->format('M d, Y h:i A') }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600 dark:text-gray-400 mb-1">Last Updated</p>
                            <p class="font-semibold text-gray-900 dark:text-white">{{ $order->updated_at->timezone(config('app.timezone'))->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>

                    @if($isBuyerOrder)
                        <div class="mt-5 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-900 dark:border-amber-800 dark:bg-amber-900/30 dark:text-amber-100">
                            Orders can only be cancelled within 24 hours after ordering and before preparation starts.
                        </div>
                    @endif
                </div>
            </div>

            <div class="{{ $isBuyerOrder ? 'buyer-card' : 'bg-white dark:bg-gray-800' }} rounded-lg shadow overflow-hidden">
                <div class="buyer-divider px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Order Tracking</h3>
                </div>

                <div class="p-6">
                    <div class="space-y-4">
                        @foreach($trackingSteps as $stepStatus => $label)
                            @php
                                $stepIndex = array_search($stepStatus, $statusOrder, true);
                                $isCancelledStep = $stepStatus === 'cancelled';
                                $isDone = $isCancelledStep
                                    ? $order->status === 'cancelled'
                                    : $stepIndex !== false && $stepIndex <= $currentStepIndex && $order->status !== 'cancelled';
                                $timestamp = null;

                                if ($stepStatus === 'pending') {
                                    $timestamp = $order->created_at;
                                } elseif ($stepStatus === $order->status) {
                                    $timestamp = $order->updated_at;
                                }
                            @endphp
                            <div class="flex gap-3">
                                <div class="mt-1 h-4 w-4 rounded-full {{ $isCancelledStep && $isDone ? 'bg-red-600' : ($isDone ? 'bg-emerald-600' : 'bg-slate-300 dark:bg-slate-600') }}"></div>
                                <div>
                                    <p class="font-bold {{ $isCancelledStep && $isDone ? 'text-red-700 dark:text-red-300' : ($isDone ? 'text-emerald-700 dark:text-emerald-300' : 'text-slate-500 dark:text-slate-400') }}">{{ $label }}</p>
                                    @if($timestamp)
                                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $timestamp->timezone(config('app.timezone'))->format('M d, Y h:i A') }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="{{ $isBuyerOrder ? 'order-card' : 'bg-white dark:bg-gray-800' }} rounded-lg shadow overflow-hidden">
                <div class="buyer-divider px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Order Items</h3>
                </div>

                <div class="buyer-divider divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($visibleItems as $item)
                        <div class="p-6 flex gap-6">
                            <x-ui.product-image
                                :product="$item->product"
                                :alt="optional($item->product)->name ?? 'Product'"
                                image-class="h-20 w-20 rounded-lg object-cover"
                                placeholder-class="flex h-20 w-20 items-center justify-center rounded-lg bg-gray-200 text-gray-400 dark:bg-gray-700"
                                icon-class="h-7 w-7"
                            />

                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-900 dark:text-white mb-1">{{ optional($item->product)->name ?? 'Product unavailable' }}</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                                    From: <strong>{{ $item->farmer->name ?? 'Farmer' }}</strong>
                                </p>
                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                    <p>Qty: {{ $item->quantity }} {{ optional($item->product)->unit ?? 'piece' }} x PHP {{ number_format($item->price, 2) }} / {{ optional($item->product)->unit ?? 'piece' }}</p>
                                </div>
                            </div>

                            <div class="text-right">
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Subtotal</p>
                                <p class="text-lg font-bold text-green-600 dark:text-green-400">PHP {{ number_format($item->subtotal, 2) }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="{{ $isBuyerOrder ? 'buyer-card' : 'bg-white dark:bg-gray-800' }} rounded-lg shadow overflow-hidden">
                <div class="buyer-divider px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                        @if(auth()->user()->isFarmer())
                            Customer Information
                        @else
                            Your Information
                        @endif
                    </h3>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-600 dark:text-gray-400 mb-1">Name</p>
                            <p class="font-semibold text-gray-900 dark:text-white">{{ $order->consumer->name }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600 dark:text-gray-400 mb-1">Email</p>
                            <p class="font-semibold text-gray-900 dark:text-white">{{ $order->consumer->email }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600 dark:text-gray-400 mb-1">Phone</p>
                            <p class="font-semibold text-gray-900 dark:text-white">{{ $order->consumer->phone ?? 'Not provided' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600 dark:text-gray-400 mb-1">Address</p>
                            <p class="font-semibold text-gray-900 dark:text-white">{{ $order->consumer->address ?? 'Not provided' }}</p>
                        </div>
                    </div>

                    @if($order->notes)
                        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <p class="text-gray-600 dark:text-gray-400 text-sm mb-2">Special Instructions</p>
                            <p class="text-gray-900 dark:text-white">{{ $order->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>

            @if(auth()->user()->isFarmer() && $order->items()->where('farmer_id', auth()->id())->exists())
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Farmer Actions</h3>
                    </div>

                    <div class="p-6">
                        <div class="flex flex-wrap gap-3">
                            @if($order->status === 'pending')
                                <form method="POST" action="{{ route('farmer.orders.accept', $order) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="rounded-lg bg-emerald-700 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-800">Accept Order</button>
                                </form>
                                <form method="POST" action="{{ route('farmer.orders.cancel', $order) }}" onsubmit="return confirm('Are you sure you want to cancel this order?')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">Cancel Order</button>
                                </form>
                            @elseif($order->status === 'accepted')
                                <form method="POST" action="{{ route('farmer.orders.preparing', $order) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Mark as Preparing</button>
                                </form>
                                <form method="POST" action="{{ route('farmer.orders.cancel', $order) }}" onsubmit="return confirm('Are you sure you want to cancel this order?')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">Cancel Order</button>
                                </form>
                            @elseif($order->status === 'preparing')
                                <form method="POST" action="{{ route('farmer.orders.complete', $order) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="rounded-lg bg-emerald-700 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-800">Mark as Completed</button>
                                </form>
                            @elseif($order->status === 'completed')
                                <span class="rounded-lg bg-emerald-100 px-4 py-2 text-sm font-bold text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-200">Completed</span>
                            @elseif($order->status === 'cancelled')
                                <span class="rounded-lg bg-red-100 px-4 py-2 text-sm font-bold text-red-800 dark:bg-red-900/40 dark:text-red-200">Cancelled</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div>
            <div class="{{ $isBuyerOrder ? 'receipt-card' : 'bg-white dark:bg-gray-800' }} rounded-lg shadow overflow-hidden sticky top-24">
                <div class="buyer-divider px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Order Summary</h2>
                </div>

                <div class="p-6 space-y-4">
                    <div class="flex justify-between text-gray-600 dark:text-gray-400">
                        <span>Total Items:</span>
                        <span>{{ $visibleItems->count() }}</span>
                    </div>

                    <div class="flex justify-between text-gray-600 dark:text-gray-400">
                        <span>Subtotal:</span>
                        <span>PHP {{ number_format(auth()->user()->isFarmer() ? $visibleSubtotal : $order->subtotal, 2) }}</span>
                    </div>

                    <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                        <div class="flex justify-between text-lg font-bold text-gray-900 dark:text-white">
                            <span>Total:</span>
                            <span class="text-green-600 dark:text-green-400">PHP {{ number_format(auth()->user()->isFarmer() ? $visibleSubtotal : $order->total, 2) }}</span>
                        </div>
                    </div>

                    <div class="pt-2">
                        @if($isBuyerOrder && $order->canBeCancelledByConsumer())
                            <form method="POST" action="{{ route('consumer.orders.cancel', $order) }}" class="mb-3" onsubmit="return confirm('Are you sure you want to cancel this order?')">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="block w-full rounded-lg bg-red-600 py-2 text-center text-sm font-semibold text-white transition hover:bg-red-700">
                                    Cancel Order
                                </button>
                            </form>
                        @elseif($isBuyerOrder && $order->status !== 'completed')
                            <div class="mb-3 rounded-lg border border-slate-200 px-3 py-2 text-center text-xs font-semibold text-slate-500 dark:border-slate-700 dark:text-slate-300">
                                {{ $order->consumerCancellationMessage() }}
                            </div>
                        @endif

                        @if($isBuyerOrder && $order->status === 'completed')
                            <a href="{{ route('consumer.orders.receipt', $order) }}" class="mb-3 block w-full rounded-lg bg-emerald-700 py-2 text-center text-sm font-semibold text-white transition hover:bg-emerald-800">
                                View Receipt
                            </a>
                        @elseif($isBuyerOrder)
                            <div class="mb-3 rounded-lg border border-slate-200 px-3 py-2 text-center text-xs font-semibold text-slate-500 dark:border-slate-700 dark:text-slate-300">
                                Receipt available after completion
                            </div>
                        @endif
                        <a href="{{ route('orders.index') }}" class="block w-full bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-white py-2 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition font-semibold text-center text-sm">
                            Back to Orders
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
