{{-- Stat Card Component --}}
@props([
    'icon' => '',
    'title' => '',
    'value' => '',
    'subtitle' => '',
    'href' => null,
    'linkText' => 'View →',
    'color' => 'green', // green, blue, amber, red
    'animated' => true,
])

@php
$colors = [
    'green' => 'text-green-600 dark:text-green-400',
    'blue' => 'text-blue-600 dark:text-blue-400',
    'amber' => 'text-amber-600 dark:text-amber-400',
    'red' => 'text-red-600 dark:text-red-400',
    'yellow' => 'text-yellow-600 dark:text-yellow-400',
];

$colorClass = $colors[$color] ?? $colors['green'];
$animationClass = $animated ? 'hover:shadow-lg transition transform hover:-translate-y-1' : '';
@endphp

<div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow {{ $animationClass }}">
    <div class="flex items-center justify-between">
        <div>
            @if($title)
                <h3 class="text-gray-600 dark:text-gray-400 text-sm font-semibold mb-1">{{ $title }}</h3>
            @endif
            @if($value)
                <p class="text-3xl font-bold {{ $colorClass }}">{{ $value }}</p>
            @endif
            @if($subtitle)
                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">{{ $subtitle }}</p>
            @endif
        </div>
        @if($icon)
            <div class="text-5xl opacity-20">{{ $icon }}</div>
        @endif
    </div>
    @if($href)
        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
            <a href="{{ $href }}" class="text-sm {{ $colorClass }} hover:opacity-80 font-medium transition">{{ $linkText }}</a>
        </div>
    @endif
</div>
