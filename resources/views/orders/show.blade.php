@extends('layouts.app')

@section('page-title', 'Order #' . $order->id)

@section('content')
    @php
        $isBuyerOrder = auth()->user()->isConsumer();
        $isGcashPayment = $order->payment_method === 'gcash';
        $fulfillmentMethod = $order->fulfillment_method === 'pickup' ? 'pickup' : 'delivery';
        $completionProofLabel = $fulfillmentMethod === 'pickup' ? 'Proof of Pickup' : 'Proof of Delivery';
        $visibleItems = auth()->user()->isFarmer()
            ? $order->items->where('farmer_id', auth()->id())
            : $order->items;
        $visibleSubtotal = $visibleItems->sum('subtotal');
        $pickupLocations = $visibleItems
            ->filter(fn ($item) => $item->farmer)
            ->groupBy('farmer_id')
            ->map(function ($items) {
                return [
                    'farmer' => $items->first()->farmer,
                    'products' => $items
                        ->map(fn ($item) => optional($item->product)->name)
                        ->filter()
                        ->unique()
                        ->values(),
                ];
            })
            ->values();
        $trackingSteps = $order->status === 'cancelled'
            ? ['pending' => 'Order Placed', 'cancelled' => 'Cancelled']
            : ($fulfillmentMethod === 'pickup'
                ? ['pending' => 'Order Placed', 'accepted' => 'Accepted', 'preparing' => 'Preparing', 'ready_for_pickup' => 'Ready for Pickup', 'completed' => 'Completed']
                : ['pending' => 'Order Placed', 'accepted' => 'Accepted', 'preparing' => 'Preparing', 'out_for_delivery' => 'Out for Delivery', 'completed' => 'Completed']);
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

                    @if($isBuyerOrder)
                        <x-ui.order-progress :order="$order" class="mb-6" />
                        @if($fulfillmentMethod === 'pickup')
                            <div class="mb-6 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900 dark:border-amber-800 dark:bg-amber-900/30 dark:text-amber-100">
                                <p class="font-bold">Pickup Location</p>
                                <p class="mt-1">
                                    {{ $order->status === 'ready_for_pickup'
                                        ? 'Your order is ready for pickup. Please proceed to the pickup address.'
                                        : 'Your order is ready for pickup when the status becomes "Ready for Pickup."' }}
                                </p>
                            </div>
                        @endif
                    @endif

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
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Payment Information</h3>
                </div>

                <div class="p-6 space-y-4">
                    <div class="grid gap-4 text-sm md:grid-cols-2">
                        <div>
                            <p class="text-gray-600 dark:text-gray-400 mb-1">Payment Method</p>
                            <p class="font-semibold text-gray-900 dark:text-white">{{ $order->paymentMethodLabel() }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600 dark:text-gray-400 mb-1">Payment Status</p>
                            <x-ui.status-badge :status="$order->paymentStatusLabel()" />
                        </div>
                        @if($order->refund_status)
                            <div>
                                <p class="text-gray-600 dark:text-gray-400 mb-1">Refund Status</p>
                                <x-ui.status-badge :status="$order->refundStatusLabel()" />
                            </div>
                        @endif
                        @if($order->refund_reference)
                            <div>
                                <p class="text-gray-600 dark:text-gray-400 mb-1">Refund Reference</p>
                                <p class="font-semibold text-gray-900 dark:text-white">{{ $order->refund_reference }}</p>
                            </div>
                        @endif
                        @if($order->refunded_at)
                            <div>
                                <p class="text-gray-600 dark:text-gray-400 mb-1">Refunded At</p>
                                <p class="font-semibold text-gray-900 dark:text-white">{{ $order->refunded_at->timezone(config('app.timezone'))->format('M d, Y h:i A') }}</p>
                            </div>
                        @endif
                        @if($order->payment_reference)
                            <div>
                                <p class="text-gray-600 dark:text-gray-400 mb-1">GCash Reference Number</p>
                                <p class="font-semibold text-gray-900 dark:text-white">{{ $order->payment_reference }}</p>
                            </div>
                        @endif
                        @if(auth()->user()->isAdmin() && $isGcashPayment)
                            <div>
                                <p class="text-gray-600 dark:text-gray-400 mb-1">Order Total Amount</p>
                                <p class="font-semibold text-gray-900 dark:text-white">PHP {{ number_format($order->total, 2) }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600 dark:text-gray-400 mb-1">Buyer Name</p>
                                <p class="font-semibold text-gray-900 dark:text-white">{{ $order->consumer->name ?? 'Buyer' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600 dark:text-gray-400 mb-1">Order Date</p>
                                <p class="font-semibold text-gray-900 dark:text-white">{{ $order->created_at->timezone(config('app.timezone'))->format('M d, Y h:i A') }}</p>
                            </div>
                        @endif
                    </div>

                    @if($isBuyerOrder && $isGcashPayment)
                        <div class="rounded-lg border px-4 py-3 text-sm font-semibold {{ $order->payment_status === 'paid' ? 'border-emerald-200 bg-emerald-50 text-emerald-800 dark:border-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-100' : ($order->payment_status === 'rejected' ? 'border-red-200 bg-red-50 text-red-800 dark:border-red-800 dark:bg-red-900/30 dark:text-red-100' : 'border-amber-200 bg-amber-50 text-amber-900 dark:border-amber-800 dark:bg-amber-900/30 dark:text-amber-100') }}">
                            @if($order->refund_status === 'pending')
                                Your paid GCash order was cancelled. Refund is pending.
                            @elseif($order->refund_status === 'refunded')
                                Your GCash payment has been refunded.
                            @elseif($order->payment_status === 'paid')
                                Your GCash payment has been verified. The farmer can now accept your order.
                            @elseif($order->payment_status === 'rejected')
                                Your GCash payment proof was rejected. The order has been cancelled. Please place a new order with a valid proof of payment.
                            @else
                                Your GCash proof of payment is waiting for admin verification.
                            @endif
                        </div>
                    @endif

                    @if($order->refund_note)
                        <div>
                            <p class="text-gray-600 dark:text-gray-400 mb-1 text-sm">Refund Note</p>
                            <p class="rounded-lg bg-gray-100 px-4 py-3 text-sm font-semibold text-gray-800 dark:bg-gray-900 dark:text-gray-200">{{ $order->refund_note }}</p>
                        </div>
                    @endif

                    @if($order->payment_proof)
                        <div>
                            <p class="text-gray-600 dark:text-gray-400 mb-2 text-sm">Proof of Payment</p>
                            @if($order->paymentProofIsImage())
                                <a href="{{ $order->paymentProofUrl() }}" target="_blank" rel="noopener" class="block max-w-sm overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700">
                                    <img src="{{ $order->paymentProofUrl() }}" alt="Proof of payment for Order #{{ $order->id }}" class="h-auto w-full object-cover">
                                </a>
                            @else
                                <a href="{{ $order->paymentProofUrl() }}" target="_blank" rel="noopener" class="inline-flex rounded-lg bg-emerald-700 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-800">
                                    View Proof
                                </a>
                            @endif
                        </div>
                    @endif

                    @if(auth()->user()->isAdmin() && $isGcashPayment)
                        <div class="border-t border-gray-200 pt-4 dark:border-gray-700">
                            <h4 class="text-base font-bold text-gray-900 dark:text-white">Payment Verification</h4>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Check the GCash proof, reference number, buyer name, and order total before marking payment as paid.</p>

                            <div class="mt-4 flex flex-wrap gap-3">
                                @if($order->refund_status === 'pending')
                                    <p class="w-full rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-900 dark:border-amber-800 dark:bg-amber-900/30 dark:text-amber-100">
                                        This paid GCash order was cancelled and needs refund processing.
                                    </p>
                                    <form method="POST" action="{{ route('admin.orders.refund', $order) }}" class="w-full space-y-3 rounded-xl border border-gray-200 p-4 dark:border-gray-700" onsubmit="return confirm('Mark this refund as completed?')">
                                        @csrf
                                        @method('PATCH')
                                        <div>
                                            <label for="refund_reference" class="mb-2 block text-sm font-bold text-gray-900 dark:text-white">Refund Reference Number</label>
                                            <input id="refund_reference" name="refund_reference" type="text" required class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-green-600 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                            @error('refund_reference')
                                                <p class="mt-2 text-sm font-semibold text-red-500">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div>
                                            <label for="refund_note" class="mb-2 block text-sm font-bold text-gray-900 dark:text-white">Refund Note</label>
                                            <textarea id="refund_note" name="refund_note" rows="3" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-green-600 dark:border-gray-600 dark:bg-gray-700 dark:text-white"></textarea>
                                            @error('refund_note')
                                                <p class="mt-2 text-sm font-semibold text-red-500">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <button type="submit" class="rounded-lg bg-emerald-700 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-800">
                                            Mark as Refunded
                                        </button>
                                    </form>
                                @elseif($order->refund_status === 'refunded')
                                    <p class="w-full rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800 dark:border-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-100">
                                        Refund completed.
                                    </p>
                                @elseif($order->payment_status === 'pending_verification')
                                    <form method="POST" action="{{ route('admin.orders.payment.paid', $order) }}" onsubmit="return confirm('Mark this GCash payment as paid?')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="rounded-lg bg-emerald-700 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-800">
                                            Mark as Paid
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.orders.payment.reject', $order) }}" onsubmit="return confirm('Reject this GCash payment proof?')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">
                                            Reject Payment
                                        </button>
                                    </form>
                                @elseif($order->payment_status === 'paid')
                                    <x-ui.status-badge status="Paid" />
                                    <form method="POST" action="{{ route('admin.orders.payment-status', $order) }}" onsubmit="return confirm('Set this GCash payment back to pending verification?')">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="payment_status" value="pending_verification">
                                        <button type="submit" class="rounded-lg border border-amber-300 px-4 py-2 text-sm font-semibold text-amber-800 hover:bg-amber-50 dark:border-amber-700 dark:text-amber-200 dark:hover:bg-amber-900/30">
                                            Set Back to Pending Verification
                                        </button>
                                    </form>
                                @elseif($order->payment_status === 'rejected')
                                    <x-ui.status-badge status="Rejected" />
                                    <x-ui.status-badge status="Cancelled" />
                                    <p class="w-full rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-800 dark:border-red-800 dark:bg-red-900/30 dark:text-red-100">
                                        Payment rejected by admin.
                                    </p>
                                @else
                                    <form method="POST" action="{{ route('admin.orders.payment-status', $order) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="payment_status" value="pending_verification">
                                        <button type="submit" class="rounded-lg bg-amber-600 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-700">
                                            Mark Pending Verification
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="{{ $isBuyerOrder ? 'buyer-card' : 'bg-white dark:bg-gray-800' }} rounded-lg shadow overflow-hidden">
                <div class="buyer-divider px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Pickup or Delivery</h3>
                </div>

                <div class="p-6 space-y-4">
                    <div class="grid gap-4 text-sm md:grid-cols-2">
                        <div>
                            <p class="text-gray-600 dark:text-gray-400 mb-1">Fulfillment Method</p>
                            <p class="font-semibold text-gray-900 dark:text-white">{{ $order->fulfillmentMethodLabel() }}</p>
                        </div>
                        @if($order->completed_at)
                            <div>
                                <p class="text-gray-600 dark:text-gray-400 mb-1">Completed At</p>
                                <p class="font-semibold text-gray-900 dark:text-white">{{ $order->completed_at->timezone(config('app.timezone'))->format('M d, Y h:i A') }}</p>
                            </div>
                        @endif
                    </div>

                    @if($fulfillmentMethod === 'pickup')
                        <div class="rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900 dark:border-amber-800 dark:bg-amber-900/30 dark:text-amber-100">
                            <h4 class="font-bold">Pickup Locations</h4>
                            <div class="mt-3 space-y-3">
                                @forelse($pickupLocations as $location)
                                    @php($farmer = $location['farmer'])
                                    <div class="rounded-lg border border-amber-200/80 bg-white/70 p-3 dark:border-amber-800/80 dark:bg-gray-900/60">
                                        <p class="font-bold text-gray-900 dark:text-white">{{ $loop->iteration }}. {{ $farmer->name }}</p>
                                        <p class="mt-1"><span class="font-semibold">Products:</span> {{ $location['products']->join(', ') }}</p>
                                        <p class="mt-1">
                                            <span class="font-semibold">Pickup Address:</span>
                                            {{ $farmer->address ?: 'Pickup address not provided. Please contact the farmer before pickup.' }}
                                        </p>
                                        <p class="mt-1"><span class="font-semibold">Seller Contact:</span> {{ $farmer->phone ?: 'Not provided' }}</p>
                                    </div>
                                @empty
                                    <p class="font-semibold">Pickup address not provided. Please contact the farmer before pickup.</p>
                                @endforelse
                            </div>
                        </div>
                    @else
                        <div class="rounded-lg bg-blue-50 p-4 text-sm text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                            <h4 class="font-bold">Delivery Details</h4>
                            <p class="mt-1"><span class="font-semibold">Address:</span> {{ $order->consumer->address ?? 'Not provided' }}</p>
                            <p class="mt-1"><span class="font-semibold">Contact Number:</span> {{ $order->consumer->phone ?? 'Not provided' }}</p>
                            @if($order->notes)
                                <p class="mt-1"><span class="font-semibold">Delivery Instructions:</span> {{ $order->notes }}</p>
                            @endif
                        </div>
                    @endif

                    @if($order->completion_proof)
                        <div>
                            <p class="text-gray-600 dark:text-gray-400 mb-2 text-sm">{{ $completionProofLabel }}</p>
                            @if($order->completionProofIsImage())
                                <a href="{{ $order->completionProofUrl() }}" target="_blank" rel="noopener" class="block max-w-sm overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700">
                                    <img src="{{ $order->completionProofUrl() }}" alt="{{ $completionProofLabel }} for Order #{{ $order->id }}" class="h-auto w-full object-cover">
                                </a>
                            @else
                                <a href="{{ $order->completionProofUrl() }}" target="_blank" rel="noopener" class="inline-flex rounded-lg bg-emerald-700 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-800">
                                    View {{ $completionProofLabel }}
                                </a>
                            @endif
                        </div>
                    @endif

                    @if($order->completion_note)
                        <div>
                            <p class="text-gray-600 dark:text-gray-400 mb-1 text-sm">Completion Note</p>
                            <p class="rounded-lg bg-gray-100 px-4 py-3 text-sm font-semibold text-gray-800 dark:bg-gray-900 dark:text-gray-200">{{ $order->completion_note }}</p>
                        </div>
                    @endif
                </div>
            </div>

            @if(! $isBuyerOrder)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
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
            @endif

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
                                    <p>Qty: {{ $item->quantity }} kg x PHP {{ number_format($item->price, 2) }} / kg</p>
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
                        <p class="mb-4 text-sm font-semibold text-gray-600 dark:text-gray-300">
                            Update this order step by step so the buyer can track the progress.
                        </p>

                        <div class="flex flex-wrap gap-3">
                            @if($order->status === 'pending')
                                @if($isGcashPayment && $order->payment_status !== 'paid')
                                    <div class="w-full rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-900 dark:border-amber-800 dark:bg-amber-900/30 dark:text-amber-100">
                                        {{ $order->payment_status === 'rejected' ? 'Payment proof was rejected. Buyer must upload valid proof before this order can continue.' : 'Waiting for admin payment verification before accepting this order.' }}
                                    </div>
                                @else
                                    <form method="POST" action="{{ route('farmer.orders.accept', $order) }}" onsubmit="return confirm('Accept this order?')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="rounded-lg bg-emerald-700 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-800">Accept Order</button>
                                    </form>
                                @endif
                                <form method="POST" action="{{ route('farmer.orders.cancel', $order) }}" onsubmit="return confirm('{{ $order->payment_method === 'gcash' && $order->payment_status === 'paid' ? 'This order has already been paid through GCash. Cancelling it will require a refund. Continue?' : 'Cancel this order?' }}')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">Cancel Order</button>
                                </form>
                            @elseif($order->status === 'accepted')
                                <form method="POST" action="{{ route('farmer.orders.preparing', $order) }}" onsubmit="return confirm('Mark this order as preparing?')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Mark as Preparing</button>
                                </form>
                                <form method="POST" action="{{ route('farmer.orders.cancel', $order) }}" onsubmit="return confirm('{{ $order->payment_method === 'gcash' && $order->payment_status === 'paid' ? 'This order has already been paid through GCash. Cancelling it will require a refund. Continue?' : 'Cancel this order?' }}')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">Cancel Order</button>
                                </form>
                            @elseif($order->status === 'preparing')
                                @if($isGcashPayment && $order->payment_status !== 'paid')
                                    <div class="w-full rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-900 dark:border-amber-800 dark:bg-amber-900/30 dark:text-amber-100">
                                        {{ $order->payment_status === 'rejected' ? 'Payment proof was rejected. This order cannot continue.' : 'GCash payment is pending admin verification. You can continue this order after payment is marked paid.' }}
                                    </div>
                                @else
                                    @if($fulfillmentMethod === 'pickup')
                                        <form method="POST" action="{{ route('farmer.orders.ready-for-pickup', $order) }}" onsubmit="return confirm('Mark this order as ready for pickup?')">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="rounded-lg bg-amber-600 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-700">Mark as Ready for Pickup</button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('farmer.orders.out-for-delivery', $order) }}" onsubmit="return confirm('Mark this order as out for delivery?')">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="rounded-lg bg-sky-600 px-4 py-2 text-sm font-semibold text-white hover:bg-sky-700">Mark as Out for Delivery</button>
                                        </form>
                                    @endif
                                @endif
                            @elseif(in_array($order->status, ['out_for_delivery', 'ready_for_pickup'], true))
                                @if($isGcashPayment && $order->payment_status !== 'paid')
                                    <div class="w-full rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-900 dark:border-amber-800 dark:bg-amber-900/30 dark:text-amber-100">
                                        {{ $order->payment_status === 'rejected' ? 'Payment proof was rejected. This order cannot be completed.' : 'GCash payment must be verified before completing this order.' }}
                                    </div>
                                @else
                                    <form method="POST" action="{{ route('farmer.orders.complete-with-proof', $order) }}" enctype="multipart/form-data" class="w-full space-y-4 rounded-xl border border-gray-200 p-4 dark:border-gray-700" onsubmit="return confirm('Mark this order as completed?')">
                                        @csrf
                                        @method('PATCH')
                                        <div>
                                            <label for="completion_proof" class="mb-2 block text-sm font-bold text-gray-900 dark:text-white">Upload {{ $completionProofLabel }}</label>
                                            <input type="file" id="completion_proof" name="completion_proof" required accept="image/jpeg,image/png,image/jpg,image/webp,application/pdf" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 file:mr-4 file:rounded-lg file:border-0 file:bg-emerald-700 file:px-4 file:py-2 file:text-sm file:font-bold file:text-white dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Accepted files: JPG, PNG, WebP, or PDF up to 5 MB.</p>
                                            @error('completion_proof')
                                                <p class="mt-2 text-sm font-semibold text-red-500">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div>
                                            <label for="completion_note" class="mb-2 block text-sm font-bold text-gray-900 dark:text-white">Completion Note</label>
                                            <textarea id="completion_note" name="completion_note" rows="3" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-green-600 dark:border-gray-600 dark:bg-gray-700 dark:text-white" placeholder="Example: Buyer received the order.">{{ old('completion_note') }}</textarea>
                                            @error('completion_note')
                                                <p class="mt-2 text-sm font-semibold text-red-500">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <button type="submit" class="rounded-lg bg-emerald-700 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-800">Mark as Completed</button>
                                    </form>
                                @endif
                            @elseif($order->status === 'completed')
                                <span class="rounded-lg bg-emerald-100 px-4 py-2 text-sm font-bold text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-200">Completed</span>
                            @elseif($order->status === 'cancelled')
                                <span class="rounded-lg bg-red-100 px-4 py-2 text-sm font-bold text-red-800 dark:bg-red-900/40 dark:text-red-200">Cancelled</span>
                                @if($isGcashPayment && $order->payment_status === 'rejected')
                                    <p class="w-full rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-800 dark:border-red-800 dark:bg-red-900/30 dark:text-red-100">
                                        This order was cancelled because the GCash payment proof was rejected.
                                    </p>
                                @endif
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

                    <div class="flex justify-between text-gray-600 dark:text-gray-400">
                        <span>Fulfillment:</span>
                        <span>{{ $order->fulfillmentMethodLabel() }}</span>
                    </div>

                    @if(! auth()->user()->isFarmer())
                        <div class="flex justify-between text-gray-600 dark:text-gray-400">
                            <span>Total Kilograms:</span>
                            <span>{{ number_format($order->total_kg ?? 0, 2) }} kg</span>
                        </div>

                        @if($order->coupon_code)
                            <div class="flex justify-between text-gray-600 dark:text-gray-400">
                                <span>Coupon Code:</span>
                                <span>{{ $order->coupon_code }}</span>
                            </div>
                        @endif

                        @if(($order->discount_amount ?? 0) > 0)
                            <div class="flex justify-between text-emerald-600 dark:text-emerald-400">
                                <span>Discount:</span>
                                <span>- PHP {{ number_format($order->discount_amount, 2) }}</span>
                            </div>
                        @endif
                    @endif

                    <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                        <div class="flex justify-between text-lg font-bold text-gray-900 dark:text-white">
                            <span>Final Total:</span>
                            <span class="text-green-600 dark:text-green-400">PHP {{ number_format(auth()->user()->isFarmer() ? $visibleSubtotal : $order->total, 2) }}</span>
                        </div>
                    </div>

                    <div class="pt-2">
                        @if($isBuyerOrder && $order->canBeCancelledByConsumer())
                            <form method="POST" action="{{ route('consumer.orders.cancel', $order) }}" class="mb-3" onsubmit="return confirm('{{ $order->payment_method === 'gcash' && $order->payment_status === 'paid' ? 'This order has already been paid through GCash. Cancelling it will require a refund. Continue?' : 'Are you sure you want to cancel this order?' }}')">
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
