@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'rounded-xl border border-blue-200/90 bg-blue-50/85 px-3.5 py-2.5 text-sm font-semibold text-blue-800 shadow-sm backdrop-blur-sm dark:border-blue-800/55 dark:bg-blue-950/40 dark:text-blue-200']) }} role="status">
        {{ $status }}
    </div>
@endif
