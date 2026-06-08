@extends('layouts.app')

@section('page-title', 'Cart')

@section('content')
    <x-ui.page-header
        title="Cart"
        subtitle="Your cart is grouped by farmer. Please checkout one farmer at a time."
    />

    @if($cart->items->count() > 0)
        <div class="space-y-6">
            @foreach($farmerGroups as $group)
                @php($farmer = $group['farmer'])

                <div class="overflow-hidden rounded-xl bg-white shadow dark:bg-gray-800">
                    <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-700">
                        <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Farmer: {{ $farmer->name }}</h2>
                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Discount is based on the total kg from this farmer.</p>
                            </div>
                            <a href="{{ route('checkout.farmer', $farmer) }}" class="inline-flex items-center justify-center rounded-lg bg-green-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-green-700">
                                Checkout from this farmer
                            </a>
                        </div>
                    </div>

                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($group['items'] as $item)
                            <div class="flex flex-col gap-5 p-6 hover:bg-gray-50 dark:hover:bg-gray-700 sm:flex-row">
                                <x-ui.product-image
                                    :product="$item->product"
                                    :alt="$item->product->name"
                                    image-class="h-24 w-24 rounded-lg object-cover"
                                    placeholder-class="flex h-24 w-24 items-center justify-center rounded-lg bg-gray-200 text-gray-400 dark:bg-gray-700"
                                    icon-class="h-8 w-8"
                                />

                                <div class="flex-1">
                                    <h3 class="mb-2 text-xl font-semibold text-gray-900 dark:text-white">{{ $item->product->name }}</h3>
                                    <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
                                        <div>
                                            <p class="mb-2 text-base text-gray-600 dark:text-gray-400">Price: PHP {{ number_format($item->price, 2) }} / kg</p>
                                            <p class="text-base text-gray-600 dark:text-gray-400">Quantity: {{ $item->quantity }} kg</p>
                                            <p class="mt-2 text-lg font-bold text-green-600 dark:text-green-400">Subtotal: PHP {{ number_format($item->subtotal, 2) }}</p>
                                        </div>

                                        <div class="flex flex-wrap items-end gap-3">
                                            <form method="POST" action="{{ route('cart.update-quantity', $item) }}" class="flex items-end gap-2">
                                                @csrf
                                                @method('PUT')
                                                <div>
                                                    <label for="quantity-{{ $item->id }}" class="text-sm text-gray-600 dark:text-gray-400">Quantity (kg)</label>
                                                    <input
                                                        id="quantity-{{ $item->id }}"
                                                        type="number"
                                                        name="quantity"
                                                        value="{{ $item->quantity }}"
                                                        min="1"
                                                        class="w-20 rounded border border-gray-300 px-2 py-2 text-center text-gray-900 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                                    >
                                                </div>
                                                <button type="submit" class="rounded-lg border border-blue-500 px-3 py-2 font-semibold text-blue-600 hover:bg-blue-50 dark:text-blue-300 dark:hover:bg-blue-900/30">Save Quantity</button>
                                            </form>

                                            <form method="POST" action="{{ route('cart.remove', $item) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="rounded-lg border border-red-500 px-3 py-2 font-semibold text-red-600 hover:bg-red-50 dark:text-red-300 dark:hover:bg-red-900/30" onclick="return confirm('Are you sure you want to remove this item from your cart?')">Remove Item</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="grid gap-4 bg-gray-100 p-6 dark:bg-gray-700 lg:grid-cols-[1fr_1fr_auto] lg:items-center">
                        <div class="space-y-2 text-gray-700 dark:text-gray-200">
                            <div class="flex justify-between gap-4">
                                <span>Subtotal:</span>
                                <span class="font-bold">PHP {{ number_format($group['subtotal'], 2) }}</span>
                            </div>
                            <div class="flex justify-between gap-4">
                                <span>Total kg:</span>
                                <span class="font-bold">{{ number_format($group['totalKg'], 2) }} kg</span>
                            </div>
                            @if($group['discountAmount'] > 0)
                                <div class="flex justify-between gap-4 text-emerald-600 dark:text-emerald-300">
                                    <span>Discount:</span>
                                    <span class="font-bold">- PHP {{ number_format($group['discountAmount'], 2) }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between gap-4 border-t border-gray-300 pt-2 text-lg font-black dark:border-gray-600">
                                <span>Total:</span>
                                <span>PHP {{ number_format($group['total'], 2) }}</span>
                            </div>
                        </div>

                        <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-600 dark:bg-gray-800">
                            <h3 class="font-bold text-gray-900 dark:text-white">Bulk Discount</h3>
                            @if($group['appliedDiscount'])
                                <p class="mt-2 text-sm font-semibold text-emerald-600 dark:text-emerald-300">
                                    Discount Applied: {{ $group['appliedDiscount']['discount_rate'] ?? 0 }}%
                                </p>
                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Discount: PHP {{ number_format($group['discountAmount'], 2) }}</p>
                                <form method="POST" action="{{ route('cart.remove-discount') }}" class="mt-3">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="farmer_id" value="{{ $farmer->id }}">
                                    <button type="submit" class="rounded-lg border border-red-200 px-3 py-2 text-sm font-bold text-red-700 hover:bg-red-50 dark:border-red-900/60 dark:text-red-300 dark:hover:bg-red-950/30">Remove Discount</button>
                                </form>
                            @elseif($group['eligibleDiscount']['eligible'] ?? false)
                                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                    Bulk discount available: {{ $group['eligibleDiscount']['discount_rate'] }}% off if you choose Bulk Order.
                                </p>
                                <form method="POST" action="{{ route('cart.apply-discount') }}" class="mt-3">
                                    @csrf
                                    <input type="hidden" name="farmer_id" value="{{ $farmer->id }}">
                                    <input type="hidden" name="purchase_type" value="bulk">
                                    <button type="submit" class="rounded-lg bg-emerald-700 px-4 py-2 text-sm font-bold text-white hover:bg-emerald-800">Apply Discount for this farmer</button>
                                </form>
                            @else
                                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">No discount available yet. Add more kg from this farmer to qualify.</p>
                            @endif
                        </div>

                        <div class="flex flex-col gap-3">
                            <a href="{{ route('checkout.farmer', $farmer) }}" class="rounded-lg bg-green-600 px-5 py-3 text-center font-semibold text-white transition hover:bg-green-700">
                                Checkout from this farmer
                            </a>
                            <a href="{{ route('consumer.marketplace', ['farmer_id' => $farmer->id]) }}" class="rounded-lg bg-gray-200 px-5 py-2 text-center font-semibold text-gray-900 transition hover:bg-gray-300 dark:bg-gray-800 dark:text-white dark:hover:bg-gray-600">
                                Add more from this farmer
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach

            <div class="flex flex-wrap justify-between gap-3">
                <a href="{{ route('consumer.marketplace') }}" class="rounded-lg bg-gray-200 px-5 py-2 text-center font-semibold text-gray-900 transition hover:bg-gray-300 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600">
                    Browse Marketplace
                </a>
                <form method="POST" action="{{ route('cart.clear') }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="font-semibold text-red-600 hover:text-red-700 dark:text-red-300" onclick="return confirm('Are you sure you want to clear your cart?')">
                        Clear Cart
                    </button>
                </form>
            </div>
        </div>
    @else
        <x-ui.empty-state title="Your cart is empty" message="Add products from the marketplace before checkout." action-url="{{ route('consumer.marketplace') }}" action-label="Browse Marketplace" icon="cart" />
    @endif
@endsection
