@props(['value'])

<label {{ $attributes->merge(['class' => 'mb-1 block text-xs font-semibold tracking-tight text-slate-600 dark:text-slate-400']) }}>
    {{ $value ?? $slot }}
</label>
