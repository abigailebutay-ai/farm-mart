@extends('layouts.app')

@section('page-title', 'Add New Product')

@section('content')
    @php($units = \App\Models\Product::UNITS)

    <x-ui.page-header
        title="Add Product"
        subtitle="Create a marketplace listing with clear inventory, pricing, and product details for buyers."
    />

    <x-ui.dashboard-card class="max-w-3xl" title="Product Information">
        <form method="POST" action="{{ route('farmer.products.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div>
                <label for="name" class="mb-2 block text-sm font-semibold text-gray-700">Product Name</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" required placeholder="Example: Fresh Organic Tomatoes" class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-gray-800 focus:border-green-700 focus:outline-none focus:ring-2 focus:ring-green-100">
                @error('name')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="description" class="mb-2 block text-sm font-semibold text-gray-700">Description</label>
                <textarea id="description" name="description" rows="4" required placeholder="Describe quality, harvest details, packaging, or pickup notes." class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-gray-800 focus:border-green-700 focus:outline-none focus:ring-2 focus:ring-green-100">{{ old('description') }}</textarea>
                @error('description')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="grid gap-5 md:grid-cols-4">
                <div>
                    <label for="category" class="mb-2 block text-sm font-semibold text-gray-700">Category</label>
                    <input id="category" name="category" type="text" value="{{ old('category') }}" required placeholder="Vegetables, Fruits" class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-gray-800 focus:border-green-700 focus:outline-none focus:ring-2 focus:ring-green-100">
                    @error('category')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="price" class="mb-2 block text-sm font-semibold text-gray-700">Price per Unit (PHP)</label>
                    <input id="price" name="price" type="number" step="0.01" min="0" value="{{ old('price') }}" required placeholder="0.00" class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-gray-800 focus:border-green-700 focus:outline-none focus:ring-2 focus:ring-green-100">
                    @error('price')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="quantity" class="mb-2 block text-sm font-semibold text-gray-700">Available Quantity</label>
                    <input id="quantity" name="quantity" type="number" min="1" value="{{ old('quantity') }}" required placeholder="10" class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-gray-800 focus:border-green-700 focus:outline-none focus:ring-2 focus:ring-green-100">
                    @error('quantity')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="unit" class="mb-2 block text-sm font-semibold text-gray-700">Unit</label>
                    <select id="unit" name="unit" required class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-gray-800 focus:border-green-700 focus:outline-none focus:ring-2 focus:ring-green-100">
                        @foreach($units as $unit)
                            <option value="{{ $unit }}" @selected(old('unit', 'piece') === $unit)>{{ $unit }}</option>
                        @endforeach
                    </select>
                    @error('unit')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label for="image" class="mb-2 block text-sm font-semibold text-gray-700">Product Image</label>
                <input id="image" name="image" type="file" accept="image/*" class="block w-full rounded-lg border border-gray-200 text-sm text-gray-500 file:mr-4 file:border-0 file:bg-green-800 file:px-4 file:py-2.5 file:text-sm file:font-semibold file:text-white hover:file:bg-green-900">
                <p class="mt-2 text-xs text-gray-500">PNG, JPG, or GIF. Use a clear product photo when available.</p>
                @error('image')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="flex flex-col gap-3 border-t border-gray-100 pt-5 sm:flex-row">
                <x-ui.primary-button>Add Product</x-ui.primary-button>
                <x-ui.secondary-button href="{{ route('farmer.products.index') }}">Cancel</x-ui.secondary-button>
            </div>
        </form>
    </x-ui.dashboard-card>
@endsection
