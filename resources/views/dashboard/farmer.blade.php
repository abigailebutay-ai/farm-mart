@extends('layouts.app')

@section('page-title', 'Farmer Dashboard')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <!-- Total Products Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow hover:shadow-lg transition transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-gray-600 dark:text-gray-400 text-sm font-semibold mb-1">Total Products</h3>
                    <p class="text-3xl font-bold text-green-600 dark:text-green-400">{{ $totalProducts }}</p>
                </div>
                <div class="text-5xl opacity-20">📦</div>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('farmer.products.index') }}" class="text-sm text-green-600 dark:text-green-400 hover:text-green-700 font-medium">View Products →</a>
            </div>
        </div>

        <!-- Total Sales Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow hover:shadow-lg transition transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-gray-600 dark:text-gray-400 text-sm font-semibold mb-1">Total Sales</h3>
                    <p class="text-3xl font-bold text-amber-600 dark:text-amber-400">{{ $totalSales }}</p>
                </div>
                <div class="text-5xl opacity-20">💰</div>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                <p class="text-xs text-gray-600 dark:text-gray-400">This month</p>
            </div>
        </div>

        <!-- Pending Orders Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow hover:shadow-lg transition transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-gray-600 dark:text-gray-400 text-sm font-semibold mb-1">Pending Orders</h3>
                    <p class="text-3xl font-bold text-yellow-600 dark:text-yellow-400">{{ $pendingOrders }}</p>
                </div>
                <div class="text-5xl opacity-20">⏳</div>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('orders.index') }}" class="text-sm text-yellow-600 dark:text-yellow-400 hover:text-yellow-700 font-medium">View Orders →</a>
            </div>
        </div>

        <!-- Quick Actions Card -->
        <div class="bg-gradient-to-br from-green-50 to-green-100 dark:from-gray-800 dark:to-gray-700 rounded-lg p-6 shadow hover:shadow-lg transition">
            <h3 class="text-gray-600 dark:text-gray-400 text-sm font-semibold mb-3">Quick Actions</h3>
            <a href="{{ route('farmer.products.create') }}" class="block w-full px-3 py-2 bg-green-600 text-white text-sm font-medium rounded hover:bg-green-700 transition text-center mb-2">
                ➕ Add Product
            </a>
            <a href="{{ route('orders.index') }}" class="block w-full px-3 py-2 bg-amber-600 text-white text-sm font-medium rounded hover:bg-amber-700 transition text-center">
                📋 View Orders
            </a>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-green-50 to-transparent dark:from-gray-700 dark:to-transparent">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">📋 Recent Orders</h2>
        </div>

        @if($recentOrders->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-100 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Order ID</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Customer</th>
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
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ $order->consumer->name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ $order->items()->where('farmer_id', auth()->id())->count() }}</td>
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
        @else
            <div class="px-6 py-12 text-center">
                <p class="text-2xl mb-4">🌾</p>
                <p class="text-gray-600 dark:text-gray-400">No orders yet. Start selling your products!</p>
                <a href="{{ route('farmer.products.create') }}" class="mt-4 inline-block px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">Add Your First Product</a>
            </div>
        @endif
    </div>
@endsection

