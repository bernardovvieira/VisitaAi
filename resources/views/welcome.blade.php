@extends('layouts.app')

@section('public')
@endsection

@section('public_full_bleed')
@endsection

@section('og_title', config('app.name'))
@section('og_description', 'Plataforma municipal para operação territorial, indicadores e transparência. Acesso para gestores, ACE, ACS e consulta pública por código do imóvel.')

@section('content')
<div class="min-h-screen w-full flex items-center justify-center px-6 sm:px-8 md:px-12 py-12">
    <div class="max-w-7xl w-full grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">

        {{-- Mobile only --}}
        <div class="flex lg:hidden justify-center mt-8">
            <div class="w-64">
                <x-welcome-illustration />
            </div>
        </div>

        {{-- Lado esquerdo: texto e ações --}}
        <div class="space-y-8 md:pl-4 max-w-xl" id="anim-texto">
            <div class="space-y-4">
                <p class="v-page-eyebrow">Plataforma municipal</p>
                 <img src="{{ asset('images/visitaai.svg') }}"
                     alt="{{ config('app.name') }}"
                     class="h-14 w-auto object-contain"
                     width="96"
                     height="56" />
                <h1 class="v-page-title text-4xl sm:text-5xl">
                    Bem-vindo(a) ao <span class="text-blue-600 dark:text-blue-400">Visita Aí</span>
                </h1>
                @if ($local)
                    <p class="text-blue-600 dark:text-blue-400 text-lg font-medium">
                        {{ $local->loc_cidade }}/{{ $local->loc_estado }}
                    </p>
                @endif
                <p class="v-page-lead mt-1 max-w-md">
                    Plataforma municipal para operação territorial, indicadores e transparência.<br>
                    Organize visitas de campo, acompanhe resultados e ofereça consulta pública por código.
                </p>
            </div>

            <div class="flex flex-col sm:flex-row gap-4" id="anim-botoes">
                <a href="{{ route('login') }}"
                   class="v-btn-primary inline-flex items-center justify-center gap-2 px-6 py-3 group">
                    <svg class="h-5 w-5 transform transition-transform duration-200 group-hover:-translate-x-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                    Entrar no sistema
                </a>

                <a href="{{ route('consulta.index') }}"
                   class="v-btn-secondary inline-flex items-center justify-center gap-2 px-6 py-3 group">
                    <svg class="h-5 w-5 transform transition-transform duration-200 group-hover:-translate-x-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01" />
                    </svg>
                    Consulta Pública
                </a>
            </div>

            <p class="text-xs text-slate-500 dark:text-slate-400 mt-6" id="anim-footer">
                &copy; 2025–{{ date('Y') }} Visita Aí · Desenvolvido por <a href="https://bitwise.dev.br" target="_blank" rel="noopener" class="text-gray-700 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500/35 rounded-sm">Bitwise Technologies</a>
            </p>
        </div>

        {{-- Desktop only --}}
        <div class="hidden lg:flex justify-center" id="anim-ilustracao">
            <div class="w-full max-w-md">
                <x-welcome-illustration />
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
        document.getElementById('anim-footer')?.setAttribute('data-aos', 'fade-left');
        document.getElementById('anim-ilustracao')?.setAttribute('data-aos', 'fade-left');

        AOS.init({
            duration: 800,
            once: true
        });
    }
</script>
@endsection