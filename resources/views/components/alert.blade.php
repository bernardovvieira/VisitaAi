@props(['type' => 'error', 'message', 'title' => null, 'autodismiss' => null])

@php
    $isSuccess = $type === 'success';
    $isWarning = $type === 'warning';
    $isInfo = $type === 'info';
    $autodismiss = $autodismiss ?? $isSuccess;
    $variant = $isSuccess
        ? 'v-alert--success'
        : ($isWarning ? 'v-alert--warning' : ($isInfo ? 'v-alert--info' : 'v-alert--error'));
    $role = $isInfo ? 'status' : 'alert';
@endphp

@if ($message)
    <div role="{{ $role }}"
         class="v-alert {{ $variant }}"
         @if($autodismiss) data-alert-autodismiss="5000" @endif>
        @if(filled($title))
            <p class="text-sm font-semibold leading-snug">{{ $title }}</p>
            <p class="mt-1 text-sm font-normal leading-snug">{{ $message }}</p>
        @else
            {{ $message }}
        @endif
    </div>
@endif
