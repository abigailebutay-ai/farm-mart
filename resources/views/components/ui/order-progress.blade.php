@props([
    'order',
])

@php
    $status = strtolower($order->status ?? 'pending');
    $fulfillmentMethod = $order->fulfillment_method === 'pickup' ? 'pickup' : 'delivery';

    if ($status === 'cancelled') {
        $steps = ['Order Placed', 'Cancelled'];
        $currentIndex = 1;
    } else {
        $steps = $fulfillmentMethod === 'pickup'
            ? ['Order Placed', 'Accepted', 'Preparing', 'Ready for Pickup', 'Completed']
            : ['Order Placed', 'Accepted', 'Preparing', 'Out for Delivery', 'Completed'];
        $statusMap = [
            'pending' => 0,
            'accepted' => 1,
            'preparing' => 2,
            'ready_for_pickup' => 3,
            'out_for_delivery' => 3,
            'completed' => 4,
        ];
        $currentIndex = $statusMap[$status] ?? 0;
    }

    $deliveryMessages = [
        'pending' => 'Your order has been placed and is waiting for farmer approval.',
        'accepted' => 'The farmer accepted your order.',
        'preparing' => 'The farmer is preparing your products.',
        'out_for_delivery' => 'Your order is on the way to your location.',
        'completed' => 'Your order has been delivered and completed.',
        'cancelled' => 'Your order was cancelled.',
    ];

    $pickupMessages = [
        'pending' => 'Your order has been placed and is waiting for farmer approval.',
        'accepted' => 'The farmer accepted your order.',
        'preparing' => 'The farmer is preparing your products.',
        'ready_for_pickup' => 'Your order is ready for pickup.',
        'completed' => 'Your order has been picked up and completed.',
        'cancelled' => 'Your order was cancelled.',
    ];

    $statusMessages = $fulfillmentMethod === 'pickup' ? $pickupMessages : $deliveryMessages;
@endphp

<div {{ $attributes->merge(['class' => 'rounded-xl border border-slate-200 bg-white/70 p-4 dark:border-gray-700 dark:bg-gray-900/70']) }}>
    <div class="flex items-start">
        @foreach($steps as $index => $label)
            @php
                $isCancelled = $label === 'Cancelled';
                $isDone = $index <= $currentIndex;
                $isCurrent = $index === $currentIndex;
                $lineDone = $index < $currentIndex;
            @endphp

            <div class="relative flex flex-1 flex-col items-center text-center">
                @if(! $loop->last)
                    <div class="absolute left-1/2 top-3 h-1 w-full {{ $status === 'cancelled' && $lineDone ? 'bg-red-500' : ($lineDone ? 'bg-emerald-500' : 'bg-slate-300 dark:bg-gray-700') }}"></div>
                @endif

                <div class="relative z-10 flex h-7 w-7 items-center justify-center rounded-full border-2 text-xs font-black shadow-sm {{ $isCancelled && $isDone ? 'border-red-500 bg-red-600 text-white ring-4 ring-red-500/20' : ($isDone ? 'border-emerald-500 bg-emerald-600 text-white ' . ($isCurrent ? 'ring-4 ring-emerald-500/20' : '') : 'border-slate-400 bg-slate-100 text-slate-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300') }}">
                    @if($isCancelled && $isDone)
                        <x-ui.icon name="x" class="h-4 w-4" />
                    @elseif($isDone)
                        <x-ui.icon name="check" class="h-4 w-4" />
                    @else
                        {{ $index + 1 }}
                    @endif
                </div>

                <p class="mt-2 max-w-[7rem] text-xs font-bold leading-snug {{ $isCancelled && $isDone ? 'text-red-500 dark:text-red-300' : ($isDone ? 'text-emerald-700 dark:text-emerald-300' : 'text-slate-500 dark:text-gray-400') }}">
                    {{ $label }}
                </p>
            </div>
        @endforeach
    </div>

    <p class="mt-4 rounded-lg bg-slate-100 px-3 py-2 text-sm font-semibold text-slate-700 dark:bg-gray-800 dark:text-gray-200">
        {{ $statusMessages[$status] ?? $statusMessages['pending'] }}
    </p>
</div>
