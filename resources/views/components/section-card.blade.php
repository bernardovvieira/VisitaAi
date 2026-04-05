@props([
    'title' => null,
    'headingId' => null,
    'heading' => 'h2',
])

@php
    $headingTag = in_array($heading, ['h2', 'h3'], true) ? $heading : 'h2';
@endphp

<section {{ $attributes->class(['v-card']) }}>
    @if(filled($title))
        @if($headingTag === 'h3')
            <h3 @if($headingId) id="{{ $headingId }}" @endif class="v-section-title mb-3">{{ $title }}</h3>
        @else
            <h2 @if($headingId) id="{{ $headingId }}" @endif class="v-section-title mb-4">{{ $title }}</h2>
        @endif
    @endif
    {{ $slot }}
</section>
