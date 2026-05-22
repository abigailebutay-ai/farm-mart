@props([
    'product',
    'compact' => false,
])

@php
    $stockStatus = ($product->quantity ?? 0) > 10
        ? 'In Stock'
        : (($product->quantity ?? 0) > 0 ? 'Low Stock' : 'Out of Stock');
    $unit = $product->unit ?? 'piece';
@endphp

<article {{ $attributes->merge(['class' => 'group overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-lg']) }}>
    <a href="{{ route('products.show', $product) }}" class="block">
        <div class="relative">
            <x-ui.product-image
                :product="$product"
                :image-class="($compact ? 'h-36' : 'h-48') . ' w-full object-cover'"
                :placeholder-class="($compact ? 'h-36' : 'h-48') . ' flex w-full items-center justify-center bg-gradient-to-br from-emerald-50 via-lime-50 to-yellow-50 text-emerald-800'"
                icon-class="h-12 w-12"
            />

            <div class="absolute left-4 top-4">
                <span class="rounded-full bg-white/95 px-3 py-1 text-xs font-bold text-emerald-800 shadow-sm">{{ $product->category ?? 'Fresh Produce' }}</span>
            </div>
            <div class="absolute right-4 top-4">
                <x-ui.status-badge :status="$stockStatus" />
            </div>
        </div>
    </a>

    <div class="p-4">
        <a href="{{ route('products.show', $product) }}" class="block">
            <h3 class="text-lg font-bold text-slate-900 transition group-hover:text-emerald-800">{{ $product->name }}</h3>
        </a>
        <p class="mt-1 text-sm text-slate-500">Farmer: {{ optional($product->farmer)->name ?? 'Local Farmer' }}</p>
        @unless($compact)
            <p class="mt-3 line-clamp-2 text-sm leading-relaxed text-slate-500">{{ $product->description }}</p>
        @endunless

        <div class="mt-4 flex items-center justify-between gap-3">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Price</p>
                <p class="text-lg font-black text-emerald-800">PHP {{ number_format($product->price ?? 0, 2) }} / {{ $unit }}</p>
            </div>
            <p class="rounded-full bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-500">{{ $product->quantity ?? 0 }} {{ $unit }} available</p>
        </div>

        <div class="mt-4 grid grid-cols-2 gap-2">
            @auth
                @if(auth()->user()->isConsumer() && ($product->quantity ?? 0) > 0)
                    <form method="POST" action="{{ route('cart.add', $product) }}">
                        @csrf
                        <input type="hidden" name="quantity" value="1">
                        <x-ui.primary-button class="w-full">Add to Cart</x-ui.primary-button>
                    </form>
                @else
                    <x-ui.primary-button href="{{ route('products.show', $product) }}" class="w-full">View</x-ui.primary-button>
                @endif
            @else
                <x-ui.primary-button href="{{ route('products.show', $product) }}" class="w-full">View</x-ui.primary-button>
            @endauth

            <x-ui.secondary-button href="{{ route('products.show', $product) }}" class="w-full">Details</x-ui.secondary-button>
        </div>
    </div>
</article>
