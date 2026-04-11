@extends('layouts.app')

@section('public')
@endsection

@section('og_title', config('app.name') . ' · ' . __('Página Inicial'))
@section('og_description', __('Plataforma municipal: indicadores e painéis com abrangência ampla, alimentados por cadastro de imóveis e visitas de campo. Vigilância entomológica, LIRAa e PNCD são funções especializadas quando o município adota. Consulta pública por código, sem dados clínicos.'))

@section('content')
<div class="welcome-public welcome-public--extend w-full min-w-0">
    <div class="public-page__stack">
        <x-public.page-hero :kicker="__('Indicadores municipais · operação em campo · transparência')" id="welcome-col-principal">
            <x-slot name="heading">
                <div class="flex flex-wrap items-center justify-between gap-x-3 gap-y-2">
                    <h1 class="welcome-public__title min-w-0 shrink">
                        {{ __('Bem-vindo(a) ao') }}
                        <span class="font-medium text-slate-800 dark:text-slate-100">{{ config('app.brand') }}</span>
                    </h1>
                    <x-public-municipality-pill :local="$local" class="shrink-0" />
                </div>
            </x-slot>
            <x-slot name="leadFull">
                <p class="welcome-public__lead welcome-public__lead--full">
                    {{ __('O Visita Aí reúne painéis e indicadores municipais amplos, alimentados por cadastro, visitas e dados opcionais. Vigilância em saúde (LIRAa/PNCD) é especialização quando adotada; transparência ao morador por código, sem dado clínico.') }}
                </p>
            </x-slot>
        </x-public.page-hero>

        <section aria-label="{{ __('Acesso') }}" class="grid grid-cols-1 gap-3 sm:grid-cols-2 sm:gap-4">
            <a href="{{ route('login') }}"
               class="v-btn-primary flex min-h-[2.875rem] w-full items-center justify-center gap-2 px-5 text-sm font-medium">
                {{ __('Acessar o sistema') }}
            </a>
            <a href="{{ route('consulta.index') }}"
               class="v-btn-secondary flex min-h-[2.875rem] w-full items-center justify-center gap-2 px-5 text-sm font-medium">
                {{ __('Verificar visitas no imóvel') }}
            </a>
        </section>

        <section aria-labelledby="welcome-pilares-heading" class="grid grid-cols-1 items-stretch gap-4 md:grid-cols-3 md:gap-5">
            <h2 id="welcome-pilares-heading" class="sr-only">{{ __('Em destaque') }}</h2>
            <article class="welcome-public__surface flex h-full flex-col p-6">
                <h3 class="public-card-eyebrow">{{ __('Indicadores e painéis') }}</h3>
                <p class="public-card-text">
                    {{ __('Painéis e números por período, território, visitas e imóveis, conforme o que cada perfil pode acessar. Relatórios e exportações apoiam gestão e prestação de contas. O escopo é amplo e vai além de um único tipo de visita.') }}
                </p>
            </article>
            <article class="welcome-public__surface flex h-full flex-col p-6">
                <h3 class="public-card-eyebrow">{{ __('Operação, cadastro e visitas') }}</h3>
                <p class="public-card-text">
                    {{ __('Cadastro de imóveis e registro de visitas seguem o fluxo definido pelo município. Quando adotado, integram-se rotinas de vigilância em saúde (visitas entomológicas, LIRAa e PNCD), em linha com o MS e a Lei 11.350/2006. O mesmo cadastro territorial pode apoiar outras frentes, com ou sem visita vetorial.') }}
                </p>
            </article>
            <article class="welcome-public__surface flex h-full flex-col p-6">
                <h3 class="public-card-eyebrow">{{ __('Consulta pública') }}</h3>
                <p class="public-card-text">
                    {{ __('Com o código do imóvel, o morador consulta visitas de campo, datas e pendências, sem criar conta. Dados clínicos e cadastro complementar do imóvel não aparecem nesta área pública.') }}
                </p>
            </article>
        </section>

        <section class="border-t border-slate-200/50 pt-8 dark:border-slate-800/70" aria-labelledby="welcome-laws-heading">
            <div class="welcome-public__surface space-y-4 p-6">
                <h2 id="welcome-laws-heading" class="public-section-title">{{ __('Leis e normas em síntese') }}</h2>
                <p class="public-prose max-w-none">
                    {{ __('Fundamentos federais: Leis 11.350/2006 (ACS e ACE), 8.080/1990 (SUS) e 13.709/2018 (LGPD). Orientação técnica: MS / PNCD e arboviroses.') }}
                </p>
                <div class="flex flex-wrap gap-2">
                    <a href="https://www.planalto.gov.br/ccivil_03/_ato2004-2006/2006/lei/l11350.htm" class="public-chip-link" target="_blank" rel="noopener noreferrer">11.350/2006</a>
                    <a href="https://www.planalto.gov.br/ccivil_03/leis/l8080.htm" class="public-chip-link" target="_blank" rel="noopener noreferrer">8.080/1990</a>
                    <a href="https://www.planalto.gov.br/ccivil_03/_ato2015-2018/2018/lei/l13709.htm" class="public-chip-link" target="_blank" rel="noopener noreferrer">LGPD · 13.709/2018</a>
                </div>
                <p class="text-xs leading-relaxed text-slate-400 dark:text-slate-500">
                    {{ __('Textos no Planalto; orientações sanitárias atualizadas no portal do Ministério da Saúde. Onde houver vigilância em saúde no município, aplicam-se também as diretrizes para ACE, ACS e vigilância entomológica.') }}
                </p>
            </div>
        </section>

        @include('partials.public-copyright-footer', ['footerClass' => 'welcome-public__footer'])
    </div>
</div>
@endsection
