@extends('layouts.app')

@section('public')
@endsection

@section('og_title', config('app.brand'))
@section('og_description', __('Consulta pelo código do imóvel, gestão municipal e registro de campo para ACE e ACS, alinhado às diretrizes do Ministério da Saúde.'))

@section('content')
<div class="welcome-public w-full min-w-0">
    <div class="w-full space-y-12 lg:space-y-14">
        {{-- Cabeçalho --}}
        <header class="border-b border-slate-200/80 pb-10 dark:border-slate-700/70" id="welcome-col-principal">
            <div class="flex min-w-0 flex-col gap-5 sm:flex-row sm:items-center sm:gap-8 lg:gap-10">
                <img
                    src="{{ asset('images/visitaai_rembg.png') }}"
                    alt="{{ __('Marca do aplicativo') }}, {{ config('app.brand') }}"
                    width="96"
                    height="96"
                    class="h-16 w-16 shrink-0 object-contain sm:h-20 sm:w-20 lg:h-24 lg:w-24"
                    decoding="async" />
                <div class="min-w-0 flex-1 space-y-3 sm:space-y-3.5">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-500 dark:text-slate-400">
                        {{ __('Vigilância entomológica e controle de vetores') }}
                    </p>
                    <x-public-municipality-pill :local="$local" />
                    <h1 class="text-balance text-3xl font-bold tracking-tight text-slate-900 dark:text-white sm:text-4xl lg:text-[2.5rem] lg:leading-[1.15]">
                        {{ __('Bem-vindo(a) ao') }}
                        <span class="text-blue-600 dark:text-blue-400">{{ config('app.brand') }}</span>
                    </h1>
                    <p class="max-w-3xl text-pretty text-[15px] leading-relaxed text-slate-600 dark:text-slate-400 sm:text-base">
                        {{ __('Consulta pelo código do imóvel, gestão municipal integrada e registro de campo alinhado ao SUS.') }}
                    </p>
                </div>
            </div>
        </header>

        {{-- Ações: largura total em duas colunas iguais --}}
        <section aria-label="{{ __('Acesso') }}" class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <a href="{{ route('login') }}"
               class="v-btn-primary flex min-h-[3rem] w-full items-center justify-center gap-2 px-5 text-[15px] font-semibold">
                {{ __('Acessar o sistema') }}
            </a>
            <a href="{{ route('consulta.index') }}"
               class="v-btn-secondary flex min-h-[3rem] w-full items-center justify-center gap-2 px-5 text-[15px] font-semibold">
                {{ __('Verificar visitas no imóvel') }}
            </a>
        </section>

        {{-- Três cartões: mesma estrutura, altura alinhada, texto equilibrado --}}
        <section aria-labelledby="welcome-pilares-heading" class="grid grid-cols-1 gap-5 md:grid-cols-3 md:gap-6">
            <h2 id="welcome-pilares-heading" class="sr-only">{{ __('Em destaque') }}</h2>
            <article class="flex min-h-[11.5rem] flex-col rounded-xl border border-slate-200/90 bg-white/90 p-5 shadow-sm dark:border-slate-700/80 dark:bg-slate-900/50 sm:p-6">
                <h3 class="text-[13px] font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">
                    {{ __('Consulta pública') }}</h3>
                <p class="mt-3 flex-1 text-[15px] leading-snug text-slate-700 dark:text-slate-300">
                    {{ __('Morador consulta o histórico de visitas pelo código do imóvel, sem cadastro e sem expor dados clínicos.') }}
                </p>
            </article>
            <article class="flex min-h-[11.5rem] flex-col rounded-xl border border-slate-200/90 bg-white/90 p-5 shadow-sm dark:border-slate-700/80 dark:bg-slate-900/50 sm:p-6">
                <h3 class="text-[13px] font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">
                    {{ __('Gestão municipal') }}</h3>
                <p class="mt-3 flex-1 text-[15px] leading-snug text-slate-700 dark:text-slate-300">
                    {{ __('Gestor centraliza visitas, território, relatórios e indicadores internos, com acessos restritos por perfil.') }}
                </p>
            </article>
            <article class="flex min-h-[11.5rem] flex-col rounded-xl border border-slate-200/90 bg-white/90 p-5 shadow-sm dark:border-slate-700/80 dark:bg-slate-900/50 sm:p-6">
                <h3 class="text-[13px] font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">
                    {{ __('Campo e legislação') }}</h3>
                <p class="mt-3 flex-1 text-[15px] leading-snug text-slate-700 dark:text-slate-300">
                    {{ __('ACE e ACS registram visitas em campo, alinhados à Lei 11.350/2006 e às diretrizes nacionais do MS.') }}
                </p>
            </article>
        </section>

        {{-- Base legal compacta, largura total --}}
        <section class="border-t border-slate-200/80 pt-10 dark:border-slate-700/70" aria-labelledby="welcome-laws-heading">
            <h2 id="welcome-laws-heading" class="text-[13px] font-semibold uppercase tracking-[0.1em] text-slate-500 dark:text-slate-400">
                {{ __('Leis e normas em síntese') }}</h2>
            <p class="mt-3 max-w-4xl text-[15px] leading-relaxed text-slate-600 dark:text-slate-400">
                {{ __('Fundamentos federais: Leis 11.350/2006 (ACS e ACE), 8.080/1990 (SUS) e 13.709/2018 (LGPD). Orientação técnica: MS / PNCD e arboviroses.') }}
            </p>
            <div class="mt-5 flex flex-wrap gap-2">
                <a href="https://www.planalto.gov.br/ccivil_03/_ato2004-2006/2006/lei/l11350.htm" class="inline-flex rounded-md border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-800 transition hover:border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 dark:hover:border-slate-500" target="_blank" rel="noopener noreferrer">11.350/2006</a>
                <a href="https://www.planalto.gov.br/ccivil_03/leis/l8080.htm" class="inline-flex rounded-md border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-800 transition hover:border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 dark:hover:border-slate-500" target="_blank" rel="noopener noreferrer">8.080/1990</a>
                <a href="https://www.planalto.gov.br/ccivil_03/_ato2015-2018/2018/lei/l13709.htm" class="inline-flex rounded-md border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-800 transition hover:border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 dark:hover:border-slate-500" target="_blank" rel="noopener noreferrer">LGPD · 13.709/2018</a>
            </div>
            <p class="mt-5 text-xs leading-relaxed text-slate-500 dark:text-slate-500">
                {{ __('Textos no Planalto; orientações sanitárias atualizadas no portal do Ministério da Saúde. Alinhado às diretrizes para ACE, ACS e vigilância entomológica.') }}
            </p>
        </section>

        @include('partials.public-copyright-footer', ['footerClass' => 'welcome-public__footer'])
    </div>
</div>
@endsection
