@extends('layouts.app')

@section('public')
@endsection

@section('og_title', config('app.name'))
@section('og_description', 'Sistema de apoio à vigilância entomológica e controle vetorial municipal, em conformidade com as recomendações do Ministério da Saúde (Lei 11.350/2006, Diretrizes ACE/ACS, Arboviroses). Acesso para gestores, ACE, ACS e consulta pública.')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-white dark:bg-gray-900 px-6 sm:px-8 md:px-12 py-12 min-w-full">
    <div class="max-w-7xl w-full grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">

        {{-- Mobile only --}}
        <div class="flex lg:hidden justify-center mt-8">
            <div class="w-64">
                @include('components.welcome-illustration')
            </div>
        </div>

        {{-- Lado esquerdo: texto e ações --}}
        <div class="space-y-8 md:pl-4 max-w-xl" id="anim-texto">
            <div class="space-y-4">
                <h1 class="text-4xl font-extrabold leading-tight text-gray-900 dark:text-white">
                    Bem-vindo(a) ao <span class="text-blue-600 dark:text-blue-400">Visita Aí</span>
                </h1>
                @if ($local)
                    <p class="text-lg font-medium text-blue-700 dark:text-blue-300">
                        {{ $local->loc_cidade }}/{{ $local->loc_estado }}
                    </p>
                @endif
                <p class="text-gray-600 dark:text-gray-400 text-base leading-relaxed max-w-md">
                    Sistema de apoio à vigilância entomológica e controle vetorial municipal. Acompanhe, consulte e controle visitas de forma ágil e segura.
                </p>
            </div>

            <div class="flex flex-col sm:flex-row gap-4" id="anim-botoes">
                <a href="{{ route('login') }}"
                   class="btn-acesso-principal inline-flex items-center justify-center gap-2 rounded-xl px-6 py-3 font-semibold text-white shadow-sm transition group focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-gray-900">
                    <svg class="h-5 w-5 transform transition-transform duration-200 group-hover:-translate-x-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                    Entrar no sistema
                </a>

                <a href="{{ route('consulta.index') }}"
                   class="group inline-flex items-center justify-center gap-2 rounded-xl border border-gray-200 bg-gray-50 px-6 py-3 font-semibold text-gray-900 shadow-sm transition hover:bg-gray-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500/50 dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:hover:bg-gray-700">
                    <svg class="h-5 w-5 transform transition-transform duration-200 group-hover:-translate-x-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01" />
                    </svg>
                    Consulta Pública
                </a>
            </div>

            {{-- Conformidade MS --}}
            <div class="mt-6 rounded-xl border border-blue-200/90 bg-blue-50/90 px-4 py-3 dark:border-blue-800 dark:bg-blue-950/35" id="anim-conformidade">
                <p class="flex items-center gap-2 text-sm font-semibold text-blue-950 dark:text-blue-100">
                    <svg class="h-5 w-5 shrink-0 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    Conformidade com o Ministério da Saúde
                </p>
                <p class="mt-1 text-xs leading-relaxed text-blue-900/90 dark:text-blue-200/90">
                    Este sistema está <strong>em conformidade</strong> com as recomendações do MS: Lei nº 11.350/2006 (ACE e ACS), Diretriz Nacional para Atuação Integrada dos ACE e ACS, Diretrizes Nacionais para Prevenção e Controle das Arboviroses Urbanas e PNCD.
                </p>
            </div>

            <p class="text-xs text-gray-400 dark:text-gray-500 mt-6" id="anim-footer">
                &copy; {{ (int) date('Y') <= 2025 ? '2025' : '2025-'.date('Y') }} Visita Aí · Desenvolvido por <a href="https://bitwise.dev.br" target="_blank" rel="noopener" class="text-gray-600 hover:text-gray-700 dark:text-gray-400">Bitwise Technologies</a>
            </p>
        </div>

        {{-- Desktop only --}}
        <div class="hidden lg:flex justify-center" id="anim-ilustracao">
            <div class="w-full max-w-md">
                @include('components.welcome-illustration')
            </div>
        </div>

    </div>
</div>

<!-- AOS JS -->
<link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script>
    if (window.innerWidth >= 768) {
        // Aplica data-aos apenas no desktop
        document.getElementById('anim-texto')?.setAttribute('data-aos', 'fade-left');
        document.getElementById('anim-botoes')?.setAttribute('data-aos', 'fade-left');
        document.getElementById('anim-conformidade')?.setAttribute('data-aos', 'fade-left');
        document.getElementById('anim-footer')?.setAttribute('data-aos', 'fade-left');
        document.getElementById('anim-ilustracao')?.setAttribute('data-aos', 'fade-left');

        AOS.init({
            duration: 800,
            once: true
        });
    }
</script>
@endsection