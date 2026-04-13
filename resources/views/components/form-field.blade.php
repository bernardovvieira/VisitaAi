@props([
    'name' => null,
    'label' => null,
    'required' => false,
    'messages' => null,
    'help' => null,
])

@php
    $resolvedMessages = $messages ?? ($name ? $errors->get($name) : []);
@endphp

<div {{ $attributes->class(['space-y-1.5']) }}>
    @if(filled($label))
        <x-input-label :for="$name" :value="$label" :required="$required" />
    @endif

    {{ $slot }}

    @if(filled($help))
        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $help }}</p>
    @endif

    <x-input-error :messages="$resolvedMessages" class="mt-1" />
</div>