{{-- Herói público: igual em home, consulta e resultado (logo + título; descrição largura total abaixo; ações opcionais). --}}
@props([
    'kicker' => '',
])

<header {{ $attributes->class(['welcome-public__hero']) }}>
    <div class="welcome-public__hero-row">
        <div class="public-page__hero-aside">
            <img
                src="{{ asset('images/visitaai.svg') }}"
                alt="{{ __('Marca do aplicativo') }}, {{ config('app.brand') }}"
                width="80"
                height="80"
                class="welcome-public__logo"
                decoding="async" />
        </div>
        <div class="welcome-public__hero-content">
            @if(filled($kicker))
                <p class="welcome-public__kicker">{{ $kicker }}</p>
            @endif
            {{ $heading }}
        </div>
    </div>
    @isset($leadFull)
        <div class="public-hero-lead-full">
            {{ $leadFull }}
        </div>
    @endisset
    @isset($actions)
        <div class="public-hero-actions">
            {{ $actions }}
        </div>
    @endisset
</header>
