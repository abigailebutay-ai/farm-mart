<div {{ $attributes->merge(['class' => 'overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm']) }}>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-100">
            {{ $slot }}
        </table>
    </div>
</div>
