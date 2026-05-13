{{-- Form Input Component --}}
@props([
    'name',
    'label' => null,
    'type' => 'text',
    'placeholder' => '',
    'value' => null,
    'error' => null,
    'icon' => null,
    'required' => false,
    'readonly' => false,
    'disabled' => false,
    'hint' => null,
    'class' => '',
    'step' => null,
    'min' => null,
])

<div class="mb-4">
    @if($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    <div class="relative">
        @if($icon)
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <span class="text-gray-500 dark:text-gray-400">{{ $icon }}</span>
            </div>
        @endif

        <input
            type="{{ $type }}"
            id="{{ $name }}"
            name="{{ $name }}"
            value="{{ old($name, $value) }}"
            placeholder="{{ $placeholder }}"
            @if($step) step="{{ $step }}" @endif
            @if($min !== null) min="{{ $min }}" @endif
            @if($readonly) readonly @endif
            @if($disabled) disabled @endif
            @if($required) required @endif
            class="block w-full px-3 py-2 @if($icon) pl-10 @endif border @if($error || $errors->has($name)) border-red-500 @else border-gray-300 dark:border-gray-600 @endif rounded-lg shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 dark:focus:ring-green-400 dark:bg-gray-700 dark:text-white transition {{ $class }}"
        />
    </div>

    @if($error || $errors->has($name))
        <p class="mt-1 text-sm text-red-500">
            {{ $error ?? $errors->first($name) }}
        </p>
    @endif

    @if($hint)
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $hint }}</p>
    @endif
</div>
