@props(['type' => 'error', 'message', 'autodismiss' => null])

@php
    $isSuccess = $type === 'success';
    $isWarning = $type === 'warning';
    $autodismiss = $autodismiss ?? $isSuccess;
    $classes = $isSuccess
        ? 'border border-emerald-500 bg-emerald-100 text-emerald-950 dark:border-emerald-700 dark:bg-emerald-950/35 dark:text-emerald-50'
        : ($isWarning
            ? 'border border-amber-500 bg-amber-100 text-amber-950 dark:border-amber-700 dark:bg-amber-950/40 dark:text-amber-50'
            : 'border border-red-500 bg-red-100 text-red-950 dark:border-red-800 dark:bg-red-950/40 dark:text-red-50');
@endphp

@if ($message)
    <div role="alert"
         class="mb-4 rounded-lg border px-3.5 py-2.5 text-sm font-medium leading-snug shadow-sm {{ $classes }}"
         @if($autodismiss) data-alert-autodismiss="5000" @endif>
        {{ $message }}
    </div>
@endif
