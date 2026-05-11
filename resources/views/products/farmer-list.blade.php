@extends('layouts.app')

@section('page-title', 'My Products')

@section('content')
    <div class="mb-6">
        <a href="{{ route('farmer.products.create') }}" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition font-semibold inline-block">
            ➕ Add New Product
        </a>
    </div>

    @if($products->count() > 0)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-100 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Product</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Category</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Price</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Quantity</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Created</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($products as $product)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3">
                                        @if($product->image)
                                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-10 h-10 rounded object-cover">
                                        @else
                                            <div class="w-10 h-10 rounded bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-xs">No img</div>
                                        @endif
                                        <div>
                                            <p class="font-semibold text-gray-900 dark:text-white">{{ $product->name }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ Str::limit($product->description, 40) }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ $product->category }}</td>
                                <td class="px-6 py-4 text-sm font-semibold text-green-600 dark:text-green-400">₱{{ number_format($product->price, 2) }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold @if($product->quantity > 0) bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 @else bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 @endif">
                                        {{ $product->quantity }} left
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $product->created_at->format('M d, Y') }}</td>
                                <td class="px-6 py-4 text-sm space-x-2">
                                    <a href="{{ route('farmer.products.edit', $product) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-700 font-semibold">Edit</a>
                                    <form method="POST" action="{{ route('farmer.products.destroy', $product) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-700 font-semibold" onclick="return confirm('Are you sure?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-8">
            {{ $products->links() }}
        </div>
    @else
        <div class="bg-white dark:bg-gray-800 rounded-lg p-12 text-center">
            <p class="text-gray-600 dark:text-gray-400 mb-4">You haven't added any products yet.</p>
            <a href="{{ route('farmer.products.create') }}" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition font-semibold inline-block">
                Add Your First Product
            </a>
        </div>
    @endif
@endsection
