@extends('layouts.app')

@section('title', 'Home')

@section('content')
    <div class="space-y-12">
        <!-- Hero Section -->
        <div class="bg-gradient-to-r from-green-600 to-green-500 rounded-lg p-8 md:p-12 text-white">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">Welcome to Farmers Marketplace</h1>
            <p class="text-lg mb-6 opacity-90">Connect directly with local farmers and get fresh, quality products delivered to your doorstep.</p>
            @guest
                <a href="{{ route('register') }}" class="inline-block bg-white text-green-600 px-6 py-3 rounded-lg font-semibold hover:bg-green-50 transition">Get Started</a>
            @endguest
        </div>

        <!-- Featured Products -->
        <div>
            <h2 class="text-3xl font-bold mb-8 text-gray-900 dark:text-white">Featured Products</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($featuredProducts as $product)
                    <a href="{{ route('products.show', $product) }}" class="bg-white dark:bg-gray-800 rounded-lg overflow-hidden shadow hover:shadow-lg transition">
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-48 object-cover">
                        @else
                            <div class="w-full h-48 bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                <span class="text-gray-400">No Image</span>
                            </div>
                        @endif
                        <div class="p-4">
                            <h3 class="font-semibold text-gray-900 dark:text-white mb-2">{{ $product->name }}</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-3 line-clamp-2">{{ $product->description }}</p>
                            <div class="flex justify-between items-center">
                                <span class="text-lg font-bold text-green-600 dark:text-green-400">₱{{ number_format($product->price, 2) }}</span>
                                <span class="text-sm text-gray-500 dark:text-gray-400">{{ $product->quantity }} left</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Info Section -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white dark:bg-gray-800 p-8 rounded-lg shadow">
                <div class="text-4xl mb-4">👨‍🌾</div>
                <h3 class="text-xl font-bold mb-2 text-gray-900 dark:text-white">For Farmers</h3>
                <p class="text-gray-600 dark:text-gray-400">Sell your products directly to consumers without intermediaries. Keep more of your earnings!</p>
            </div>
            <div class="bg-white dark:bg-gray-800 p-8 rounded-lg shadow">
                <div class="text-4xl mb-4">🛒</div>
                <h3 class="text-xl font-bold mb-2 text-gray-900 dark:text-white">For Consumers</h3>
                <p class="text-gray-600 dark:text-gray-400">Buy fresh vegetables, fruits, and farm products directly from farmers. Get quality guaranteed!</p>
            </div>
            <div class="bg-white dark:bg-gray-800 p-8 rounded-lg shadow">
                <div class="text-4xl mb-4">🤝</div>
                <h3 class="text-xl font-bold mb-2 text-gray-900 dark:text-white">Direct Connection</h3>
                <p class="text-gray-600 dark:text-gray-400">Create a direct marketplace between farmers and consumers for better prices and quality.</p>
            </div>
        </div>
    </div>
@endsection
