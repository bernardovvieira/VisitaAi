@extends('layouts.app')

@section('public')
@endsection

@section('og_title', config('app.brand'))
@section('og_description', __('Visita Aí reúne consulta pública pelo código do imóvel, painéis com indicadores municipais para a gestão e registro de campo por ACE/ACS, alinhado às diretrizes do Ministério da Saúde.'))

@section('content')
<div class="flex min-h-screen min-w-full items-start justify-center px-2 py-8 sm:px-4 sm:py-12 md:px-8">
    <div class="max-w-7xl w-full grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-12 items-start">

        {{-- Mobile only --}}
        <div class="flex lg:hidden justify-center order-first lg:order-none">
            <div class="w-56 sm:w-64" data-aos="zoom-in" data-aos-duration="700">
                <x-welcome-illustration />
            </div>
        </div>

        {{-- Lado esquerdo: texto, benefícios, indicadores, ações --}}
        <div class="space-y-8 md:pl-4 max-w-xl lg:max-w-none lg:pr-4" id="welcome-col-principal">
            <div class="space-y-4" id="anim-texto">
                <h1 class="text-4xl font-extrabold leading-tight text-gray-900 dark:text-white">
                    {{ __('Bem-vindo(a) ao') }}
                    <span class="text-blue-600 dark:text-blue-400">{{ config('app.brand') }}</span>
                </h1>
                @if ($local)
                    <p class="text-lg font-medium text-blue-700 dark:text-blue-300">
                        {{ $local->loc_cidade }}/{{ $local->loc_estado }}
                    </p>
                @endif
                <p class="text-gray-600 dark:text-gray-400 text-base leading-relaxed max-w-2xl font-medium">
                    {{ __('Transparência para quem mora no imóvel, indicadores para quem gerencia o município e apoio digital a ACE e ACS — em um só lugar, com foco em dengue e outras arboviroses.') }}
                </p>
            </div>

            {{-- Benefícios: realce no cliente (hover + AOS) --}}
            <div class="space-y-3" id="welcome-beneficios-wrap">
                <h2 class="text-xs font-bold uppercase tracking-[0.12em] text-blue-600 dark:text-blue-400">
                    {{ __('Benefícios em destaque') }}
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <article
                        class="welcome-benefit-card group relative overflow-hidden rounded-2xl border border-slate-200/90 bg-gradient-to-br from-white to-slate-50/90 p-5 shadow-sm ring-1 ring-slate-900/[0.04] transition duration-300 ease-out hover:-translate-y-0.5 hover:border-blue-300/90 hover:shadow-md hover:shadow-blue-500/10 hover:ring-blue-500/15 dark:border-slate-700 dark:from-slate-900/80 dark:to-slate-900/40 dark:ring-white/[0.06] dark:hover:border-blue-500/50"
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
                        class="welcome-benefit-card group relative overflow-hidden rounded-2xl border border-slate-200/90 bg-gradient-to-br from-white to-slate-50/90 p-5 shadow-sm ring-1 ring-slate-900/[0.04] transition duration-300 ease-out hover:-translate-y-0.5 hover:border-blue-300/90 hover:shadow-md hover:shadow-blue-500/10 hover:ring-blue-500/15 dark:border-slate-700 dark:from-slate-900/80 dark:to-slate-900/40 dark:ring-white/[0.06] dark:hover:border-blue-500/50"
                        data-aos="fade-up"
                        data-aos-delay="80">
                        <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 text-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white">{{ __('Indicadores para gestão municipal') }}</h3>
                        <p class="mt-2 text-xs leading-relaxed text-slate-600 dark:text-slate-400 sm:text-sm">
                            {{ __('O gestor acompanha no sistema totais de visitas, pendências, distribuição por bairro, relatórios em PDF e painéis com perfil dos ocupantes do imóvel — base para planejamento e transparência interna.') }}
                        </p>
                    </article>
                    <article
                        class="welcome-benefit-card group relative overflow-hidden rounded-2xl border border-slate-200/90 bg-gradient-to-br from-white to-slate-50/90 p-5 shadow-sm ring-1 ring-slate-900/[0.04] transition duration-300 ease-out hover:-translate-y-0.5 hover:border-blue-300/90 hover:shadow-md hover:shadow-blue-500/10 hover:ring-blue-500/15 dark:border-slate-700 dark:from-slate-900/80 dark:to-slate-900/40 dark:ring-white/[0.06] dark:hover:border-blue-500/50"
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
                        class="welcome-benefit-card group relative overflow-hidden rounded-2xl border border-slate-200/90 bg-gradient-to-br from-white to-slate-50/90 p-5 shadow-sm ring-1 ring-slate-900/[0.04] transition duration-300 ease-out hover:-translate-y-0.5 hover:border-blue-300/90 hover:shadow-md hover:shadow-blue-500/10 hover:ring-blue-500/15 dark:border-slate-700 dark:from-slate-900/80 dark:to-slate-900/40 dark:ring-white/[0.06] dark:hover:border-blue-500/50"
                        data-aos="fade-up"
                        data-aos-delay="240">
                        <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-xl bg-violet-100 text-violet-800 dark:bg-violet-950/80 dark:text-violet-300">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                            </svg>
                        </div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white">{{ __('Relatórios, gráficos e mapa de calor') }}</h3>
                        <p class="mt-2 text-xs leading-relaxed text-slate-600 dark:text-slate-400 sm:text-sm">
                            {{ __('Gere PDFs com indicadores do período, acompanhe gráficos por bairro, evolução diária e tratamentos, e visualize no mapa de calor onde há mais visitas com localização — apoio à gestão da vigilância.') }}
                        </p>
                    </article>
                </div>
            </div>

            {{-- Indicadores municipais (citado para gestão; acesso via login) --}}
            <div
                class="relative overflow-hidden rounded-2xl border border-indigo-200/80 bg-gradient-to-br from-indigo-50/95 via-white to-sky-50/80 px-5 py-4 dark:border-indigo-500/30 dark:from-indigo-950/40 dark:via-slate-900/60 dark:to-sky-950/30"
                id="welcome-indicadores"
                data-aos="fade-up"
                data-aos-delay="100">
                <div class="pointer-events-none absolute -right-8 -top-8 h-24 w-24 rounded-full bg-indigo-400/20 blur-2xl dark:bg-indigo-400/10" aria-hidden="true"></div>
                <h2 class="text-sm font-bold text-indigo-950 dark:text-indigo-200">
                    {{ __('Indicadores municipais no painel do gestor') }}
                </h2>
                <p class="mt-2 text-sm leading-relaxed text-indigo-900/85 dark:text-indigo-100/85">
                    {{ __('Além das visitas, o município dispõe de visões agregadas: produção por período, território (bairros) e dados complementares de ocupação do imóvel quando cadastrados — sempre com critérios de privacidade na área pública.') }}
                </p>
            </div>

            <div class="flex flex-col sm:flex-row gap-4" id="anim-botoes">
                <a href="{{ route('login') }}"
                   class="v-btn-primary group gap-2 focus-visible:ring-blue-500/40">
                    <svg class="h-5 w-5 transform transition-transform duration-200 group-hover:-translate-x-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                    {{ __('Acessar o sistema') }}
                </a>

                <a href="{{ route('consulta.index') }}"
                   class="v-btn-secondary group gap-2">
                    <svg class="h-5 w-5 transform transition-transform duration-200 group-hover:-translate-x-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01" />
                    </svg>
                    {{ __('Verificar visitas no imóvel') }}
                </a>
            </div>

            {{-- Conformidade MS --}}
            <div class="rounded-xl border border-blue-200/90 bg-blue-50/90 px-4 py-3 dark:border-blue-800 dark:bg-blue-950/35" id="anim-conformidade">
                <p class="flex items-center gap-2 text-sm font-semibold text-blue-950 dark:text-blue-100">
                    <svg class="h-5 w-5 shrink-0 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    {{ __('Alinhado às diretrizes do Ministério da Saúde') }}
                </p>
                <p class="mt-1 text-xs leading-relaxed text-blue-900/90 dark:text-blue-200/90">
                    {{ __('O Visita Aí segue a Lei nº 11.350/2006 (ACE e ACS), a diretriz de atuação integrada desses profissionais, as diretrizes nacionais de arboviroses urbanas e o Programa Nacional de Controle da Dengue (PNCD), como referência para o trabalho em campo e a transparência para a população.') }}
                </p>
            </div>

            <p class="text-xs text-gray-400 dark:text-gray-500" id="anim-footer">
                &copy; {{ date('Y') }} Visita Aí · {{ __('Desenvolvido por') }} <a href="https://bitwise.dev.br" target="_blank" rel="noopener noreferrer" class="text-gray-600 hover:text-gray-700 dark:text-gray-400">Bitwise Technologies</a>
            </p>
        </div>

        {{-- Desktop only --}}
        <div class="hidden lg:flex justify-center lg:sticky lg:top-8" id="anim-ilustracao">
            <div class="w-full max-w-md" data-aos="fade-left" data-aos-duration="700">
                <x-welcome-illustration />
            </div>
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
            offset: 32,
            easing: 'ease-out-cubic',
        });
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initWelcomeMotion);
    } else {
        initWelcomeMotion();
    }

    // Reforço no cliente: pulse discreto nos cards ao entrar no viewport (acessível: apenas classe visual)
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
