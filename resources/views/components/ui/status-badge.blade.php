@props(['status' => 'pending'])

@php
    $normalized = strtolower(str_replace(['_', '-'], ' ', (string) $status));
    $classes = match ($normalized) {
        'completed', 'delivered', 'approved', 'active', 'in stock', 'high' => 'bg-emerald-100 text-emerald-800 ring-emerald-200 dark:bg-emerald-900/40 dark:text-emerald-200 dark:ring-emerald-800',
        'accepted', 'confirmed', 'preparing', 'ready for pickup', 'ready for delivery', 'out for delivery' => 'bg-sky-100 text-sky-800 ring-sky-200 dark:bg-sky-900/40 dark:text-sky-200 dark:ring-sky-800',
        'pending', 'low stock', 'moderate' => 'bg-amber-100 text-amber-800 ring-amber-200 dark:bg-amber-900/40 dark:text-amber-200 dark:ring-amber-800',
        'cancelled', 'canceled', 'rejected', 'out of stock', 'high risk' => 'bg-red-100 text-red-800 ring-red-200 dark:bg-red-900/40 dark:text-red-200 dark:ring-red-800',
        default => 'bg-slate-100 text-slate-700 ring-slate-200 dark:bg-slate-800 dark:text-slate-200 dark:ring-slate-700',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold ring-1 {$classes}"]) }}>
    {{ \Illuminate\Support\Str::title($normalized) }}
</span>
