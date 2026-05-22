@extends('layouts.app')

@section('page-title', 'Checkout')

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Checkout</h2>
                </div>

                <form method="POST" action="{{ route('checkout.store') }}" class="p-6 space-y-6">
                    @csrf

                    <!-- Order Items Review -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Order Items</h3>
                        <div class="space-y-4">
                            @foreach($cart->items as $item)
                                <div class="flex justify-between items-center p-4 bg-gray-100 dark:bg-gray-700 rounded-lg">
                                    <div>
                                        <p class="font-semibold text-gray-900 dark:text-white">{{ $item->product->name }}</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Qty: {{ $item->quantity }} {{ $item->product->unit ?? 'piece' }} x ₱{{ number_format($item->price, 2) }} / {{ $item->product->unit ?? 'piece' }}</p>
                                    </div>
                                    <p class="font-semibold text-gray-900 dark:text-white">₱{{ number_format($item->subtotal, 2) }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Special Instructions -->
                    <div>
                        <label for="notes" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Special Instructions (Optional)</label>
                        <textarea
                            id="notes"
                            name="notes"
                            rows="3"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-600"
                            placeholder="Any special requests or delivery instructions?"
                        ></textarea>
                    </div>

                    <!-- Delivery Info -->
                    <div class="bg-blue-50 dark:bg-blue-900 p-4 rounded-lg">
                        <h3 class="font-semibold text-blue-900 dark:text-blue-200 mb-2">Delivery Information</h3>
                        <p class="text-sm text-blue-800 dark:text-blue-300">
                            <strong>Name:</strong> {{ auth()->user()->name }}<br>
                            <strong>Address:</strong> {{ auth()->user()->address ?? 'Not provided' }}<br>
                            <strong>Phone:</strong> {{ auth()->user()->phone ?? 'Not provided' }}
                        </p>
                        <p class="text-xs text-blue-700 dark:text-blue-400 mt-2">
                            <a href="{{ route('profile.edit') }}" class="underline font-semibold">Update delivery details</a>
                        </p>
                    </div>

                    <div class="flex gap-4">
                        <button
                            type="submit"
                            class="flex-1 bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition font-semibold"
                        >
                            Place Order
                        </button>
                        <a href="{{ route('cart.index') }}" class="flex-1 bg-gray-300 dark:bg-gray-700 text-gray-900 dark:text-white px-6 py-3 rounded-lg hover:bg-gray-400 dark:hover:bg-gray-600 transition font-semibold text-center">
                            Back to Cart
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Order Summary -->
        <div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden sticky top-24">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Order Summary</h2>
                </div>

                <div class="p-6 space-y-4">
                    <div class="flex justify-between text-gray-600 dark:text-gray-400">
                        <span>Total Items:</span>
                        <span>{{ $cart->items->sum('quantity') }}</span>
                    </div>

                    <div class="flex justify-between text-gray-600 dark:text-gray-400">
                        <span>Subtotal:</span>
                        <span>₱{{ number_format($cart->subtotal, 2) }}</span>
                    </div>

                    <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                        <div class="flex justify-between text-xl font-bold text-gray-900 dark:text-white">
                            <span>Grand Total:</span>
                            <span class="text-green-600 dark:text-green-400">₱{{ number_format($cart->total, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
