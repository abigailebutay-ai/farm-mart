@extends('layouts.app')

@section('page-title', 'Marketplace')

@section('content')
    @if(isset($selectedFarmer))
        <x-ui.page-header
            title="Products from {{ $selectedFarmer->name }}"
            subtitle="You are viewing products from {{ $selectedFarmer->name }}."
            action-url="{{ route('consumer.marketplace') }}"
            action-label="Change Farmer"
        />

        <x-ui.dashboard-card class="mb-6" title="Find Products" subtitle="Search products from this farmer.">
            <form method="GET" action="{{ route('consumer.marketplace.farmer', $selectedFarmer) }}" class="grid gap-4 lg:grid-cols-[1fr_220px_220px_auto] lg:items-end">
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
                        <option value="">All Available Stock</option>
                        <option value="in_stock" @selected(($availability ?? '') === 'in_stock')>In Stock</option>
                        <option value="low_stock" @selected(($availability ?? '') === 'low_stock')>Low Stock</option>
                    </select>
                </div>

                <div class="flex gap-2">
                    <x-ui.primary-button class="flex-1 py-2.5">Search</x-ui.primary-button>
                    <x-ui.secondary-button href="{{ route('consumer.marketplace.farmer', $selectedFarmer) }}" class="py-2.5">Reset</x-ui.secondary-button>
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
                message="No available products from this farmer matched your search."
                action-url="{{ route('consumer.marketplace') }}"
                action-label="Choose Another Farmer"
                icon="products"
            />
        @endif
    @else
        <div class="mb-6 overflow-hidden rounded-3xl border border-slate-700 bg-slate-900 p-6 shadow-sm">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-sm font-bold uppercase tracking-wide text-emerald-300">Marketplace</p>
                    <h1 class="mt-2 text-3xl font-black text-white">Choose a Farmer</h1>
                    <p class="mt-2 max-w-3xl text-base leading-relaxed text-slate-300">
                        Choose a farmer to view their available products, pickup location, and payment details.
                    </p>
                </div>

                <div class="rounded-2xl border border-emerald-800/70 bg-emerald-950/40 px-4 py-3 text-sm font-semibold text-emerald-100">
                    Shop one farmer at a time for clearer checkout.
                </div>
            </div>
        </div>

        @if(($farmers ?? collect())->count() > 0)
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
                @foreach($farmers as $farmer)
                    <article class="group flex h-full flex-col rounded-3xl border border-slate-700 bg-slate-900 p-6 shadow-sm transition hover:border-emerald-500/60 hover:bg-slate-800/80 hover:shadow-lg">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex min-w-0 items-center gap-4">
                                <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-emerald-700 text-lg font-black text-white shadow-sm shadow-emerald-950/40">
                                    {{ strtoupper(\Illuminate\Support\Str::substr($farmer->name, 0, 1)) }}
                                </div>

                                <div class="min-w-0">
                                    <h2 class="truncate text-xl font-black text-white">{{ $farmer->name }}</h2>
                                    <p class="mt-1 text-sm font-semibold text-emerald-300">Farmer Store</p>
                                </div>
                            </div>

                            <span class="rounded-full bg-emerald-900/50 px-3 py-1 text-sm font-bold text-emerald-200">
                                {{ $farmer->available_products_count }} products
                            </span>
                        </div>

                        <div class="mt-6 flex-1 space-y-4 text-sm leading-relaxed text-slate-300">
                            <div class="rounded-2xl border border-slate-700/80 bg-slate-950/40 p-4">
                                <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Location</p>
                                <p class="mt-1 font-semibold text-slate-100">{{ $farmer->address ?: 'Address not provided' }}</p>
                            </div>

                            <div class="rounded-2xl border border-slate-700/80 bg-slate-950/40 p-4">
                                <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Contact</p>
                                <p class="mt-1 font-semibold text-slate-100">{{ $farmer->phone ?: 'Contact not provided' }}</p>
                            </div>
                        </div>

                        <a href="{{ route('consumer.marketplace.farmer', $farmer) }}" class="mt-6 inline-flex w-full items-center justify-center rounded-xl bg-emerald-600 px-4 py-3 text-sm font-black text-white transition hover:bg-emerald-500 focus:outline-none focus:ring-4 focus:ring-emerald-500/30">
                            View Products
                        </a>
                    </article>
                @endforeach
            </div>
        @else
            <x-ui.empty-state
                title="No farmers available yet."
                message="Please check again later for available products."
                action-url="{{ route('dashboard') }}"
                action-label="Back to Dashboard"
                icon="farmer"
                class="border-slate-700 bg-slate-900 text-white [&_h3]:text-white [&_p]:text-slate-300"
            />
        @endif
    @endif
@endsection
