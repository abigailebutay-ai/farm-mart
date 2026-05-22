@props([
    'product' => null,
    'src' => null,
    'alt' => null,
    'imageClass' => 'h-20 w-20 rounded-xl object-cover',
    'placeholderClass' => 'flex h-20 w-20 items-center justify-center rounded-xl bg-emerald-50 text-emerald-800',
    'iconClass' => 'h-8 w-8',
])

@php
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;

    $imagePath = $src ?? $product?->image;
    $imageUrl = null;
    $altText = $alt ?? $product?->name ?? 'Product image';

    if (! empty($imagePath)) {
        $imagePath = str_replace('\\', '/', (string) $imagePath);

        if (Str::startsWith($imagePath, ['http://', 'https://'])) {
            $imageUrl = $imagePath;
        } else {
            foreach (['/storage/', 'storage/', 'storage/app/public/', 'public/'] as $prefix) {
                if (Str::contains($imagePath, $prefix)) {
                    $imagePath = Str::after($imagePath, $prefix);
                }
            }

            $imageUrl = Storage::disk(config('filesystems.default'))->url(ltrim($imagePath, '/'));
        }
    }
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
