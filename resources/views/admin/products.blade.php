@extends('layouts.app')

@section('page-title', 'Products')

@section('content')
    <x-ui.page-header
        title="Products"
        subtitle="View products listed by farmers."
    />

    <div class="mb-5 flex justify-end">
        <x-ui.secondary-button href="{{ route('admin.products.print') }}">
            Print Products Report
        </x-ui.secondary-button>
    </div>

    <x-ui.table-card title="Products" subtitle="Review product details, seller, price, stock, and status.">
        <thead class="bg-slate-50">
            <tr class="text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                <th class="px-5 py-3">Product</th>
                <th class="px-5 py-3">Farmer</th>
                <th class="px-5 py-3">Category</th>
                <th class="px-5 py-3">Price</th>
                <th class="px-5 py-3">Stock</th>
                <th class="px-5 py-3">Status</th>
                <th class="px-5 py-3">Action</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($products as $product)
                @php
                    $stockStatus = $product->quantity > 10 ? 'In Stock' : ($product->quantity > 0 ? 'Low Stock' : 'Out of Stock');
                @endphp
                <tr class="hover:bg-slate-50">
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-3">
                            <x-ui.product-image
                                :product="$product"
                                :alt="$product->name"
                                image-class="h-12 w-12 rounded-xl object-cover"
                                placeholder-class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-50 text-emerald-800"
                                icon-class="h-5 w-5"
                            />
                            <div>
                                <p class="font-bold text-slate-900">{{ $product->name }}</p>
                                <p class="text-xs text-slate-500">#{{ $product->id }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-4 text-sm text-slate-600">{{ $product->farmer->name ?? 'Unknown farmer' }}</td>
                    <td class="px-5 py-4 text-sm text-slate-600">{{ $product->category }}</td>
                    <td class="px-5 py-4 text-sm font-bold text-slate-900">PHP {{ number_format($product->price, 2) }} / kg</td>
                    <td class="px-5 py-4 text-sm text-slate-600">{{ $product->quantity }} kg</td>
                    <td class="px-5 py-4"><x-ui.status-badge :status="$stockStatus" /></td>
                    <td class="px-5 py-4">
                        <x-ui.secondary-button href="{{ route('admin.products.show', $product) }}" class="px-3 py-1.5 text-xs">View Details</x-ui.secondary-button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-5 py-5">
                        <x-ui.empty-state title="No products found" message="Farmer product listings will appear here once products are created." icon="products" />
                    </td>
                </tr>
            @endforelse
        </tbody>
    </x-ui.table-card>

    <div class="mt-8">
        {{ $products->links() }}
    </div>
@endsection
