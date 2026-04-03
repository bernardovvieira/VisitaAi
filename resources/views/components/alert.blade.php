@props(['type' => 'error', 'message', 'autodismiss' => null])

@php
    $isSuccess = $type === 'success';
    $isWarning = $type === 'warning';
    $autodismiss = $autodismiss ?? $isSuccess;
    $classes = $isSuccess
        ? 'bg-blue-50 dark:bg-blue-900/25 text-blue-950 dark:text-blue-100 border border-blue-200/90 dark:border-blue-800'
        : ($isWarning
            ? 'bg-amber-50 dark:bg-amber-900/25 text-amber-950 dark:text-amber-100 border border-amber-200/90 dark:border-amber-800'
            : 'bg-red-50 dark:bg-red-900/25 text-red-900 dark:text-red-100 border border-red-200/90 dark:border-red-800');
@endphp

@if ($message)
    <div role="alert"
         class="mb-4 rounded-xl p-4 text-sm leading-relaxed shadow-sm {{ $classes }}"
         @if($autodismiss) data-alert-autodismiss="5000" @endif>
        {{ $message }}
    </div>
@endif
