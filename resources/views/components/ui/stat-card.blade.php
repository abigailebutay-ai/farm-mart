@props([
    'label',
    'value' => 0,
    'icon' => 'dashboard',
    'trend' => null,
    'tone' => 'green',
])

@php
    $tones = [
        'green' => 'bg-emerald-50 text-emerald-800 ring-emerald-100',
        'amber' => 'bg-amber-50 text-amber-700 ring-amber-100',
        'red' => 'bg-red-50 text-red-700 ring-red-100',
        'blue' => 'bg-sky-50 text-sky-700 ring-sky-100',
        'gray' => 'bg-gray-50 text-gray-700 ring-gray-100',
    ];
    $toneClass = $tones[$tone] ?? $tones['green'];
@endphp

<div {{ $attributes->merge(['class' => 'rounded-2xl border border-slate-100 bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md']) }}>
    <div class="flex items-start justify-between gap-3">
        <div>
            <p class="text-sm font-semibold text-slate-500">{{ $label }}</p>
            <p class="mt-1.5 text-2xl font-black text-slate-900">{{ $value }}</p>
        </div>
        @if($icon)
            <div class="flex h-10 w-10 items-center justify-center rounded-xl ring-1 {{ $toneClass }}">
                <x-ui.icon :name="$icon" />
            </div>
        @endif
    </div>

    @if($trend)
        <p class="mt-3 border-t border-slate-100 pt-2.5 text-xs font-medium leading-relaxed text-slate-500">{{ $trend }}</p>
    @endif
</div>
