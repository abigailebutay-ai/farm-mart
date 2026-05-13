{{-- Form Select Component --}}
@props([
    'name',
    'label' => null,
    'placeholder' => 'Select an option',
    'options' => [],
    'value' => null,
    'error' => null,
    'icon' => null,
    'required' => false,
    'disabled' => false,
    'hint' => null,
    'class' => '',
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

        <select
            id="{{ $name }}"
            name="{{ $name }}"
            @if($disabled) disabled @endif
            @if($required) required @endif
            class="block w-full px-3 py-2 @if($icon) pl-10 @endif border @if($error || $errors->has($name)) border-red-500 @else border-gray-300 dark:border-gray-600 @endif rounded-lg shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 dark:focus:ring-green-400 dark:bg-gray-700 dark:text-white transition {{ $class }}"
        >
            @if($placeholder)
                <option value="">{{ $placeholder }}</option>
            @endif

            @foreach($options as $optValue => $optLabel)
                <option
                    value="{{ $optValue }}"
                    @if(old($name, $value) == $optValue) selected @endif
                >
                    {{ $optLabel }}
                </option>
            @endforeach
        </select>
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
