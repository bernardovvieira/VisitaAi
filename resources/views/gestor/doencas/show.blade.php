<!-- resources/views/gestor/doencas/show.blade.php -->
@extends('layouts.app')

@section('og_title', config('app.name') . ' · ' . $doenca->doe_nome)
@section('og_description', __('Detalhes da doença monitorada: sintomas e registro municipal.'))

@section('content')
<div class="v-page v-page--wide v-page--loose">
  <x-breadcrumbs :items="[['label' => __('Página Inicial'), 'url' => route('dashboard')], ['label' => __('Doenças'), 'url' => route('gestor.doencas.index')], ['label' => __('Visualizar')]]" />

  <x-page-header :eyebrow="__('Cadastros municipais')" :title="$doenca->doe_nome">
    <x-slot name="lead">
      <p class="text-sm leading-relaxed text-slate-600 dark:text-slate-400">{{ __('Detalhes da doença monitorada, com sintomas, transmissão e medidas de controle organizadas em cartões.') }}</p>
    </x-slot>
  </x-page-header>

  <div class="grid gap-4 lg:grid-cols-2">
    <x-section-card class="space-y-4 rounded-2xl border border-slate-200/80 bg-white/95 p-5 shadow-sm dark:border-slate-700/70 dark:bg-slate-900/50">
      <div class="border-b border-slate-200/80 pb-3 dark:border-slate-700/70">
        <h2 class="text-lg font-semibold text-slate-800 dark:text-slate-100">{{ __('Registro') }}</h2>
      </div>
      <dl class="grid grid-cols-1 gap-x-6 gap-y-4 text-sm text-slate-700 dark:text-slate-300 sm:grid-cols-2">
        <div>
          <dt class="text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">{{ __('ID do registro') }}</dt>
          <dd class="mt-1 text-slate-900 dark:text-slate-100">#{{ $doenca->doe_id }}</dd>
        </div>
        <div>
          <dt class="text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">{{ __('Criado em') }}</dt>
          <dd class="mt-1 text-slate-900 dark:text-slate-100">{{ $doenca->created_at->format('d/m/Y H:i') }}</dd>
        </div>
        <div>
          <dt class="text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">{{ __('Atualizado em') }}</dt>
          <dd class="mt-1 text-slate-900 dark:text-slate-100">{{ $doenca->updated_at->format('d/m/Y H:i') }}</dd>
        </div>
      </dl>
    </x-section-card>

    <x-section-card class="space-y-4 rounded-2xl border border-slate-200/80 bg-white/95 p-5 shadow-sm dark:border-slate-700/70 dark:bg-slate-900/50">
      <div class="border-b border-slate-200/80 pb-3 dark:border-slate-700/70">
        <h2 class="text-lg font-semibold text-slate-800 dark:text-slate-100">{{ __('Sintomas') }}</h2>
      </div>
      @if(count($doenca->doe_sintomas))
        <div class="flex flex-wrap gap-2">
          @foreach($doenca->doe_sintomas as $s)
            <span class="inline-flex rounded-full border border-amber-200/90 bg-amber-50 px-2.5 py-1 text-xs font-medium text-amber-950 dark:border-amber-700/60 dark:bg-amber-950/35 dark:text-amber-100">{{ $s }}</span>
          @endforeach
        </div>
      @else
        <p class="text-sm italic text-slate-500 dark:text-slate-400">{{ __('Nenhum sintoma registrado.') }}</p>
      @endif
    </x-section-card>

    <x-section-card class="space-y-4 rounded-2xl border border-slate-200/80 bg-white/95 p-5 shadow-sm dark:border-slate-700/70 dark:bg-slate-900/50">
      <div class="border-b border-slate-200/80 pb-3 dark:border-slate-700/70">
        <h2 class="text-lg font-semibold text-slate-800 dark:text-slate-100">{{ __('Modos de transmissão') }}</h2>
      </div>
      @if(count($doenca->doe_transmissao))
        <div class="flex flex-wrap gap-2">
          @foreach($doenca->doe_transmissao as $t)
            <span class="inline-flex rounded-full border border-sky-200/90 bg-sky-50 px-2.5 py-1 text-xs font-medium text-sky-950 dark:border-sky-700/60 dark:bg-sky-950/35 dark:text-sky-100">{{ $t }}</span>
          @endforeach
        </div>
      @else
        <p class="text-sm italic text-slate-500 dark:text-slate-400">{{ __('Nenhum modo de transmissão registrado.') }}</p>
      @endif
    </x-section-card>

    <x-section-card class="space-y-4 rounded-2xl border border-slate-200/80 bg-white/95 p-5 shadow-sm dark:border-slate-700/70 dark:bg-slate-900/50 lg:col-span-2">
      <div class="border-b border-slate-200/80 pb-3 dark:border-slate-700/70">
        <h2 class="text-lg font-semibold text-slate-800 dark:text-slate-100">{{ __('Medidas de controle') }}</h2>
      </div>
      @if(count($doenca->doe_medidas_controle))
        <div class="flex flex-wrap gap-2">
          @foreach($doenca->doe_medidas_controle as $m)
            <span class="inline-flex rounded-full border border-violet-200/90 bg-violet-50 px-2.5 py-1 text-xs font-medium text-violet-950 dark:border-violet-700/60 dark:bg-violet-950/35 dark:text-violet-100">{{ $m }}</span>
          @endforeach
        </div>
      @else
        <p class="text-sm italic text-slate-500 dark:text-slate-400">{{ __('Nenhuma medida de controle registrada.') }}</p>
      @endif
    </x-section-card>
  </div>

</div>
@endsection