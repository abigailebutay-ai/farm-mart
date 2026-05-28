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

                    <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                        <div class="flex justify-between text-lg font-bold text-gray-900 dark:text-white">
                            <span>Total:</span>
                            <span class="text-green-600 dark:text-green-400">PHP {{ number_format($cart->total, 2) }}</span>
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
