@extends('layouts.app')

@section('page-title', 'Marketplace')

@section('content')
    <x-ui.page-header
        title="Marketplace"
        subtitle="Browse available products from local farmers."
    />

    <x-ui.dashboard-card class="mb-6" title="Choose a farmer to shop from" subtitle="Search by farmer/store, product name, category, or stock status.">
        <form method="GET" action="{{ route('consumer.marketplace') }}" class="grid gap-4 lg:grid-cols-[1fr_220px_220px_220px_auto] lg:items-end">
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
                <label for="farmer_id" class="mb-2 block text-sm font-bold text-slate-700">Farmer</label>
                <select id="farmer_id" name="farmer_id" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-slate-800 focus:border-emerald-600 focus:outline-none focus:ring-4 focus:ring-emerald-100">
                    <option value="">All Farmers</option>
                    @foreach($farmers ?? [] as $farmer)
                        <option value="{{ $farmer->id }}" @selected((string) ($selectedFarmerId ?? '') === (string) $farmer->id)>
                            {{ $farmer->name }}
                        </option>
                    @endforeach
                </select>
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
                    <option value="">All Available Stock</option>
                    <option value="in_stock" @selected(($availability ?? '') === 'in_stock')>In Stock</option>
                    <option value="low_stock" @selected(($availability ?? '') === 'low_stock')>Low Stock</option>
                </select>
            </div>

            <div class="flex gap-2">
                <x-ui.primary-button class="flex-1 py-2.5">Search</x-ui.primary-button>
                <x-ui.secondary-button href="{{ route('consumer.marketplace') }}" class="py-2.5">Reset</x-ui.secondary-button>
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
            title="No available products found"
            message="No available farmer products matched your search. Try another keyword, category, or stock filter."
            icon="products"
        />
    @endif
@endsection
