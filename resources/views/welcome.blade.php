@extends('layouts.app')

@section('public')
@endsection

@section('og_title', config('app.brand'))
@section('og_description', __('Visita Aí reúne consulta pública pelo código do imóvel, painéis com indicadores municipais para a gestão e registro de campo por ACE/ACS, alinhado às diretrizes do Ministério da Saúde.'))

@section('content')
<div class="welcome-public flex min-h-[calc(100vh-3rem)] min-w-full items-start justify-center px-4 py-6 sm:px-6 sm:py-10 md:py-12 lg:px-8">
    <div class="welcome-public__grid w-full max-w-screen-2xl space-y-10">

        {{-- Conteúdo principal em largura útil maior (sem coluna de ilustração) --}}
        <div class="welcome-public__main space-y-8" id="welcome-col-principal">

            {{-- Cabeçalho: logo + título + município + lead --}}
            <header class="welcome-public__hero space-y-4" id="anim-texto">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:gap-6 lg:gap-10 xl:items-center">
                    <img
                        src="{{ asset('images/visitaai_rembg.png') }}"
                        alt="{{ __('Marca do aplicativo') }}, {{ config('app.brand') }}"
                        width="64"
                        height="64"
                        class="welcome-public__logo h-14 w-14 shrink-0 object-contain sm:h-16 sm:w-16"
                        decoding="async" />
                    <div class="min-w-0 flex-1 space-y-1">
                        <p class="text-[11px] font-bold uppercase leading-snug tracking-[0.14em] text-blue-600 dark:text-blue-400 sm:text-xs">
                            {{ __('Vigilância entomológica e controle de vetores') }}
                        </p>
                        <h1 class="text-balance text-3xl font-extrabold leading-tight text-gray-900 dark:text-white sm:text-4xl">
                            {{ __('Bem-vindo(a) ao') }}
                            <span class="text-blue-600 dark:text-blue-400">{{ config('app.brand') }}</span>
                        </h1>
                    </div>
                </div>

                @if ($local)
                    <p class="inline-flex items-center gap-2 rounded-full border border-blue-200/90 bg-blue-50/90 px-3 py-1 text-sm font-semibold text-blue-800 dark:border-blue-800/60 dark:bg-blue-950/50 dark:text-blue-200">
                        <svg class="h-4 w-4 shrink-0 opacity-80" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        {{ $local->loc_cidade }}/{{ $local->loc_estado }}
                    </p>
                @endif

                <p class="text-pretty max-w-none text-base font-medium leading-relaxed text-gray-600 dark:text-gray-400 lg:max-w-4xl xl:max-w-5xl">
                    {{ __('Transparência para quem mora no imóvel, indicadores para quem gerencia o município e apoio digital a ACE e ACS. Em um só lugar, com foco em dengue e outras arboviroses.') }}
                </p>

                {{-- Mini-marketing: três pilares --}}
                <div
                    class="welcome-public__marketing mt-5 grid grid-cols-1 gap-3 sm:grid-cols-3"
                    aria-label="{{ __('Transparência · Gestão municipal · Campo conectado') }}">
                    <div class="flex items-start gap-3 rounded-2xl border border-slate-200/90 bg-white/80 px-4 py-3 shadow-sm ring-1 ring-slate-900/[0.03] dark:border-slate-600/80 dark:bg-slate-900/50 dark:ring-white/[0.05]">
                        <span class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-sky-100 text-sky-800 dark:bg-sky-950/80 dark:text-sky-200" aria-hidden="true">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </span>
                        <p class="text-sm font-semibold leading-snug text-slate-800 dark:text-slate-100">{{ __('Consulta pública com o código do imóvel. Registro digital para ACE e ACS. Indicadores para o planejamento.') }}</p>
                    </div>
                    <div class="flex items-start gap-3 rounded-2xl border border-slate-200/90 bg-white/80 px-4 py-3 shadow-sm ring-1 ring-slate-900/[0.03] dark:border-slate-600/80 dark:bg-slate-900/50 dark:ring-white/[0.05]">
                        <span class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-amber-100 text-amber-900 dark:bg-amber-950/80 dark:text-amber-200" aria-hidden="true">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </span>
                        <p class="text-sm font-semibold leading-snug text-slate-800 dark:text-slate-100">{{ __('Referência nacional em saúde pública e dados') }}</p>
                    </div>
                    <div class="flex items-start gap-3 rounded-2xl border border-slate-200/90 bg-white/80 px-4 py-3 shadow-sm ring-1 ring-slate-900/[0.03] dark:border-slate-600/80 dark:bg-slate-900/50 dark:ring-white/[0.05]">
                        <span class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-emerald-100 text-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-200" aria-hidden="true">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </span>
                        <p class="text-sm font-semibold leading-snug text-slate-800 dark:text-slate-100">{{ __('Transparência · Gestão municipal · Campo conectado') }}</p>
                    </div>
                </div>
            </header>

            {{-- Ações principais (logo após o resumo) --}}
            <section class="welcome-public__actions space-y-3" aria-label="{{ __('Acesse o sistema ou consulte seu imóvel') }}">
                <h2 class="text-xs font-bold uppercase tracking-[0.12em] text-slate-500 dark:text-slate-400">
                    {{ __('Acesse o sistema ou consulte seu imóvel') }}
                </h2>
                <div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap" id="anim-botoes">
                    <a href="{{ route('login') }}"
                       class="v-btn-primary group justify-center gap-2 focus-visible:ring-blue-500/40 sm:min-w-[12rem]">
                        <svg class="h-5 w-5 shrink-0 transition-transform duration-200 group-hover:-translate-x-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                        </svg>
                        {{ __('Acessar o sistema') }}
                    </a>
                    <a href="{{ route('consulta.index') }}"
                       class="v-btn-secondary group justify-center gap-2 sm:min-w-[12rem]">
                        <svg class="h-5 w-5 shrink-0 transition-transform duration-200 group-hover:-translate-x-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01" />
                        </svg>
                        {{ __('Verificar visitas no imóvel') }}
                    </a>
                </div>
            </section>

            {{-- Benefícios em painel único --}}
            <section
                class="welcome-public__benefits rounded-3xl border border-slate-200/90 bg-white/70 p-5 shadow-sm ring-1 ring-slate-900/[0.03] dark:border-slate-700 dark:bg-slate-900/45 dark:ring-white/[0.05] sm:p-6"
                id="welcome-beneficios-wrap"
                aria-labelledby="welcome-beneficios-heading">
                <h2 id="welcome-beneficios-heading" class="mb-4 text-xs font-bold uppercase tracking-[0.12em] text-blue-600 dark:text-blue-400">
                    {{ __('Benefícios em destaque') }}
                </h2>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:gap-5 xl:grid-cols-4">
                    <article
                        class="welcome-benefit-card group relative overflow-hidden rounded-2xl border border-slate-200/90 bg-gradient-to-br from-white to-slate-50/90 p-5 shadow-sm ring-1 ring-slate-900/[0.04] transition duration-300 ease-out hover:-translate-y-0.5 hover:border-blue-300/90 hover:shadow-md hover:shadow-blue-500/10 hover:ring-blue-500/15 dark:border-slate-600/80 dark:from-slate-900/80 dark:to-slate-900/40 dark:ring-white/[0.06] dark:hover:border-blue-500/50"
                        data-aos="fade-up"
                        data-aos-delay="0">
                        <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-xl bg-blue-100 text-blue-700 dark:bg-blue-950/80 dark:text-blue-300">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white">{{ __('Consulta pelo código, sem cadastro') }}</h3>
                        <p class="mt-2 text-xs leading-relaxed text-slate-600 dark:text-slate-400 sm:text-sm">
                            {{ __('Veja as datas das visitas de vigilância vinculadas ao seu imóvel usando só o código que o ACE ou ACS deixou no endereço. Rápido, gratuito e sem expor dados clínicos na internet.') }}
                        </p>
                    </article>
                    <article
                        class="welcome-benefit-card group relative overflow-hidden rounded-2xl border border-slate-200/90 bg-gradient-to-br from-white to-slate-50/90 p-5 shadow-sm ring-1 ring-slate-900/[0.04] transition duration-300 ease-out hover:-translate-y-0.5 hover:border-blue-300/90 hover:shadow-md hover:shadow-blue-500/10 hover:ring-blue-500/15 dark:border-slate-600/80 dark:from-slate-900/80 dark:to-slate-900/40 dark:ring-white/[0.06] dark:hover:border-blue-500/50"
                        data-aos="fade-up"
                        data-aos-delay="80">
                        <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 text-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white">{{ __('Indicadores para gestão municipal') }}</h3>
                        <p class="mt-2 text-xs leading-relaxed text-slate-600 dark:text-slate-400 sm:text-sm">
                            {{ __('O gestor acompanha no sistema totais de visitas, pendências, distribuição por bairro, relatórios em PDF e painéis com perfil dos ocupantes do imóvel: base para planejamento e transparência interna.') }}
                        </p>
                    </article>
                    <article
                        class="welcome-benefit-card group relative overflow-hidden rounded-2xl border border-slate-200/90 bg-gradient-to-br from-white to-slate-50/90 p-5 shadow-sm ring-1 ring-slate-900/[0.04] transition duration-300 ease-out hover:-translate-y-0.5 hover:border-blue-300/90 hover:shadow-md hover:shadow-blue-500/10 hover:ring-blue-500/15 dark:border-slate-600/80 dark:from-slate-900/80 dark:to-slate-900/40 dark:ring-white/[0.06] dark:hover:border-blue-500/50"
                        data-aos="fade-up"
                        data-aos-delay="160">
                        <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-xl bg-amber-100 text-amber-900 dark:bg-amber-950/80 dark:text-amber-200">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white">{{ __('Campo conectado à norma') }}</h3>
                        <p class="mt-2 text-xs leading-relaxed text-slate-600 dark:text-slate-400 sm:text-sm">
                            {{ __('ACE e ACS registram visitas alinhadas à Lei 11.350/2006 e às diretrizes nacionais; é possível trabalhar offline e sincronizar quando a conexão voltar.') }}
                        </p>
                    </article>
                    <article
                        class="welcome-benefit-card group relative overflow-hidden rounded-2xl border border-slate-200/90 bg-gradient-to-br from-white to-slate-50/90 p-5 shadow-sm ring-1 ring-slate-900/[0.04] transition duration-300 ease-out hover:-translate-y-0.5 hover:border-blue-300/90 hover:shadow-md hover:shadow-blue-500/10 hover:ring-blue-500/15 dark:border-slate-600/80 dark:from-slate-900/80 dark:to-slate-900/40 dark:ring-white/[0.06] dark:hover:border-blue-500/50"
                        data-aos="fade-up"
                        data-aos-delay="240">
                        <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-xl bg-violet-100 text-violet-800 dark:bg-violet-950/80 dark:text-violet-300">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                            </svg>
                        </div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white">{{ __('Relatórios, gráficos e mapa de calor') }}</h3>
                        <p class="mt-2 text-xs leading-relaxed text-slate-600 dark:text-slate-400 sm:text-sm">
                            {{ __('Gere PDFs com indicadores do período, acompanhe gráficos por bairro, evolução diária e tratamentos, e visualize no mapa de calor onde há mais visitas com localização. Apoio à gestão da vigilância.') }}
                        </p>
                    </article>
                </div>
            </section>

            {{-- Base legal (página inicial sem números consolidados públicos) --}}
            <div class="mx-auto max-w-3xl space-y-3" id="anim-conformidade">
                <section
                    class="welcome-laws-panel relative overflow-hidden rounded-2xl border-2 border-slate-300/80 bg-gradient-to-b from-slate-50 via-white to-slate-50/90 px-4 py-5 shadow-md ring-1 ring-slate-900/[0.06] dark:border-slate-500/50 dark:from-slate-900 dark:via-slate-900/95 dark:to-slate-950/90 dark:ring-white/[0.08] sm:px-6 sm:py-6"
                    aria-labelledby="welcome-laws-heading">
                    <div class="pointer-events-none absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-slate-400 via-slate-600 to-slate-400 opacity-80 dark:from-slate-500 dark:via-slate-300 dark:to-slate-500" aria-hidden="true"></div>
                    <div class="flex gap-4">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl border border-slate-300/60 bg-slate-100 text-slate-700 shadow-inner dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200">
                            <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1 space-y-3">
                            <div>
                                <h2 id="welcome-laws-heading" class="text-base font-extrabold tracking-tight text-slate-900 dark:text-white">
                                    {{ __('Leis e normas em síntese') }}
                                </h2>
                                <p class="mt-2 text-sm leading-relaxed text-slate-600 dark:text-slate-400">
                                    {{ __('O Visita Aí apoia vigilância em saúde e controle de vetores. Fundamentos: Lei 11.350/2006 (ACS e ACE), Lei 8.080/1990 (SUS) e Lei 13.709/2018 (LGPD). Portarias e notas técnicas do Ministério da Saúde (arboviroses, PNCD) orientam o operacional.') }}
                                </p>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <a href="https://www.planalto.gov.br/ccivil_03/_ato2004-2006/2006/lei/l11350.htm" class="inline-flex items-center rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-bold text-slate-800 shadow-sm transition hover:border-blue-400 hover:text-blue-700 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 dark:hover:border-blue-500 dark:hover:text-blue-300" target="_blank" rel="noopener noreferrer">Lei 11.350/2006</a>
                                <a href="https://www.planalto.gov.br/ccivil_03/leis/l8080.htm" class="inline-flex items-center rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-bold text-slate-800 shadow-sm transition hover:border-blue-400 hover:text-blue-700 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 dark:hover:border-blue-500 dark:hover:text-blue-300" target="_blank" rel="noopener noreferrer">Lei 8.080/1990</a>
                                <a href="https://www.planalto.gov.br/ccivil_03/_ato2015-2018/2018/lei/l13709.htm" class="inline-flex items-center rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-bold text-slate-800 shadow-sm transition hover:border-blue-400 hover:text-blue-700 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 dark:hover:border-blue-500 dark:hover:text-blue-300" target="_blank" rel="noopener noreferrer">LGPD 13.709/2018</a>
                            </div>
                            <p class="text-[11px] leading-snug text-slate-500 dark:text-slate-500">
                                {{ __('Textos legais consolidados no Planalto; orientações sanitárias atualizadas no portal do Ministério da Saúde.') }}
                            </p>
                        </div>
                    </div>
                </section>
                <p class="flex items-center gap-2 rounded-xl border border-blue-200/90 bg-blue-50/90 px-3 py-2.5 text-xs font-semibold text-blue-950 dark:border-blue-800 dark:bg-blue-950/40 dark:text-blue-100">
                    <svg class="h-4 w-4 shrink-0 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    {{ __('Alinhado às diretrizes do Ministério da Saúde para ACE, ACS e vigilância entomológica.') }}
                </p>
            </div>

            @include('partials.public-copyright-footer', ['footerClass' => 'welcome-public__footer', 'footerId' => 'anim-footer'])
        </div>

    </div>
</div>

<link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script>
(function () {
    function initWelcomeMotion() {
        if (typeof AOS === 'undefined') return;
        AOS.init({
            duration: 680,
            once: true,
            offset: 28,
            easing: 'ease-out-cubic',
        });
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initWelcomeMotion);
    } else {
        initWelcomeMotion();
    }

    var reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (!reduced && 'IntersectionObserver' in window) {
        var cards = document.querySelectorAll('.welcome-benefit-card');
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (e) {
                if (!e.isIntersecting) return;
                e.target.classList.add('welcome-benefit-card--seen');
                io.unobserve(e.target);
            });
        }, { rootMargin: '0px 0px -10% 0px', threshold: 0.15 });
        cards.forEach(function (el) { io.observe(el); });
    }
})();
</script>
<style>
.welcome-benefit-card--seen {
    box-shadow: 0 12px 40px -12px rgb(59 130 246 / 0.18);
}
.dark .welcome-benefit-card--seen {
    box-shadow: 0 12px 40px -12px rgb(59 130 246 / 0.12);
}
@media (prefers-reduced-motion: reduce) {
    .welcome-benefit-card {
        transition: none !important;
    }
    .welcome-benefit-card:hover {
        transform: none !important;
    }
}
</style>
@endsection
