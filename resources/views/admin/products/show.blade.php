@extends('layouts.app')

@section('page-title', 'Product Details')

@section('content')
    @php
        $stockStatus = $product->quantity > 10 ? 'In Stock' : ($product->quantity > 0 ? 'Low Stock' : 'Out of Stock');
        $listingStatus = $product->status ?? ($product->quantity > 0 ? 'Active' : 'Out of Stock');
        $unit = 'kg';
    @endphp

    <x-ui.page-header
        title="Product Details"
        subtitle="Review the product, farmer, price, stock, and status."
    />

    <div class="grid gap-5 xl:grid-cols-[0.85fr_1.15fr]">
        <x-ui.dashboard-card title="Product Image" subtitle="Current product listing image.">
            <x-ui.product-image
                :product="$product"
                :alt="$product->name"
                image-class="h-80 w-full rounded-2xl object-cover"
                placeholder-class="flex h-80 w-full items-center justify-center rounded-2xl bg-emerald-50 text-emerald-800"
                icon-class="h-16 w-16"
            />
        </x-ui.dashboard-card>

        <x-ui.dashboard-card title="{{ $product->name }}" subtitle="Admin product monitoring details.">
            <div class="grid gap-4 md:grid-cols-2">
                <div class="rounded-xl border border-slate-100 p-4 dark:border-gray-800">
                    <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Farmer</p>
                    <p class="mt-1 font-black text-slate-900 dark:text-white">{{ $product->farmer->name ?? 'Unknown farmer' }}</p>
                    <p class="mt-1 text-sm text-slate-500 dark:text-gray-400">{{ $product->farmer->email ?? 'Email unavailable' }}</p>
                </div>

                <div class="rounded-xl border border-slate-100 p-4 dark:border-gray-800">
                    <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Category</p>
                    <p class="mt-1 font-black text-slate-900 dark:text-white">{{ $product->category }}</p>
                </div>

                <div class="rounded-xl border border-slate-100 p-4 dark:border-gray-800">
                    <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Price</p>
                    <p class="mt-1 font-black text-emerald-800 dark:text-emerald-300">PHP {{ number_format($product->price, 2) }} / {{ $unit }}</p>
                </div>

                <div class="rounded-xl border border-slate-100 p-4 dark:border-gray-800">
                    <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Stock</p>
                    <p class="mt-1 font-black text-slate-900 dark:text-white">{{ $product->quantity }} {{ $unit }}</p>
                </div>

                <div class="rounded-xl border border-slate-100 p-4 dark:border-gray-800">
                    <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Stock Status</p>
                    <div class="mt-2"><x-ui.status-badge :status="$stockStatus" /></div>
                </div>

                <div class="rounded-xl border border-slate-100 p-4 dark:border-gray-800">
                    <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Status</p>
                    <div class="mt-2"><x-ui.status-badge :status="$listingStatus" /></div>
                </div>

                <div class="rounded-xl border border-slate-100 p-4 dark:border-gray-800">
                    <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Created Date</p>
                    <p class="mt-1 font-semibold text-slate-900 dark:text-white">{{ optional($product->created_at)->timezone(config('app.timezone'))->format('M d, Y h:i A') }}</p>
                </div>

                <div class="rounded-xl border border-slate-100 p-4 dark:border-gray-800">
                    <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Updated Date</p>
                    <p class="mt-1 font-semibold text-slate-900 dark:text-white">{{ optional($product->updated_at)->timezone(config('app.timezone'))->format('M d, Y h:i A') }}</p>
                </div>
            </div>

            <div class="mt-5 rounded-xl border border-slate-100 p-4 dark:border-gray-800">
                <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Description</p>
                <p class="mt-2 leading-relaxed text-slate-600 dark:text-gray-300">{{ $product->description }}</p>
            </div>

            <div class="mt-5 flex flex-wrap gap-3 border-t border-slate-100 pt-5 dark:border-gray-800">
                <x-ui.secondary-button href="{{ route('admin.products') }}">Back to Products</x-ui.secondary-button>
            </div>
        </x-ui.dashboard-card>
    </div>
@endsection
