@props([
    'product' => null,
    'alt' => null,
    'imageClass' => 'h-20 w-20 rounded-xl object-cover',
    'placeholderClass' => 'flex h-20 w-20 items-center justify-center rounded-xl bg-emerald-50 text-emerald-800',
    'iconClass' => 'h-8 w-8',
])

@php
    $imageUrl = $product?->image_url;
    $altText = $alt ?? $product?->name ?? 'Product image';
@endphp

@if($imageUrl)
    <img
        src="{{ $imageUrl }}"
        alt="{{ $altText }}"
        class="{{ $imageClass }}"
        loading="lazy"
        onerror="this.classList.add('hidden'); this.nextElementSibling?.classList.remove('hidden');"
    >
@endif

<div class="{{ $imageUrl ? 'hidden ' : '' }}{{ $placeholderClass }}">
    <x-ui.icon name="products" class="{{ $iconClass }}" />
</div>
