@extends('layouts.app')

@section('page-title', 'Browse Products')

@section('content')
    <div class="flex flex-col md:flex-row gap-6 mb-6">
        <div class="flex-1">
            <form method="GET" action="{{ route('products.index') }}" class="space-y-4">
                <div>
                    <input
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Search products..."
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-600"
                    >
                </div>

                <div>
                    <select
                        name="category"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-600"
                    >
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->category }}" @selected(request('category') === $cat->category)>
                                {{ $cat->category }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button
                    type="submit"
                    class="w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition font-semibold"
                >
                    Search
                </button>
            </form>
        </div>
    </div>

    @if($products->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            @foreach($products as $product)
                <div class="bg-white dark:bg-gray-800 rounded-lg overflow-hidden shadow hover:shadow-lg transition">
                    @if($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-48 object-cover">
                    @else
                        <div class="w-full h-48 bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                            <span class="text-gray-400">No Image</span>
                        </div>
                    @endif
                    <div class="p-4">
                        <h3 class="font-semibold text-gray-900 dark:text-white mb-2">{{ $product->name }}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">{{ $product->farmer->name }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-3 line-clamp-2">{{ $product->description }}</p>
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-lg font-bold text-green-600 dark:text-green-400">₱{{ number_format($product->price, 2) }}</span>
                            <span class="text-sm text-gray-500 dark:text-gray-400">{{ $product->quantity }} left</span>
                        </div>

                        @auth
                            @if(auth()->user()->isConsumer())
                                <form method="POST" action="{{ route('cart.add', $product) }}" class="flex gap-2">
                                    @csrf
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="flex-1 bg-green-600 text-white py-2 rounded hover:bg-green-700 transition font-semibold text-sm">
                                        Add to Cart
                                    </button>
                                    <a href="{{ route('products.show', $product) }}" class="flex-1 bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-white py-2 rounded hover:bg-gray-300 dark:hover:bg-gray-600 transition font-semibold text-sm text-center">
                                        View
                                    </a>
                                </form>
                            @else
                                <a href="{{ route('products.show', $product) }}" class="block w-full bg-green-600 text-white py-2 rounded hover:bg-green-700 transition font-semibold text-center">
                                    View Details
                                </a>
                            @endif
                        @else
                            <a href="{{ route('products.show', $product) }}" class="block w-full bg-green-600 text-white py-2 rounded hover:bg-green-700 transition font-semibold text-center">
                                View Details
                            </a>
                        @endauth
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $products->links() }}
        </div>
    @else
        <div class="bg-white dark:bg-gray-800 rounded-lg p-12 text-center">
            <p class="text-gray-600 dark:text-gray-400 text-lg">No products found. Try adjusting your search filters.</p>
        </div>
    @endif
@endsection
