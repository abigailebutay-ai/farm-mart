@extends('layouts.app')

@section('page-title', 'Add New Product')

@section('content')
    <div class="max-w-2xl">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Create New Product</h2>
            </div>

            <form method="POST" action="{{ route('farmer.products.store') }}" enctype="multipart/form-data" class="p-6 space-y-6">
                @csrf

                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Product Name *</label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name') }}"
                        required
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-600 @error('name') border-red-500 @enderror"
                    >
                    @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Description *</label>
                    <textarea
                        id="description"
                        name="description"
                        rows="4"
                        required
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-600 @error('description') border-red-500 @enderror"
                    >{{ old('description') }}</textarea>
                    @error('description')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="category" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Category *</label>
                        <input
                            type="text"
                            id="category"
                            name="category"
                            value="{{ old('category') }}"
                            required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-600 @error('category') border-red-500 @enderror"
                            placeholder="e.g., Vegetables, Fruits, Dairy"
                        >
                        @error('category')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="price" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Price (₱) *</label>
                        <input
                            type="number"
                            id="price"
                            name="price"
                            value="{{ old('price') }}"
                            step="0.01"
                            min="0"
                            required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-600 @error('price') border-red-500 @enderror"
                        >
                        @error('price')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label for="quantity" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Quantity (pcs/units) *</label>
                    <input
                        type="number"
                        id="quantity"
                        name="quantity"
                        value="{{ old('quantity') }}"
                        min="1"
                        required
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-600 @error('quantity') border-red-500 @enderror"
                    >
                    @error('quantity')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="image" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Product Image</label>
                    <input
                        type="file"
                        id="image"
                        name="image"
                        accept="image/*"
                        class="block w-full text-sm text-gray-500 dark:text-gray-400 border border-gray-300 dark:border-gray-600 rounded-lg file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 dark:file:bg-green-900 dark:file:text-green-200"
                    >
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">PNG, JPG or GIF (max. 2MB)</p>
                </div>

                <div class="flex gap-4">
                    <button
                        type="submit"
                        class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition font-semibold"
                    >
                        Add Product
                    </button>
                    <a href="{{ route('farmer.products.index') }}" class="bg-gray-300 dark:bg-gray-700 text-gray-900 dark:text-white px-6 py-2 rounded-lg hover:bg-gray-400 dark:hover:bg-gray-600 transition font-semibold">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
