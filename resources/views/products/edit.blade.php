@extends('layouts.app')

@section('page-title', 'Edit Product')

@section('content')
    <div class="max-w-2xl">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-green-50 to-transparent dark:from-gray-700 dark:to-transparent">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">✏️ Edit Product</h2>
            </div>

            <form method="POST" action="{{ route('farmer.products.update', $product) }}" enctype="multipart/form-data" class="p-6 space-y-6">
                @csrf
                @method('PUT')

                @include('layouts.form-input', [
                    'name' => 'name',
                    'label' => 'Product Name',
                    'placeholder' => 'e.g., Fresh Organic Tomatoes',
                    'icon' => '🥕',
                    'required' => true,
                    'value' => old('name', $product->name)
                ])

                @include('layouts.form-textarea', [
                    'name' => 'description',
                    'label' => 'Description',
                    'placeholder' => 'Describe your product, harvesting details, quality, etc.',
                    'required' => true,
                    'rows' => 4,
                    'value' => old('description', $product->description)
                ])

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @include('layouts.form-input', [
                        'name' => 'category',
                        'label' => 'Category',
                        'type' => 'text',
                        'placeholder' => 'e.g., Vegetables, Fruits',
                        'icon' => '📁',
                        'required' => true,
                        'hint' => 'Help customers find your product',
                        'value' => old('category', $product->category)
                    ])

                    @include('layouts.form-input', [
                        'name' => 'price',
                        'label' => 'Price (₱)',
                        'type' => 'number',
                        'placeholder' => '0.00',
                        'icon' => '💰',
                        'required' => true,
                        'step' => '0.01',
                        'min' => '0',
                        'value' => old('price', $product->price)
                    ])
                </div>

                @include('layouts.form-input', [
                    'name' => 'quantity',
                    'label' => 'Available Quantity (units)',
                    'type' => 'number',
                    'placeholder' => '0',
                    'icon' => '📦',
                    'required' => true,
                    'hint' => 'Number of units available',
                    'value' => old('quantity', $product->quantity)
                ])

                <div class="mb-4">
                    <label for="image" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Product Image
                    </label>
                    @if($product->image)
                        <div class="mb-4 relative">
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="h-48 rounded-lg shadow">
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Current image. Upload a new one to replace it.</p>
                        </div>
                    @endif
                    <div class="relative">
                        <input
                            type="file"
                            id="image"
                            name="image"
                            accept="image/*"
                            class="block w-full text-sm text-gray-500 dark:text-gray-400 border border-gray-300 dark:border-gray-600 rounded-lg file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-green-600 file:text-white hover:file:bg-green-700 dark:file:bg-green-600 dark:hover:file:bg-green-700 transition cursor-pointer"
                        />
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">PNG, JPG, or GIF (max. 2MB). Recommended: 500x500px</p>
                </div>

                <div class="flex gap-4 pt-4">
                    @include('layouts.form-button', [
                        'type' => 'submit',
                        'variant' => 'primary',
                        'size' => 'md',
                        'icon' => '✓',
                        'slot' => 'Update Product'
                    ])
                    @include('layouts.form-button', [
                        'href' => route('farmer.products.index'),
                        'variant' => 'secondary',
                        'size' => 'md',
                        'slot' => 'Cancel'
                    ])
                </div>
            </form>
        </div>
    </div>
@endsection
