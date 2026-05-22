@extends('layouts.app')

@section('page-title', $product->name)

@section('content')
    @php
        $stockStatus = $product->quantity > 10 ? 'In Stock' : ($product->quantity > 0 ? 'Low Stock' : 'Out of Stock');
        $unit = $product->unit ?? 'piece';
    @endphp

    <div class="grid gap-8 lg:grid-cols-[0.95fr_1.05fr]">
        <div class="overflow-hidden rounded-[2rem] border border-slate-100 bg-white shadow-sm">
            <x-ui.product-image
                :product="$product"
                image-class="h-[28rem] w-full object-cover"
                placeholder-class="flex h-[28rem] w-full items-center justify-center bg-gradient-to-br from-emerald-50 via-lime-50 to-yellow-50 text-emerald-800"
                icon-class="h-20 w-20"
            />
        </div>

        <div class="rounded-[2rem] border border-slate-100 bg-white p-6 shadow-sm md:p-8">
            <div class="flex flex-wrap items-center gap-3">
                <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-black uppercase tracking-wide text-emerald-800">{{ $product->category }}</span>
                <x-ui.status-badge :status="$stockStatus" />
            </div>

            <h1 class="mt-5 text-4xl font-black tracking-tight text-slate-900">{{ $product->name }}</h1>
            <p class="mt-4 leading-relaxed text-slate-600">{{ $product->description }}</p>

            <div class="mt-6 grid gap-4 sm:grid-cols-2">
                <div class="rounded-2xl bg-yellow-50 p-5">
                    <p class="text-xs font-black uppercase tracking-wide text-slate-500">Price</p>
                    <p class="mt-2 text-3xl font-black text-emerald-800">PHP {{ number_format($product->price, 2) }} / {{ $unit }}</p>
                </div>
                <div class="rounded-2xl bg-emerald-50 p-5">
                    <p class="text-xs font-black uppercase tracking-wide text-slate-500">Available Stock</p>
                    <p class="mt-2 text-3xl font-black text-emerald-900">{{ $product->quantity }} {{ $unit }}</p>
                </div>
            </div>

            <div class="mt-6 rounded-2xl border border-slate-100 p-5">
                <p class="text-xs font-black uppercase tracking-wide text-slate-500">Sold by</p>
                <div class="mt-3 flex items-center gap-3">
                    <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-800">
                        <x-ui.icon name="farmer" />
                    </span>
                    <div>
                        <p class="font-bold text-slate-900">{{ optional($product->farmer)->name ?? 'Local Farmer' }}</p>
                        <p class="text-sm text-slate-500">{{ optional($product->farmer)->address ?? 'Address not provided' }}</p>
                    </div>
                </div>
            </div>

            <div class="mt-6">
                @auth
                    @if(auth()->user()->isConsumer() && $product->quantity > 0)
                        <form method="POST" action="{{ route('cart.add', $product) }}" class="grid gap-3 sm:grid-cols-[1fr_auto] sm:items-end">
                            @csrf
                            <div>
                                <label for="quantity" class="mb-2 block text-sm font-bold text-slate-700">Quantity ({{ $unit }})</label>
                                <input type="number" id="quantity" name="quantity" value="1" min="1" max="{{ $product->quantity }}" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-slate-900 focus:border-emerald-600 focus:outline-none focus:ring-4 focus:ring-emerald-100">
                            </div>
                            <x-ui.primary-button class="py-3">Add to Cart</x-ui.primary-button>
                        </form>
                    @elseif(auth()->user()->isConsumer())
                        <x-ui.secondary-button href="{{ route('consumer.marketplace') }}">Back to Marketplace</x-ui.secondary-button>
                    @else
                        <x-ui.secondary-button href="{{ route('marketplace') }}">Back to Marketplace</x-ui.secondary-button>
                    @endif
                @else
                    <div class="grid gap-3 sm:grid-cols-2">
                        <x-ui.primary-button href="{{ route('login') }}" class="py-3">Login to Buy</x-ui.primary-button>
                        <x-ui.secondary-button href="{{ route('register') }}" class="py-3">Create Account</x-ui.secondary-button>
                    </div>
                @endauth
            </div>
        </div>
    </div>
@endsection
