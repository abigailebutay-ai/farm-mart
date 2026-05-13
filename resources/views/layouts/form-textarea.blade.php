{{-- Form Textarea Component --}}
@props([
    'name',
    'label' => null,
    'placeholder' => '',
    'value' => null,
    'error' => null,
    'required' => false,
    'readonly' => false,
    'disabled' => false,
    'rows' => 4,
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

    <textarea
        id="{{ $name }}"
        name="{{ $name }}"
        rows="{{ $rows }}"
        placeholder="{{ $placeholder }}"
        @if($readonly) readonly @endif
        @if($disabled) disabled @endif
        @if($required) required @endif
        class="block w-full px-3 py-2 border @if($error || $errors->has($name)) border-red-500 @else border-gray-300 dark:border-gray-600 @endif rounded-lg shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 dark:focus:ring-green-400 dark:bg-gray-700 dark:text-white transition {{ $class }}"
    >{{ old($name, $value) }}</textarea>

    @if($error || $errors->has($name))
        <p class="mt-1 text-sm text-red-500">
            {{ $error ?? $errors->first($name) }}
        </p>
    @endif

    @if($hint)
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $hint }}</p>
    @endif
</div>
