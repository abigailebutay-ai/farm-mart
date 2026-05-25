@props([
    'product' => null,
    'src' => null,
    'alt' => null,
    'class' => null,
    'imageClass' => 'h-20 w-20 rounded-xl object-cover',
    'placeholderClass' => 'flex h-20 w-20 items-center justify-center rounded-xl bg-emerald-50 text-emerald-800',
    'iconClass' => 'h-8 w-8',
])

@php
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;

    $imagePath = $src ?? $product?->image;
    $imageUrl = $product?->image_url ?? null;
    $altText = $alt ?? $product?->name ?? 'Product image';
    $resolvedImageClass = $class ?: $imageClass;

    if (! $imageUrl && ! empty($imagePath)) {
        $imagePath = str_replace('\\', '/', (string) $imagePath);

        if (Str::startsWith($imagePath, ['http://', 'https://'])) {
            $imageUrl = $imagePath;
        } else {
            foreach (['/storage/', 'storage/', 'storage/app/public/', 'public/'] as $prefix) {
                if (Str::contains($imagePath, $prefix)) {
                    $imagePath = Str::after($imagePath, $prefix);
                }
            }

            $path = ltrim($imagePath, '/');

            try {
                $imageUrl = Str::startsWith($path, 'products/')
                    ? route('product.image', ['path' => $path])
                    : Storage::disk(config('filesystems.default'))->url($path);
            } catch (\Throwable $e) {
                report($e);
                $imageUrl = null;
            }
        }
    }
@endphp

@if($imageUrl)
    <img
        src="{{ $imageUrl }}"
        alt="{{ $altText }}"
        class="{{ $resolvedImageClass }}"
        loading="lazy"
        onerror="this.classList.add('hidden'); this.nextElementSibling?.classList.remove('hidden');"
    >
@endif

<div class="{{ $imageUrl ? 'hidden ' : '' }}{{ $placeholderClass }}">
    <x-ui.icon name="products" class="{{ $iconClass }}" />
</div>
