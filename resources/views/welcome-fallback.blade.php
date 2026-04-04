{{-- Fallback mínimo se a home completa falhar (evita 500 para o visitante). --}}
@extends('layouts.app')

@section('public')
@endsection

@section('og_title', config('app.name'))
@section('og_description', __('Visita Aí apoia a vigilância entomológica e o controle de vetores no município, alinhado às diretrizes do Ministério da Saúde. Profissionais registram visitas; moradores consultam o histórico do imóvel com o código único.'))

@section('content')
<div class="flex min-h-[60vh] flex-col items-center justify-center px-6 py-16 text-center">
    <h1 class="text-2xl font-extrabold text-slate-900 dark:text-slate-100 sm:text-3xl">
        {{ __('Bem-vindo(a) ao') }} <span class="text-blue-600 dark:text-blue-400">{{ config('app.name', 'Visita Aí') }}</span>
    </h1>
    <p class="mt-4 max-w-lg text-sm leading-relaxed text-slate-600 dark:text-slate-400">
        {{ __('Plataforma municipal para registrar e acompanhar visitas de campo na luta contra dengue, chikungunya, Zika e outras arboviroses. Gestores e agentes trabalham no sistema; você, morador, pode verificar quando houve visita no seu endereço — com o código que o ACE ou ACS informou.') }}
    </p>
    <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:justify-center">
        <a href="{{ route('login') }}" class="v-btn-primary inline-flex items-center justify-center px-6 py-2.5 text-[13px] font-semibold">
            {{ __('Acessar o sistema') }}
        </a>
        <a href="{{ route('consulta.index') }}" class="v-btn-secondary inline-flex items-center justify-center px-6 py-2.5 text-[13px] font-semibold">
            {{ __('Verificar visitas no imóvel') }}
        </a>
    </div>
    <p class="mt-10 text-xs text-slate-500 dark:text-slate-400">
        &copy; {{ date('Y') }} Visita Aí · {{ __('Desenvolvido por') }}
        <a href="https://bitwise.dev.br" target="_blank" rel="noopener noreferrer" class="text-slate-600 underline hover:text-slate-800 dark:text-slate-400">Bitwise Technologies</a>
    </p>
</div>
@endsection
