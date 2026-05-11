@extends('layouts.app')

@section('page-title', 'Order #' . $order->id)

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-6">
            <!-- Order Status -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Order #{{ $order->id }}</h2>
                </div>

                <div class="p-6">
                    <div class="mb-6">
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Status</p>
                        <span class="px-4 py-2 rounded-full font-semibold inline-block
                            @if($order->status === 'pending') bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200
                            @elseif($order->status === 'accepted') bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200
                            @elseif($order->status === 'completed') bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200
                            @else bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 @endif">
                            {{ Str::ucfirst($order->status) }}
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-600 dark:text-gray-400 mb-1">Order Date</p>
                            <p class="font-semibold text-gray-900 dark:text-white">{{ $order->created_at->format('M d, Y h:i A') }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600 dark:text-gray-400 mb-1">Last Updated</p>
                            <p class="font-semibold text-gray-900 dark:text-white">{{ $order->updated_at->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Order Items</h3>
                </div>

                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($order->items as $item)
                        <div class="p-6 flex gap-6">
                            @if($item->product->image)
                                <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product->name }}" class="w-20 h-20 object-cover rounded-lg">
                            @else
                                <div class="w-20 h-20 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                                    <span class="text-gray-400 text-xs">No img</span>
                                </div>
                            @endif

                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-900 dark:text-white mb-1">{{ $item->product->name }}</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                                    From: <strong>{{ $item->farmer->name }}</strong>
                                </p>
                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                    <p>Qty: {{ $item->quantity }} x ₱{{ number_format($item->price, 2) }}</p>
                                </div>
                            </div>

                            <div class="text-right">
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Subtotal</p>
                                <p class="text-lg font-bold text-green-600 dark:text-green-400">₱{{ number_format($item->subtotal, 2) }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Customer Info -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
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

            <!-- Order Status Update (Farmer Only) -->
            @if(auth()->user()->isFarmer() && $order->items()->where('farmer_id', auth()->id())->exists())
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Update Order Status</h3>
                    </div>

                    <form method="POST" action="{{ route('orders.update-status', $order) }}" class="p-6">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="status" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">New Status</label>
                            <select
                                id="status"
                                name="status"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-600"
                            >
                                <option value="pending" @selected($order->status === 'pending')>Pending</option>
                                <option value="accepted" @selected($order->status === 'accepted')>Accepted</option>
                                <option value="completed" @selected($order->status === 'completed')>Completed</option>
                                <option value="cancelled" @selected($order->status === 'cancelled')>Cancelled</option>
                            </select>
                        </div>

                        <button
                            type="submit"
                            class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition font-semibold"
                        >
                            Update Status
                        </button>
                    </form>
                </div>
            @endif
        </div>

        <!-- Order Summary Sidebar -->
        <div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden sticky top-24">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Order Summary</h2>
                </div>

                <div class="p-6 space-y-4">
                    <div class="flex justify-between text-gray-600 dark:text-gray-400">
                        <span>Total Items:</span>
                        <span>{{ $order->items()->count() }}</span>
                    </div>

                    <div class="flex justify-between text-gray-600 dark:text-gray-400">
                        <span>Subtotal:</span>
                        <span>₱{{ number_format($order->subtotal, 2) }}</span>
                    </div>

                    <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                        <div class="flex justify-between text-lg font-bold text-gray-900 dark:text-white">
                            <span>Total:</span>
                            <span class="text-green-600 dark:text-green-400">₱{{ number_format($order->total, 2) }}</span>
                        </div>
                    </div>

                    <div class="pt-2">
                        <a href="{{ route('orders.index') }}" class="block w-full bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-white py-2 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition font-semibold text-center text-sm">
                            Back to Orders
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
