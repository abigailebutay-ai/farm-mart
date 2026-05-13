{{-- Form Checkbox Component --}}
@props([
    'name',
    'label' => null,
    'value' => 1,
    'checked' => false,
    'error' => null,
    'disabled' => false,
    'hint' => null,
    'class' => '',
])

<div class="mb-4">
    <div class="flex items-start">
        <input
            type="checkbox"
            id="{{ $name }}"
            name="{{ $name }}"
            value="{{ $value }}"
            @if(old($name, $checked)) checked @endif
            @if($disabled) disabled @endif
            class="mt-1 h-4 w-4 text-green-600 border-gray-300 rounded focus:ring-green-500 dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-green-400 cursor-pointer transition {{ $class }}"
        />

        @if($label)
            <label for="{{ $name }}" class="ml-2 block text-sm text-gray-700 dark:text-gray-300 cursor-pointer">
                {{ $label }}
            </label>
        @endif
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
