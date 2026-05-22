@props([
    'href' => '#',
    'title',
    'description' => null,
    'icon' => 'dashboard',
])

<a href="{{ $href }}" {{ $attributes->merge(['class' => 'group flex items-start gap-3 rounded-xl border border-slate-100 bg-white p-3 shadow-sm transition hover:-translate-y-0.5 hover:border-emerald-200 hover:bg-emerald-50/60 hover:shadow-md']) }}>
    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-emerald-800 ring-1 ring-emerald-100 group-hover:bg-white">
        <x-ui.icon :name="$icon" />
    </span>
    <span>
        <span class="block text-sm font-bold text-slate-900">{{ $title }}</span>
        @if($description)
            <span class="mt-1 block text-xs leading-relaxed text-slate-500">{{ $description }}</span>
        @endif
    </span>
</a>
