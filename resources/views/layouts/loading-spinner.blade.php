{{-- Loading Spinner Component --}}
@props([
    'size' => 'md',
    'text' => 'Loading...',
    'dark' => false,
])

@php
$sizes = [
    'sm' => 'h-4 w-4',
    'md' => 'h-8 w-8',
    'lg' => 'h-12 w-12',
];
$sizeClass = $sizes[$size] ?? $sizes['md'];
@endphp

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center']) }}>
    <svg class="animate-spin {{ $sizeClass }} @if($dark) text-gray-400 @else text-green-600 @endif" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
    </svg>
    @if($text)
        <p class="mt-2 text-sm @if($dark) text-gray-500 @else text-gray-700 @endif">{{ $text }}</p>
    @endif
</div>

{{-- Skeleton Loader for Tables --}}
<template id="skeleton-row">
    <tr class="border-b border-gray-200 dark:border-gray-700 animate-pulse">
        <td class="px-4 py-3"><div class="h-4 bg-gray-300 dark:bg-gray-600 rounded w-3/4"></div></td>
        <td class="px-4 py-3"><div class="h-4 bg-gray-300 dark:bg-gray-600 rounded w-1/2"></div></td>
        <td class="px-4 py-3"><div class="h-4 bg-gray-300 dark:bg-gray-600 rounded w-2/3"></div></td>
        <td class="px-4 py-3"><div class="h-4 bg-gray-300 dark:bg-gray-600 rounded w-1/4"></div></td>
    </tr>
</template>

<script>
    function showSkeletonLoader(tableSelector, rows = 5) {
        const table = document.querySelector(tableSelector);
        if (!table) return;

        const tbody = table.querySelector('tbody');
        if (!tbody) return;

        const template = document.querySelector('#skeleton-row');
        for (let i = 0; i < rows; i++) {
            const clone = template.content.cloneNode(true);
            tbody.appendChild(clone);
        }
    }

    function hideSkeletonLoader(tableSelector) {
        const table = document.querySelector(tableSelector);
        if (!table) return;

        const tbody = table.querySelector('tbody');
        if (!tbody) return;

        const skeletons = tbody.querySelectorAll('tr.animate-pulse');
        skeletons.forEach(row => row.remove());
    }
</script>
