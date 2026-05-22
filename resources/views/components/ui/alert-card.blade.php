@props([
    'title',
    'message',
    'tone' => 'amber',
])

@php
    $tones = [
        'amber' => 'border-amber-200 bg-amber-50 text-amber-900',
        'green' => 'border-emerald-200 bg-emerald-50 text-emerald-900',
        'red' => 'border-red-200 bg-red-50 text-red-900',
        'gray' => 'border-slate-200 bg-slate-50 text-slate-800',
    ];
@endphp

<div {{ $attributes->merge(['class' => 'rounded-2xl border p-4 ' . ($tones[$tone] ?? $tones['amber'])]) }}>
    <div class="flex gap-3">
        <x-ui.icon name="alert" class="mt-0.5 h-5 w-5 shrink-0" />
        <div>
            <p class="text-sm font-bold">{{ $title }}</p>
            <p class="mt-1 text-sm leading-relaxed opacity-80">{{ $message }}</p>
        </div>
    </div>
</div>
