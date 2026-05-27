@props([
    'title' => null,
    'subtitle' => null,
])

<section {{ $attributes->merge(['class' => 'overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm']) }}>
    @if($title || $subtitle)
        <div class="border-b border-slate-100 px-5 py-4">
            @if($title)
                <h2 class="text-xl font-bold text-slate-900">{{ $title }}</h2>
            @endif
            @if($subtitle)
                <p class="mt-1.5 text-base leading-relaxed text-slate-500">{{ $subtitle }}</p>
            @endif
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="data-table min-w-full divide-y divide-slate-100 text-base">
            {{ $slot }}
        </table>
    </div>
</section>
