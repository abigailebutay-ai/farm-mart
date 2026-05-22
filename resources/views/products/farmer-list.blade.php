@extends('layouts.app')

@section('page-title', 'My Products')

@section('content')
    <x-ui.page-header
        title="My Products"
        subtitle="Manage product listing information such as names, categories, prices, descriptions, images, and listing status."
        action-url="{{ route('farmer.products.create') }}"
        action-label="Add New Product"
    />

    @if($products->count() > 0)
        <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-100">
            <div class="overflow-x-auto">
                <table class="product-table w-full">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Product</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Category</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Price</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Listing Status</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Created</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($products as $product)
                            <tr class="product-row">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <x-ui.product-image
                                            :product="$product"
                                            image-class="h-10 w-10 rounded object-cover"
                                            placeholder-class="flex h-10 w-10 items-center justify-center rounded bg-emerald-50 text-emerald-800"
                                            icon-class="h-5 w-5"
                                        />
                                        <div>
                                            <p class="product-name font-semibold">{{ $product->name }}</p>
                                            <p class="product-description text-xs">{{ \Illuminate\Support\Str::limit($product->description, 40) }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="product-category px-6 py-4 text-sm">{{ $product->category }}</td>
                                <td class="px-6 py-4 text-sm font-semibold text-green-800">PHP {{ number_format($product->price, 2) }} / {{ $product->unit ?? 'piece' }}</td>
                                <td class="px-6 py-4 text-sm">
                                    <x-ui.status-badge :status="$product->quantity > 0 ? 'Active' : 'Inactive'" />
                                    <span class="product-stock ml-2 text-xs">{{ $product->quantity }} {{ $product->unit ?? 'piece' }}</span>
                                </td>
                                <td class="product-date px-6 py-4 text-sm">{{ $product->created_at->format('M d, Y') }}</td>
                                <td class="px-6 py-4 text-sm">
                                    <a href="{{ route('farmer.products.edit', $product) }}" class="mr-3 font-semibold text-green-800 hover:text-green-900">Edit</a>
                                    <form method="POST" action="{{ route('farmer.products.destroy', $product) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="font-semibold text-red-600 hover:text-red-700" onclick="return confirm('Are you sure?')">Delete</button>
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
        <x-ui.empty-state title="No products yet" message="Add your first product to start selling in the marketplace." action-url="{{ route('farmer.products.create') }}" action-label="Add Your First Product" />
    @endif
@endsection
