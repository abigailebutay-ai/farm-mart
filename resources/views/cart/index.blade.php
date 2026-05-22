@extends('layouts.app')

@section('page-title', 'Shopping Cart')

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">
            @if($cart->items->count() > 0)
                <div class="bg-white dark:bg-gray-800 rounded-lg overflow-hidden shadow">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Cart Items ({{ $cart->items->count() }})</h2>
                    </div>

                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($cart->items as $item)
                            <div class="p-6 flex gap-6 hover:bg-gray-50 dark:hover:bg-gray-700">
                                <x-ui.product-image
                                    :src="$item->product->image"
                                    :alt="$item->product->name"
                                    image-class="h-24 w-24 rounded-lg object-cover"
                                    placeholder-class="flex h-24 w-24 items-center justify-center rounded-lg bg-gray-200 text-gray-400 dark:bg-gray-700"
                                    icon-class="h-8 w-8"
                                />

                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ $item->product->name }}</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">{{ $item->product->farmer->name }}</p>

                                    <div class="flex justify-between items-end">
                                        <div>
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Price: ₱{{ number_format($item->price, 2) }} / {{ $item->product->unit ?? 'piece' }}</p>
                                            <p class="text-lg font-bold text-green-600 dark:text-green-400">Subtotal: ₱{{ number_format($item->subtotal, 2) }}</p>
                                        </div>

                                        <form method="POST" action="{{ route('cart.update-quantity', $item) }}" class="flex items-end gap-2">
                                            @csrf
                                            @method('PUT')
                                            <div>
                                                <label for="quantity" class="text-xs text-gray-600 dark:text-gray-400">Qty ({{ $item->product->unit ?? 'piece' }})</label>
                                                <input
                                                    type="number"
                                                    name="quantity"
                                                    value="{{ $item->quantity }}"
                                                    min="1"
                                                    class="w-16 px-2 py-1 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-center"
                                                >
                                            </div>
                                            <button type="submit" class="text-blue-600 dark:text-blue-400 hover:text-blue-700 px-2 py-1 text-sm font-semibold">Update</button>
                                        </form>

                                        <form method="POST" action="{{ route('cart.remove', $item) }}" class="ml-2">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-700 px-2 py-1 text-sm font-semibold">Remove</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="px-6 py-4 bg-gray-100 dark:bg-gray-700 flex justify-end">
                        <form method="POST" action="{{ route('cart.clear') }}" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-700 font-semibold" onclick="return confirm('Clear entire cart?')">
                                Clear Cart
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <div class="bg-white dark:bg-gray-800 rounded-lg p-12 text-center">
                    <p class="text-gray-600 dark:text-gray-400 text-lg mb-4">Your cart is empty</p>
                    <a href="{{ route('consumer.marketplace') }}" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition font-semibold inline-block">
                        Continue Shopping
                    </a>
                </div>
            @endif
        </div>

        <!-- Cart Summary -->
        <div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden sticky top-24">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Order Summary</h2>
                </div>

                <div class="p-6 space-y-4">
                    <div class="flex justify-between text-gray-600 dark:text-gray-400">
                        <span>Subtotal:</span>
                        <span>₱{{ number_format($cart->subtotal, 2) }}</span>
                    </div>

                    <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                        <div class="flex justify-between text-lg font-bold text-gray-900 dark:text-white">
                            <span>Total:</span>
                            <span class="text-green-600 dark:text-green-400">₱{{ number_format($cart->total, 2) }}</span>
                        </div>
                    </div>

                    @if($cart->items->count() > 0)
                        <a href="{{ route('checkout.show') }}" class="block w-full bg-green-600 text-white py-3 rounded-lg hover:bg-green-700 transition font-semibold text-center mt-6">
                            Proceed to Checkout
                        </a>
                    @endif

                    <a href="{{ route('consumer.marketplace') }}" class="block w-full bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-white py-2 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition font-semibold text-center">
                        Continue Shopping
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
