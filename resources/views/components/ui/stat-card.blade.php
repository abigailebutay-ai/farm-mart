@props([
    'label',
    'value' => 0,
    'icon' => 'dashboard',
    'trend' => null,
    'tone' => 'green',
])

@php
    $tones = [
        'green' => 'bg-emerald-50 text-emerald-800 ring-emerald-100 dark:bg-emerald-950 dark:text-emerald-300 dark:ring-emerald-900',
        'amber' => 'bg-amber-50 text-amber-700 ring-amber-100 dark:bg-amber-950 dark:text-amber-300 dark:ring-amber-900',
        'red' => 'bg-red-50 text-red-700 ring-red-100 dark:bg-red-950 dark:text-red-300 dark:ring-red-900',
        'blue' => 'bg-sky-50 text-sky-700 ring-sky-100 dark:bg-sky-950 dark:text-sky-300 dark:ring-sky-900',
        'gray' => 'bg-gray-50 text-gray-700 ring-gray-100 dark:bg-gray-900 dark:text-gray-300 dark:ring-gray-800',
    ];
    $toneClass = $tones[$tone] ?? $tones['green'];
@endphp

<div {{ $attributes->merge(['class' => 'rounded-2xl border border-slate-100 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:border-gray-800 dark:bg-gray-950']) }}>
    <div class="flex items-start justify-between gap-3">
        <div>
            <p class="text-base font-semibold text-slate-500 dark:text-gray-400">{{ $label }}</p>
            <p class="mt-2 text-3xl font-black text-slate-900 dark:text-white">{{ $value }}</p>
        </div>
        @if($icon)
            <div class="flex h-10 w-10 items-center justify-center rounded-xl ring-1 {{ $toneClass }}">
                <x-ui.icon :name="$icon" />
            </div>
        @endif
    </div>

    @if($trend)
        <p class="mt-4 border-t border-slate-100 pt-3 text-sm font-medium leading-relaxed text-slate-500 dark:border-gray-800 dark:text-gray-400">{{ $trend }}</p>
    @endif
</div>
