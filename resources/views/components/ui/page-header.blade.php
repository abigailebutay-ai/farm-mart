@props([
    'title',
    'subtitle' => null,
    'actionUrl' => null,
    'actionLabel' => null,
])

<div {{ $attributes->merge(['class' => 'mb-5 overflow-hidden rounded-2xl border border-emerald-100 bg-gradient-to-r from-emerald-50 via-white to-lime-50 p-5 shadow-sm dark:border-emerald-900/50 dark:from-gray-900 dark:via-gray-950 dark:to-emerald-950/40']) }}>
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
    <div>
        <h1 class="text-2xl font-black text-slate-900 dark:text-white md:text-3xl">{{ $title }}</h1>
        @if($subtitle)
            <p class="mt-2 max-w-3xl text-sm leading-relaxed text-slate-600 dark:text-gray-300">{{ $subtitle }}</p>
        @endif
    </div>

    @if($actionUrl && $actionLabel)
        <a href="{{ $actionUrl }}" class="inline-flex items-center justify-center rounded-xl bg-emerald-700 px-4 py-2 text-sm font-bold text-white shadow-sm transition hover:bg-emerald-800">
            {{ $actionLabel }}
        </a>
    @endif
    </div>
</div>
