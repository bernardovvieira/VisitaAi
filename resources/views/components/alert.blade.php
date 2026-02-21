@props(['type' => 'error', 'message', 'autodismiss' => null])

@php
    $isSuccess = $type === 'success';
    $isWarning = $type === 'warning';
    $autodismiss = $autodismiss ?? $isSuccess;
    $classes = $isSuccess
        ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200 border border-green-200 dark:border-green-800'
        : ($isWarning
            ? 'bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-200 border border-amber-200 dark:border-amber-800'
            : 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200 border border-red-200 dark:border-red-800');
@endphp

@if ($message)
    <div role="alert"
         class="p-3 mb-4 rounded {{ $classes }}"
         @if($autodismiss) data-alert-autodismiss="5000" @endif>
        {{ $message }}
    </div>
@endif
