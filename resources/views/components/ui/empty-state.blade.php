@props([
    'title' => 'No records found',
    'message' => 'There is no data to display yet.',
    'actionUrl' => null,
    'actionLabel' => null,
    'icon' => 'inventory',
])

<div {{ $attributes->merge(['class' => 'rounded-2xl border border-dashed border-slate-200 bg-slate-50/70 px-6 py-12 text-center']) }}>
    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-emerald-50 text-emerald-800 ring-1 ring-emerald-100">
        <x-ui.icon :name="$icon" />
    </div>
    <h3 class="mt-4 text-lg font-bold text-slate-900">{{ $title }}</h3>
    <p class="mx-auto mt-2 max-w-md text-base leading-relaxed text-slate-500">{{ $message }}</p>

    @if($actionUrl && $actionLabel)
        <a href="{{ $actionUrl }}" class="mt-5 inline-flex items-center justify-center rounded-lg bg-emerald-700 px-5 py-2.5 text-base font-semibold text-white transition hover:bg-emerald-800">
            {{ $actionLabel }}
        </a>
    @endif
</div>
