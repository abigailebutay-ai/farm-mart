@props([
    'title' => null,
    'subtitle' => null,
])

<section {{ $attributes->merge(['class' => 'rounded-2xl border border-slate-100 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-950']) }}>
    @if($title || $subtitle)
        <div class="border-b border-slate-100 px-4 py-3.5 dark:border-gray-800">
            @if($title)
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">{{ $title }}</h2>
            @endif
            @if($subtitle)
                <p class="mt-1 text-sm leading-relaxed text-slate-500 dark:text-gray-400">{{ $subtitle }}</p>
            @endif
        </div>
    @endif

    <div class="p-4">
        {{ $slot }}
    </div>
</section>
