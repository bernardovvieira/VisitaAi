@props(['value'])

<label {{ $attributes->merge(['class' => 'mb-0.5 block text-xs font-semibold text-slate-600 dark:text-slate-400']) }}>
    {{ $value ?? $slot }}
</label>
