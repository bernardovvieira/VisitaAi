@extends('layouts.app')

@section('public')
@endsection

@section('og_title', config('app.name') . ' · ' . __('Página Inicial'))
@section('og_description', __('Plataforma municipal: indicadores e painéis com abrangência ampla, alimentados por cadastro de imóveis e visitas de campo. Vigilância entomológica, LIRAa e PNCD são funções especializadas quando o município adota. Consulta pública por código, sem dados clínicos.'))

@section('content')
<div class="welcome-public w-full min-w-0">
    <div class="w-full space-y-11 lg:space-y-12">
        {{-- Cabeçalho --}}
        <header class="welcome-public__hero" id="welcome-col-principal">
            <div class="welcome-public__hero-row">
                <img
                    src="{{ asset('images/visitaai_rembg.png') }}"
                    alt="{{ __('Marca do aplicativo') }}, {{ config('app.brand') }}"
                    width="96"
                    height="96"
                    class="welcome-public__logo"
                    decoding="async" />
                <div class="welcome-public__hero-content">
                    <p class="welcome-public__kicker">
                        {{ __('Indicadores municipais · operação em campo · transparência') }}
                    </p>
                    <div class="flex flex-wrap items-center justify-between gap-x-3 gap-y-2">
                        <h1 class="welcome-public__title min-w-0 shrink">
                            {{ __('Bem-vindo(a) ao') }}
                            <span class="font-medium text-slate-800 dark:text-slate-100">{{ config('app.brand') }}</span>
                        </h1>
                        <x-public-municipality-pill :local="$local" class="shrink-0" />
                    </div>
                    <p class="welcome-public__lead">
                        {{ __('O Visita Aí organiza a gestão municipal em torno de painéis e indicadores de amplo alcance. Cadastro de imóveis, visitas de campo e dados complementares opcionais alimentam esses números. Visitas de vigilância entomológica, LIRAa e fluxos do PNCD são funções especializadas da operação em saúde quando o município as utiliza — não resumem o produto. A consulta pública por código reforça a transparência sem exibir dado clínico.') }}
                    </p>
                </div>
            </div>
        </header>

        {{-- Ações: largura total em duas colunas iguais --}}
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

        {{-- Três cartões: mesma estrutura, altura alinhada, texto equilibrado --}}
        <section aria-labelledby="welcome-pilares-heading" class="grid grid-cols-1 gap-4 md:grid-cols-3 md:gap-5">
            <h2 id="welcome-pilares-heading" class="sr-only">{{ __('Em destaque') }}</h2>
            <article class="welcome-public__surface flex min-h-[11rem] flex-col p-6">
                <h3 class="text-[11px] font-medium uppercase tracking-[0.14em] text-slate-400 dark:text-slate-500">
                    {{ __('Indicadores e painéis') }}</h3>
                <p class="mt-3 flex-1 text-sm leading-relaxed text-slate-600 dark:text-slate-400">
                    {{ __('O que oferece: leituras por período, território, visitas, imóveis e agregados permitidos por perfil, com relatórios e exportações. Objetivo: apoiar decisão, planejamento e prestação de contas. O escopo dos indicadores é amplo e não se limita a um único tipo de visita.') }}
                </p>
            </article>
            <article class="welcome-public__surface flex min-h-[11rem] flex-col p-6">
                <h3 class="text-[11px] font-medium uppercase tracking-[0.14em] text-slate-400 dark:text-slate-500">
                    {{ __('Operação, cadastro e visitas') }}</h3>
                <p class="mt-3 flex-1 text-sm leading-relaxed text-slate-600 dark:text-slate-400">
                    {{ __('Como usar: cadastre imóveis e registre visitas conforme o fluxo local. Função especializada: quando o município adota, há rotinas de vigilância em saúde — visitas entomológicas, LIRAa e PNCD — alinhadas à Lei 11.350/2006 e às diretrizes do MS. O mesmo cadastro territorial pode apoiar outras frentes, com ou sem visita vetorial.') }}
                </p>
            </article>
            <article class="welcome-public__surface flex min-h-[11rem] flex-col p-6">
                <h3 class="text-[11px] font-medium uppercase tracking-[0.14em] text-slate-400 dark:text-slate-500">
                    {{ __('Consulta pública') }}</h3>
                <p class="mt-3 flex-1 text-sm leading-relaxed text-slate-600 dark:text-slate-400">
                    {{ __('Morador consulta o histórico de visitas pelo código do imóvel, sem cadastro e sem expor dados clínicos.') }}
                </p>
            </article>
        </section>

        <p class="max-w-3xl text-sm leading-relaxed text-slate-500 dark:text-slate-400">
            {{ __('Indicadores, operação em campo e transparência ao cidadão integram-se no mesmo ambiente, mas cada município define o que ativa — inclusive se usará ou não as funções especializadas de vigilância em saúde.') }}
        </p>

        {{-- Base legal compacta, largura total --}}
        <section class="border-t border-slate-200/50 pt-9 dark:border-slate-800/70" aria-labelledby="welcome-laws-heading">
            <h2 id="welcome-laws-heading" class="text-[11px] font-medium uppercase tracking-[0.14em] text-slate-400 dark:text-slate-500">
                {{ __('Leis e normas em síntese') }}</h2>
            <p class="mt-3 max-w-2xl text-sm leading-relaxed text-slate-500 dark:text-slate-400 lg:max-w-3xl xl:max-w-4xl">
                {{ __('Fundamentos federais: Leis 11.350/2006 (ACS e ACE), 8.080/1990 (SUS) e 13.709/2018 (LGPD). Orientação técnica: MS / PNCD e arboviroses.') }}
            </p>
            <div class="mt-4 flex flex-wrap gap-2">
                <a href="https://www.planalto.gov.br/ccivil_03/_ato2004-2006/2006/lei/l11350.htm" class="inline-flex rounded border border-slate-200/80 bg-white/80 px-2.5 py-1.5 text-[11px] font-medium text-slate-600 transition hover:border-slate-300 hover:text-slate-800 dark:border-slate-600/70 dark:bg-slate-900/40 dark:text-slate-300 dark:hover:border-slate-500" target="_blank" rel="noopener noreferrer">11.350/2006</a>
                <a href="https://www.planalto.gov.br/ccivil_03/leis/l8080.htm" class="inline-flex rounded border border-slate-200/80 bg-white/80 px-2.5 py-1.5 text-[11px] font-medium text-slate-600 transition hover:border-slate-300 hover:text-slate-800 dark:border-slate-600/70 dark:bg-slate-900/40 dark:text-slate-300 dark:hover:border-slate-500" target="_blank" rel="noopener noreferrer">8.080/1990</a>
                <a href="https://www.planalto.gov.br/ccivil_03/_ato2015-2018/2018/lei/l13709.htm" class="inline-flex rounded border border-slate-200/80 bg-white/80 px-2.5 py-1.5 text-[11px] font-medium text-slate-600 transition hover:border-slate-300 hover:text-slate-800 dark:border-slate-600/70 dark:bg-slate-900/40 dark:text-slate-300 dark:hover:border-slate-500" target="_blank" rel="noopener noreferrer">LGPD · 13.709/2018</a>
            </div>
            <p class="mt-4 text-[11px] leading-relaxed text-slate-400 dark:text-slate-500">
                {{ __('Textos no Planalto; orientações sanitárias atualizadas no portal do Ministério da Saúde. Onde houver vigilância em saúde no município, aplicam-se também as diretrizes para ACE, ACS e vigilância entomológica.') }}
            </p>
        </section>

        @include('partials.public-copyright-footer', ['footerClass' => 'welcome-public__footer'])
    </div>
</div>
@endsection
