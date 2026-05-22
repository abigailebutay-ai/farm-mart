@extends('layouts.app')

@section('page-title', 'Edit Product')

@section('content')
    @php($units = \App\Models\Product::UNITS)

    <x-ui.page-header
        title="Edit Product"
        subtitle="Update marketplace details, stock quantity, and pricing for inventory monitoring."
    />

    <x-ui.dashboard-card class="max-w-3xl" title="Product Information">
        <form method="POST" action="{{ route('farmer.products.update', $product) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="mb-2 block text-sm font-semibold text-gray-700">Product Name</label>
                <input id="name" name="name" type="text" value="{{ old('name', $product->name) }}" required placeholder="Example: Fresh Organic Tomatoes" class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-gray-800 focus:border-green-700 focus:outline-none focus:ring-2 focus:ring-green-100">
                @error('name')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="description" class="mb-2 block text-sm font-semibold text-gray-700">Description</label>
                <textarea id="description" name="description" rows="4" required placeholder="Describe quality, harvest details, packaging, or pickup notes." class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-gray-800 focus:border-green-700 focus:outline-none focus:ring-2 focus:ring-green-100">{{ old('description', $product->description) }}</textarea>
                @error('description')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="grid gap-5 md:grid-cols-4">
                <div>
                    <label for="category" class="mb-2 block text-sm font-semibold text-gray-700">Category</label>
                    <input id="category" name="category" type="text" value="{{ old('category', $product->category) }}" required placeholder="Vegetables, Fruits" class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-gray-800 focus:border-green-700 focus:outline-none focus:ring-2 focus:ring-green-100">
                    @error('category')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="price" class="mb-2 block text-sm font-semibold text-gray-700">Price per Unit (PHP)</label>
                    <input id="price" name="price" type="number" step="0.01" min="0" value="{{ old('price', $product->price) }}" required placeholder="0.00" class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-gray-800 focus:border-green-700 focus:outline-none focus:ring-2 focus:ring-green-100">
                    @error('price')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="quantity" class="mb-2 block text-sm font-semibold text-gray-700">Available Quantity</label>
                    <input id="quantity" name="quantity" type="number" min="0" value="{{ old('quantity', $product->quantity) }}" required placeholder="10" class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-gray-800 focus:border-green-700 focus:outline-none focus:ring-2 focus:ring-green-100">
                    @error('quantity')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="unit" class="mb-2 block text-sm font-semibold text-gray-700">Unit</label>
                    <select id="unit" name="unit" required class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-gray-800 focus:border-green-700 focus:outline-none focus:ring-2 focus:ring-green-100">
                        @foreach(\App\Models\Product::UNITS as $unit)
                            <option value="{{ $unit }}" @selected(old('unit', $product->unit ?? 'piece') === $unit)>{{ $unit }}</option>
                        @endforeach
                    </select>
                    @error('unit')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label for="image" class="mb-2 block text-sm font-semibold text-gray-700">Product Image</label>
                @if($product->image)
                    <div class="mb-4">
                        <x-ui.product-image
                            :product="$product"
                            image-class="h-48 rounded-lg object-cover shadow-sm"
                            placeholder-class="flex h-48 w-48 items-center justify-center rounded-lg bg-emerald-50 text-emerald-800 shadow-sm"
                            icon-class="h-12 w-12"
                        />
                        <p class="mt-2 text-sm text-gray-500">Current image. Upload a new file only when replacing it.</p>
                    </div>
                @endif
                <input id="image" name="image" type="file" accept="image/*" class="block w-full rounded-lg border border-gray-200 text-sm text-gray-500 file:mr-4 file:border-0 file:bg-green-800 file:px-4 file:py-2.5 file:text-sm file:font-semibold file:text-white hover:file:bg-green-900">
                @error('image')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="flex flex-col gap-3 border-t border-gray-100 pt-5 sm:flex-row">
                <x-ui.primary-button>Update Product</x-ui.primary-button>
                <x-ui.secondary-button href="{{ route('farmer.products.index') }}">Cancel</x-ui.secondary-button>
            </div>
        </form>
    </x-ui.dashboard-card>
@endsection
