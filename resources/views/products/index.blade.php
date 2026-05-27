@extends('layouts.app')

@section('page-title', 'Marketplace')

@section('content')
    <div class="mb-6 overflow-hidden rounded-2xl border border-emerald-100 bg-gradient-to-r from-white via-emerald-50 to-lime-50 p-5 shadow-sm md:p-6">
        <div class="grid gap-5 lg:grid-cols-[1fr_auto] lg:items-end">
            <div>
                <p class="text-xs font-black uppercase tracking-wide text-amber-600">SariwaLink Marketplace</p>
                <h1 class="mt-1 text-2xl font-black text-slate-900 md:text-3xl">Shop Fresh Products Directly from Local Farmers</h1>
                <p class="mt-2 max-w-3xl text-sm leading-relaxed text-slate-600">
                    Search agricultural products, compare availability, and order directly from farmers through the digital supply chain.
                </p>
            </div>
            <div class="grid grid-cols-3 gap-3 text-center">
                <div class="rounded-xl bg-white p-3 ring-1 ring-slate-100">
                    <p class="text-xl font-black text-slate-900">{{ $products->total() }}</p>
                    <p class="text-xs text-slate-500">Results</p>
                </div>
                <div class="rounded-xl bg-white p-3 ring-1 ring-slate-100">
                    <p class="text-xl font-black text-slate-900">{{ $categories->count() }}</p>
                    <p class="text-xs text-slate-500">Categories</p>
                </div>
                <div class="rounded-xl bg-amber-100 p-3 text-amber-800 ring-1 ring-amber-200">
                    <p class="text-xl font-black">Fresh</p>
                    <p class="text-xs font-bold">Local supply</p>
                </div>
            </div>
        </div>
    </div>

    <x-ui.dashboard-card class="mb-6" title="Find Products" subtitle="Filter by keyword, category, and availability.">
        <form method="GET" action="{{ route($marketplaceRoute ?? 'marketplace') }}" class="grid gap-4 lg:grid-cols-[1fr_220px_220px_auto] lg:items-end">
            <div>
                <label for="search" class="mb-2 block text-sm font-bold text-slate-700">Search</label>
                <div class="relative">
                    <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-emerald-700">
                        <x-ui.icon name="products" class="h-4 w-4" />
                    </span>
                    <input id="search" type="text" name="search" value="{{ $search ?? '' }}" placeholder="Search tomato, pechay, eggplant..." class="w-full rounded-xl border border-slate-200 bg-white py-2.5 pl-10 pr-4 text-slate-800 focus:border-emerald-600 focus:outline-none focus:ring-4 focus:ring-emerald-100">
                </div>
            </div>

            <div>
                <label for="category" class="mb-2 block text-sm font-bold text-slate-700">Category</label>
                <select id="category" name="category" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-slate-800 focus:border-emerald-600 focus:outline-none focus:ring-4 focus:ring-emerald-100">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->category }}" @selected(request('category') === $cat->category)>
                            {{ $cat->category }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="availability" class="mb-2 block text-sm font-bold text-slate-700">Availability</label>
                <select id="availability" name="availability" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-slate-800 focus:border-emerald-600 focus:outline-none focus:ring-4 focus:ring-emerald-100">
                    <option value="">All Stock Status</option>
                    <option value="in_stock" @selected(($availability ?? '') === 'in_stock')>In Stock</option>
                    <option value="low_stock" @selected(($availability ?? '') === 'low_stock')>Low Stock</option>
                    <option value="out_of_stock" @selected(($availability ?? '') === 'out_of_stock')>Out of Stock</option>
                </select>
            </div>

            <div class="flex gap-2">
                <x-ui.primary-button class="flex-1 py-2.5">Search</x-ui.primary-button>
                <x-ui.secondary-button href="{{ route($marketplaceRoute ?? 'marketplace') }}" class="py-2.5">Reset</x-ui.secondary-button>
            </div>
        </form>
    </x-ui.dashboard-card>

    @if($products->count() > 0)
        <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4">
            @foreach($products as $product)
                <x-ui.product-card :product="$product" />
            @endforeach
        </div>

        <div class="mt-8">
            {{ $products->links() }}
        </div>
    @else
        <x-ui.empty-state
            title="No products found"
            message="No approved products matched your search. Try another keyword, category, or availability filter."
            icon="products"
        />
    @endif
@endsection
