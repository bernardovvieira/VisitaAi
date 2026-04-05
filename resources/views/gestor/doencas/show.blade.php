<!-- resources/views/gestor/doencas/show.blade.php -->
@extends('layouts.app')

@section('content')
<div class="v-page">
  <x-breadcrumbs :items="[['label' => __('Página Inicial'), 'url' => route('dashboard')], ['label' => __('Doenças'), 'url' => route('gestor.doencas.index')], ['label' => __('Visualizar')]]" />

  <x-page-header :eyebrow="__('Doenças')" :title="$doenca->doe_nome" />

  <x-section-card :title="__('Registro')">
    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-sm text-gray-700 dark:text-gray-300">
      <div>
        <dt class="font-medium">{{ __('ID do registro') }}</dt>
        <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ $doenca->doe_id }}</dd>
      </div>
      <div>
        <dt class="font-medium">{{ __('Criado em') }}</dt>
        <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ $doenca->created_at->format('d/m/Y H:i') }}</dd>
      </div>
      <div>
        <dt class="font-medium">{{ __('Atualizado em') }}</dt>
        <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ $doenca->updated_at->format('d/m/Y H:i') }}</dd>
      </div>
    </dl>
  </x-section-card>

  <x-section-card :title="__('Sintomas')">
    @if(count($doenca->doe_sintomas))
      <div class="flex flex-wrap gap-2">
        @foreach($doenca->doe_sintomas as $s)
          <span class="inline-block rounded bg-slate-200 px-2 py-0.5 text-xs font-medium text-slate-900 dark:bg-slate-600 dark:text-slate-100">{{ $s }}</span>
        @endforeach
      </div>
    @else
      <p class="italic text-gray-500 dark:text-gray-400 text-sm">{{ __('Nenhum sintoma registrado.') }}</p>
    @endif
  </x-section-card>

  <x-section-card :title="__('Modos de transmissão')">
    @if(count($doenca->doe_transmissao))
      <div class="flex flex-wrap gap-2">
        @foreach($doenca->doe_transmissao as $t)
          <span class="inline-block bg-yellow-200 dark:bg-yellow-700 text-yellow-900 dark:text-yellow-100 text-xs font-medium px-2 py-0.5 rounded">{{ $t }}</span>
        @endforeach
      </div>
    @else
      <p class="italic text-gray-500 dark:text-gray-400 text-sm">{{ __('Nenhum modo de transmissão registrado.') }}</p>
    @endif
  </x-section-card>

  <x-section-card :title="__('Medidas de controle')">
    @if(count($doenca->doe_medidas_controle))
      <div class="flex flex-wrap gap-2">
        @foreach($doenca->doe_medidas_controle as $m)
          <span class="inline-block rounded bg-slate-200 px-2 py-0.5 text-xs font-medium text-slate-900 dark:bg-slate-600 dark:text-slate-100">{{ $m }}</span>
        @endforeach
      </div>
    @else
      <p class="italic text-gray-500 dark:text-gray-400 text-sm">{{ __('Nenhuma medida de controle registrada.') }}</p>
    @endif
  </x-section-card>

</div>
@endsection