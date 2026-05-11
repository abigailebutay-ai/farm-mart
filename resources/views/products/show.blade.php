@extends('layouts.app')

@section('page-title', $product->name)

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
        <div>
            @if($product->image)
                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-96 object-cover rounded-lg">
            @else
                <div class="w-full h-96 bg-gray-200 dark:bg-gray-700 flex items-center justify-center rounded-lg">
                    <span class="text-gray-400">No Image Available</span>
                </div>
            @endif
        </div>

        <div class="space-y-6">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Category</p>
                <p class="text-lg text-gray-900 dark:text-white font-semibold">{{ $product->category }}</p>
            </div>

            <div>
                <h1 class="text-4xl font-bold text-gray-900 dark:text-white">{{ $product->name }}</h1>
            </div>

            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Sold by</p>
                <p class="text-lg text-gray-900 dark:text-white font-semibold">{{ $product->farmer->name }}</p>
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $product->farmer->address }}</p>
            </div>

            <div class="bg-gray-100 dark:bg-gray-700 p-6 rounded-lg">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Price</p>
                <p class="text-4xl font-bold text-green-600 dark:text-green-400">₱{{ number_format($product->price, 2) }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Availability</p>
                @if($product->quantity > 0)
                    <p class="text-lg text-green-600 dark:text-green-400 font-semibold">{{ $product->quantity }} in stock</p>
                @else
                    <p class="text-lg text-red-600 dark:text-red-400 font-semibold">Out of Stock</p>
                @endif
            </div>

            @auth
                @if(auth()->user()->isConsumer() && $product->quantity > 0)
                    <form method="POST" action="{{ route('cart.add', $product) }}" class="flex items-end gap-4">
                        @csrf
                        <div class="flex-1">
                            <label for="quantity" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Quantity</label>
                            <input
                                type="number"
                                id="quantity"
                                name="quantity"
                                value="1"
                                min="1"
                                max="{{ $product->quantity }}"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-600"
                            >
                        </div>
                        <button
                            type="submit"
                            class="bg-green-600 text-white px-8 py-2 rounded-lg hover:bg-green-700 transition font-semibold"
                        >
                            Add to Cart
                        </button>
                    </form>
                @endif
            @else
                <a href="{{ route('login') }}" class="block w-full bg-green-600 text-white px-8 py-3 rounded-lg hover:bg-green-700 transition font-semibold text-center">
                    Login to Buy
                </a>
            @endauth
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg p-8">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Product Description</h2>
        <p class="text-gray-600 dark:text-gray-400 leading-relaxed whitespace-pre-wrap">{{ $product->description }}</p>
    </div>
@endsection
