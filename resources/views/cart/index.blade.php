@extends('layouts.app')

@section('page-title', 'Cart')

@section('content')
    <x-ui.page-header
        title="Cart"
        subtitle="Review your selected products before checkout."
    />

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">
            @if($cart->items->count() > 0)
                <div class="bg-white dark:bg-gray-800 rounded-lg overflow-hidden shadow">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Cart Items ({{ $cart->items->count() }})</h2>
                    </div>

                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($cart->items as $item)
                            <div class="p-6 flex flex-col gap-5 hover:bg-gray-50 dark:hover:bg-gray-700 sm:flex-row">
                                <x-ui.product-image
                                    :product="$item->product"
                                    :alt="$item->product->name"
                                    image-class="h-24 w-24 rounded-lg object-cover"
                                    placeholder-class="flex h-24 w-24 items-center justify-center rounded-lg bg-gray-200 text-gray-400 dark:bg-gray-700"
                                    icon-class="h-8 w-8"
                                />

                                <div class="flex-1">
                                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">{{ $item->product->name }}</h3>
                                    <p class="text-base text-gray-600 dark:text-gray-400 mb-4">Seller: {{ $item->product->farmer->name }}</p>

                                    <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
                                        <div>
                                            <p class="text-base text-gray-600 dark:text-gray-400 mb-2">Price: PHP {{ number_format($item->price, 2) }} / kg</p>
                                            <p class="text-lg font-bold text-green-600 dark:text-green-400">Subtotal: PHP {{ number_format($item->subtotal, 2) }}</p>
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

                    <div class="px-6 py-4 bg-gray-100 dark:bg-gray-700 flex justify-end">
                        <form method="POST" action="{{ route('cart.clear') }}" class="inline">
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
        </div>

        <div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden sticky top-24">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Order Summary</h2>
                </div>

                <div class="p-6 space-y-4">
                    <div class="flex justify-between text-gray-600 dark:text-gray-400">
                        <span>Subtotal:</span>
                        <span>PHP {{ number_format($cart->subtotal, 2) }}</span>
                    </div>

                    <div class="flex justify-between text-gray-600 dark:text-gray-400">
                        <span>Total kg:</span>
                        <span>{{ number_format($totalKg ?? 0, 2) }} kg</span>
                    </div>

                    <div class="rounded-xl border border-gray-200 p-4 dark:border-gray-700">
                        <h3 class="font-bold text-gray-900 dark:text-white">Bulk Discount</h3>
                        @if($appliedDiscount)
                            <p class="mt-2 text-sm font-semibold text-emerald-600 dark:text-emerald-300">
                                Discount Applied: {{ $appliedDiscount['discount_rate'] ?? 0 }}%
                            </p>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                Discount: PHP {{ number_format($discountAmount ?? 0, 2) }}
                            </p>
                            <form method="POST" action="{{ route('cart.remove-discount') }}" class="mt-3">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded-lg border border-red-200 px-3 py-2 text-sm font-bold text-red-700 hover:bg-red-50 dark:border-red-900/60 dark:text-red-300 dark:hover:bg-red-950/30">
                                    Remove Discount
                                </button>
                            </form>
                        @elseif($eligibleDiscount['eligible'] ?? false)
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                Your cart has {{ $eligibleDiscount['minimum_kg'] }} kg or more. You can apply a {{ $eligibleDiscount['discount_rate'] }}% discount.
                            </p>
                            <form method="POST" action="{{ route('cart.apply-discount') }}" class="mt-3">
                                @csrf
                                <button type="submit" class="rounded-lg bg-emerald-700 px-4 py-2 text-sm font-bold text-white hover:bg-emerald-800">
                                    Apply Discount
                                </button>
                            </form>
                        @else
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                No bulk discount available yet. Add more kg to qualify for a discount.
                            </p>
                        @endif
                    </div>

                    @if(($discountAmount ?? 0) > 0)
                        <div class="flex justify-between text-emerald-600 dark:text-emerald-400">
                            <span>Discount:</span>
                            <span>- PHP {{ number_format($discountAmount, 2) }}</span>
                        </div>
                    @endif

                    <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                        <div class="flex justify-between text-lg font-bold text-gray-900 dark:text-white">
                            <span>Total:</span>
                            <span class="text-green-600 dark:text-green-400">PHP {{ number_format(max((float) $cart->total - (float) ($discountAmount ?? 0), 0), 2) }}</span>
                        </div>
                    </div>

                    @if($cart->items->count() > 0)
                        <a href="{{ route('checkout.show') }}" class="block w-full rounded-lg bg-green-600 py-3 text-center font-semibold text-white transition hover:bg-green-700">
                            Review and Checkout
                        </a>
                    @endif

                    <a href="{{ route('consumer.marketplace') }}" class="block w-full rounded-lg bg-gray-200 py-2 text-center font-semibold text-gray-900 transition hover:bg-gray-300 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600">
                        Browse Marketplace
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
