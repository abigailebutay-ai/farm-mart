@extends('layouts.app')

@section('page-title', 'Orders')

@section('content')
    @if($orders->count() > 0)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-100 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Order ID</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">
                                @if(auth()->user()->isFarmer())
                                    Customer
                                @else
                                    Items
                                @endif
                            </th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Total</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Status</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Date</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($orders as $order)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900 dark:text-white">#{{ $order->id }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                    @if(auth()->user()->isFarmer())
                                        {{ $order->consumer->name }}
                                    @else
                                        {{ $order->items()->count() }} items
                                    @endif
                                </td>
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
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $order->created_at->format('M d, Y h:i A') }}</td>
                                <td class="px-6 py-4 text-sm">
                                    <a href="{{ route('orders.show', $order) }}" class="text-green-600 dark:text-green-400 hover:text-green-700 font-semibold">View</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-8">
            {{ $orders->links() }}
        </div>
    @else
        <div class="bg-white dark:bg-gray-800 rounded-lg p-12 text-center">
            <p class="text-gray-600 dark:text-gray-400 text-lg mb-4">
                @if(auth()->user()->isFarmer())
                    No orders yet. Keep an eye out for new customer orders!
                @else
                    You haven't placed any orders yet.
                @endif
            </p>
            @if(auth()->user()->isConsumer())
                <a href="{{ route('products.index') }}" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition font-semibold inline-block">
                    Start Shopping
                </a>
            @endif
        </div>
    @endif
@endsection
