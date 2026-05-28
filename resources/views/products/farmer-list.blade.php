@extends('layouts.app')

@section('page-title', 'My Products')

@section('content')
    <x-ui.page-header
        title="My Products"
        subtitle="Add, edit, and manage the products you sell."
        action-url="{{ route('farmer.products.create') }}"
        action-label="Add Product"
    />

    @if($products->count() > 0)
        <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-100">
            <div class="overflow-x-auto">
                <table class="product-table w-full">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wide text-gray-500">Product</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wide text-gray-500">Category</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wide text-gray-500">Price</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wide text-gray-500">Stock Status</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wide text-gray-500">Created</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wide text-gray-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($products as $product)
                            <tr class="product-row">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <x-ui.product-image
                                            :product="$product"
                                            :alt="$product->name"
                                            image-class="h-10 w-10 rounded object-cover"
                                            placeholder-class="flex h-10 w-10 items-center justify-center rounded bg-emerald-50 text-emerald-800"
                                            icon-class="h-5 w-5"
                                        />
                                        <div>
                                            <p class="product-name font-semibold">{{ $product->name }}</p>
                                            <p class="product-description text-sm">{{ \Illuminate\Support\Str::limit($product->description, 50) }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="product-category px-6 py-4">{{ $product->category }}</td>
                                <td class="px-6 py-4 font-semibold text-green-800">PHP {{ number_format($product->price, 2) }} / {{ $product->unit ?? 'piece' }}</td>
                                <td class="px-6 py-4">
                                    <x-ui.status-badge :status="$product->status === 'inactive' ? 'Archived' : ($product->quantity > 10 ? 'In Stock' : ($product->quantity > 0 ? 'Low Stock' : 'Out of Stock'))" />
                                    <span class="product-stock ml-2 text-sm">{{ $product->quantity }} {{ $product->unit ?? 'piece' }}</span>
                                </td>
                                <td class="product-date px-6 py-4">{{ $product->created_at->format('M d, Y') }}</td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('farmer.products.edit', $product) }}" class="mr-3 font-semibold text-green-800 hover:text-green-900">Edit Product</a>
                                    <form method="POST" action="{{ route('farmer.products.destroy', $product) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="font-semibold text-red-600 hover:text-red-700" onclick="return confirm('Products with order history will be archived instead of permanently deleted. Continue?')">{{ ($product->order_items_count ?? 0) > 0 ? 'Archive' : 'Delete' }} Product</button>
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
        <x-ui.empty-state title="No products yet" message="You have not added any products yet." action-url="{{ route('farmer.products.create') }}" action-label="Add Product" />
    @endif
@endsection
