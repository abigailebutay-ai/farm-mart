@extends('layouts.app')

@section('page-title', 'Consumer Dashboard')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Cart Items Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow hover:shadow-lg transition transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-gray-600 dark:text-gray-400 text-sm font-semibold mb-1">Items in Cart</h3>
                    <p class="text-3xl font-bold text-green-600 dark:text-green-400">{{ $cartItemsCount }}</p>
                </div>
                <div class="text-5xl opacity-20">🛒</div>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('cart.index') }}" class="text-sm text-green-600 dark:text-green-400 hover:text-green-700 font-medium">View Cart →</a>
            </div>
        </div>

        <!-- Orders Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow hover:shadow-lg transition transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-gray-600 dark:text-gray-400 text-sm font-semibold mb-1">Orders Placed</h3>
                    <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ $recentOrders->count() }}</p>
                </div>
                <div class="text-5xl opacity-20">📋</div>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('orders.index') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 font-medium">View Orders →</a>
            </div>
        </div>

        <!-- Browse Products Card -->
        <div class="bg-gradient-to-br from-green-50 to-green-100 dark:from-gray-800 dark:to-gray-700 rounded-lg p-6 shadow hover:shadow-lg transition">
            <h3 class="text-gray-600 dark:text-gray-400 text-sm font-semibold mb-3">Explore</h3>
            <a href="{{ route('products.index') }}" class="block w-full px-3 py-2 bg-green-600 text-white text-sm font-medium rounded hover:bg-green-700 transition text-center">
                🛍️ Browse Products
            </a>
        </div>
    </div>

    @if($recentOrders->count() > 0)
        <div class="mb-8 bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-green-50 to-transparent dark:from-gray-700 dark:to-transparent">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">📋 Your Recent Orders</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-100 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Order ID</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Items</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Total</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Status</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Date</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($recentOrders as $order)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-white font-medium">#{{ $order->id }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ $order->items()->count() }}</td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900 dark:text-white">₱{{ number_format($order->total, 2) }}</td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold
                                        @if($order->status === 'pending') bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200
                                        @elseif($order->status === 'accepted') bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200
                                        @elseif($order->status === 'completed') bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200
                                        @else bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 @endif">
                                        {{ Str::ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $order->created_at->format('M d, Y') }}</td>
                                <td class="px-6 py-4 text-sm">
                                    <a href="{{ route('orders.show', $order) }}" class="text-green-600 dark:text-green-400 hover:text-green-700 font-semibold transition">View</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <div>
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">✨ Recommended Products</h2>
            <a href="{{ route('products.index') }}" class="text-green-600 dark:text-green-400 font-semibold hover:text-green-700 transition">View All →</a>
        </div>

        @if($recommendedProducts->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($recommendedProducts as $product)
                    <div class="bg-white dark:bg-gray-800 rounded-lg overflow-hidden shadow hover:shadow-lg transition transform hover:-translate-y-1">
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-48 object-cover">
                        @else
                            <div class="w-full h-48 bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                <span class="text-4xl">🌾</span>
                            </div>
                        @endif
                        <div class="p-4">
                            <h3 class="font-semibold text-gray-900 dark:text-white mb-2">{{ $product->name }}</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-3 line-clamp-2">{{ $product->description }}</p>
                            <div class="flex justify-between items-center mb-4">
                                <span class="text-lg font-bold text-green-600 dark:text-green-400">₱{{ number_format($product->price, 2) }}</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">{{ $product->quantity }} left</span>
                            </div>
                            <form method="POST" action="{{ route('cart.add', $product) }}" class="flex gap-2">
                                @csrf
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="flex-1 bg-green-600 text-white py-2 rounded hover:bg-green-700 transition font-semibold">
                                    Add to Cart
                                </button>
                                <a href="{{ route('products.show', $product) }}" class="flex-1 bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-white py-2 rounded hover:bg-gray-300 dark:hover:bg-gray-600 transition font-semibold text-center">
                                    View
                                </a>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white dark:bg-gray-800 rounded-lg p-12 text-center">
                <p class="text-2xl mb-4">🌾</p>
                <p class="text-gray-600 dark:text-gray-400 mb-4">No products available at the moment</p>
                <a href="{{ route('products.index') }}" class="inline-block px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">Browse All Products</a>
            </div>
        @endif
    </div>
@endsection
