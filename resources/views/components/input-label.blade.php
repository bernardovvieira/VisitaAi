@props(['value' => null, 'required' => false])

@php
    $resolvedValue = $value;
    if (is_string($resolvedValue)) {
        $resolvedValue = preg_replace('/\s*\((?:opcional|optional)\)\.?/iu', '', $resolvedValue) ?? $resolvedValue;
        $resolvedValue = trim($resolvedValue);
    }
@endphp

<label {{ $attributes->merge(['class' => 'mb-1 block text-xs font-semibold tracking-tight text-slate-600 dark:text-slate-400']) }}>
    {{ $resolvedValue ?? $slot }}
    @if($required)
        <span class="text-red-500">*</span>
    @endif
</label>
