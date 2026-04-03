{{-- Cabeçalho de página estilo ERP: eyebrow (contexto) → título → lead opcional (slot nomeado "lead"). --}}
@props([
    'eyebrow' => null,
    'title',
])

<header {{ $attributes->merge(['class' => 'v-page-header']) }}>
    @if(filled($eyebrow))
        <p class="v-page-eyebrow">{{ $eyebrow }}</p>
    @endif
    <h1 class="v-page-title">{{ $title }}</h1>
    @isset($lead)
        <div class="v-page-lead [&>*:first-child]:mt-0">
            {{ $lead }}
        </div>
    @endisset
</header>
