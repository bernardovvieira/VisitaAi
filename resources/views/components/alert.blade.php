@props(['type' => 'error', 'message', 'autodismiss' => null])

@php
    $isSuccess = $type === 'success';
    $isWarning = $type === 'warning';
    $autodismiss = $autodismiss ?? $isSuccess;
    $variant = $isSuccess ? 'v-alert--success' : ($isWarning ? 'v-alert--warning' : 'v-alert--error');
@endphp

@if ($message)
    <div role="alert"
         class="v-alert {{ $variant }}"
         @if($autodismiss) data-alert-autodismiss="5000" @endif>
        {{ $message }}
    </div>
@endif
