@extends('layouts.app')

@section('page-title', $product->name)

@section('content')
    @php
        $stockStatus = $product->quantity > 10 ? 'In Stock' : ($product->quantity > 0 ? 'Low Stock' : 'Out of Stock');
        $unit = 'kg';
        $farmer = $product->farmer;
        $farmerName = $farmer ? html_entity_decode($farmer->name, ENT_QUOTES | ENT_HTML5, 'UTF-8') : 'Local Farmer';
        $backUrl = auth()->check() && auth()->user()->isConsumer()
            ? ($farmer ? route('consumer.marketplace.farmer', $farmer) : route('consumer.marketplace'))
            : route('marketplace');
    @endphp

    <div class="space-y-6">
        <div class="rounded-3xl border border-slate-700 bg-slate-900 p-6 shadow-sm">
            <a href="{{ $backUrl }}" class="inline-flex items-center text-sm font-bold text-emerald-300 transition hover:text-emerald-200">
                &larr; Back to Products
            </a>
            <h1 class="mt-3 text-3xl font-black text-white md:text-4xl">{{ $product->name }}</h1>
            <p class="mt-2 max-w-3xl text-base leading-relaxed text-slate-300">
                View product details and add the quantity you want to buy.
            </p>
        </div>

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
            <div class="overflow-hidden rounded-3xl border border-slate-700 bg-slate-900 shadow-sm">
                <x-ui.product-image
                    :product="$product"
                    :alt="$product->name"
                    image-class="h-72 w-full object-cover md:h-[420px]"
                    placeholder-class="flex h-72 w-full flex-col items-center justify-center bg-slate-800 text-slate-400 md:h-[420px]"
                    icon-class="h-20 w-20"
                    placeholder-label="No image available"
                />
            </div>

            <div class="rounded-3xl border border-slate-700 bg-slate-900 p-6 shadow-sm md:p-8">
                <div class="space-y-6">
                    <div class="flex flex-wrap items-center gap-3">
                        <span class="rounded-full bg-emerald-900/60 px-3 py-1 text-xs font-black uppercase tracking-wide text-emerald-200">
                            {{ $product->category }}
                        </span>
                        <x-ui.status-badge :status="$stockStatus" />
                    </div>

                    <div>
                        <h2 class="text-3xl font-black tracking-tight text-white">{{ $product->name }}</h2>
                        <p class="mt-4 text-base leading-relaxed text-slate-300">
                            {{ $product->description ?: 'No description provided.' }}
                        </p>
                    </div>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div class="rounded-2xl border border-slate-700 bg-slate-950/50 p-5">
                            <p class="text-xs font-black uppercase tracking-wide text-slate-500">Price</p>
                            <p class="mt-2 text-2xl font-black text-emerald-300">PHP {{ number_format($product->price, 2) }} / {{ $unit }}</p>
                        </div>
                        <div class="rounded-2xl border border-slate-700 bg-slate-950/50 p-5">
                            <p class="text-xs font-black uppercase tracking-wide text-slate-500">Available Stock</p>
                            <p class="mt-2 text-2xl font-black text-white">{{ $product->quantity }} {{ $unit }}</p>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-700 bg-slate-950/50 p-5">
                        <p class="text-xs font-black uppercase tracking-wide text-slate-500">Sold by</p>
                        <div class="mt-4 flex items-start gap-4">
                            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-emerald-700 text-white">
                                <x-ui.icon name="farmer" />
                            </span>
                            <div class="min-w-0">
                                <p class="text-lg font-black text-white">{{ $farmerName }}</p>
                                <p class="mt-1 text-sm leading-relaxed text-slate-300">{{ $farmer?->address ?: 'Address not provided' }}</p>
                                <p class="mt-1 text-sm text-slate-400">Contact: {{ $farmer?->phone ?: 'Contact not provided' }}</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        @auth
                            @if(auth()->user()->isConsumer() && $product->quantity > 0)
                                <form method="POST" action="{{ route('cart.add', $product) }}" class="flex flex-col gap-3 sm:flex-row sm:items-end">
                                    @csrf
                                    <div class="flex-1">
                                        <label for="quantity" class="mb-2 block text-sm font-bold text-slate-200">Quantity ({{ $unit }})</label>
                                        <input
                                            type="number"
                                            id="quantity"
                                            name="quantity"
                                            value="1"
                                            min="1"
                                            max="{{ $product->quantity }}"
                                            class="w-full rounded-2xl border border-slate-700 bg-slate-950 px-4 py-3 text-white focus:border-emerald-500 focus:outline-none focus:ring-4 focus:ring-emerald-500/20"
                                        >
                                    </div>
                                    <button type="submit" class="inline-flex w-full items-center justify-center rounded-2xl bg-emerald-600 px-5 py-3 text-sm font-black text-white transition hover:bg-emerald-500 focus:outline-none focus:ring-4 focus:ring-emerald-500/30 sm:w-40">
                                        Add to Cart
                                    </button>
                                </form>
                            @elseif(auth()->user()->isConsumer())
                                <a href="{{ $backUrl }}" class="inline-flex w-full items-center justify-center rounded-2xl border border-slate-700 px-5 py-3 text-sm font-black text-slate-200 transition hover:border-emerald-500/60 hover:bg-slate-800 sm:w-auto">
                                    Back to Marketplace
                                </a>
                            @else
                                <a href="{{ $backUrl }}" class="inline-flex w-full items-center justify-center rounded-2xl border border-slate-700 px-5 py-3 text-sm font-black text-slate-200 transition hover:border-emerald-500/60 hover:bg-slate-800 sm:w-auto">
                                    Back to Marketplace
                                </a>
                            @endif
                        @else
                            <div class="grid gap-3 sm:grid-cols-2">
                                <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-2xl bg-emerald-600 px-5 py-3 text-sm font-black text-white transition hover:bg-emerald-500">
                                    Login to Buy
                                </a>
                                <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-700 px-5 py-3 text-sm font-black text-slate-200 transition hover:border-emerald-500/60 hover:bg-slate-800">
                                    Create Account
                                </a>
                            </div>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
