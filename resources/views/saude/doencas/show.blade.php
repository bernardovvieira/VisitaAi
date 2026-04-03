<!-- resources/views/gestor/doencas/show.blade.php -->
@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto space-y-8">
  <x-breadcrumbs :items="[['label' => 'Página Inicial', 'url' => route('saude.dashboard')], ['label' => 'Doenças', 'url' => route('saude.doencas.index')], ['label' => 'Visualizar']]" />

  <!-- Introdução -->
  <section class="rounded-xl border border-gray-200/80 bg-white p-5 shadow-sm dark:border-gray-600 dark:bg-gray-800">
    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Visão Geral da Doença</h2>
    <p class="mt-2 text-gray-600 dark:text-gray-400">
      Esta seção apresenta os detalhes completos de uma doença monitorada no sistema: nome, sintomas, formas de transmissão e medidas de controle.
    </p>
  </section>

  <!-- Dados Básicos -->
  <section class="rounded-xl border border-gray-200/80 bg-white p-6 shadow-sm dark:border-gray-600 dark:bg-gray-800">
    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Dados Básicos</h3>
    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-sm text-gray-700 dark:text-gray-300">
      <div>
        <dt class="font-medium">ID do Registro</dt>
        <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ $doenca->doe_id }}</dd>
      </div>
      <div>
        <dt class="font-medium">Nome da Doença</dt>
        <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ $doenca->doe_nome }}</dd>
      </div>
      <div>
        <dt class="font-medium">Criado em</dt>
        <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ $doenca->created_at->format('d/m/Y H:i') }}</dd>
      </div>
      <div>
        <dt class="font-medium">Atualizado em</dt>
        <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ $doenca->updated_at->format('d/m/Y H:i') }}</dd>
      </div>
    </dl>
  </section>

  <!-- Sintomas -->
  <section class="rounded-xl border border-gray-200/80 bg-white p-6 shadow-sm dark:border-gray-600 dark:bg-gray-800">
    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-2">Sintomas</h3>
    @if(count($doenca->doe_sintomas))
      <div class="flex flex-wrap gap-2">
        @foreach($doenca->doe_sintomas as $s)
          <span class="inline-block rounded bg-slate-200 px-2 py-0.5 text-xs font-medium text-slate-900 dark:bg-slate-600 dark:text-slate-100">{{ $s }}</span>
        @endforeach
      </div>
    @else
      <p class="italic text-gray-500 dark:text-gray-400 text-sm">Nenhum sintoma registrado.</p>
    @endif
  </section>

  <!-- Transmissão -->
  <section class="rounded-xl border border-gray-200/80 bg-white p-6 shadow-sm dark:border-gray-600 dark:bg-gray-800">
    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-2">Modos de Transmissão</h3>
    @if(count($doenca->doe_transmissao))
      <div class="flex flex-wrap gap-2">
        @foreach($doenca->doe_transmissao as $t)
          <span class="inline-block bg-yellow-200 dark:bg-yellow-700 text-yellow-900 dark:text-yellow-100 text-xs font-medium px-2 py-0.5 rounded">{{ $t }}</span>
        @endforeach
      </div>
    @else
      <p class="italic text-gray-500 dark:text-gray-400 text-sm">Nenhum modo de transmissão registrado.</p>
    @endif
  </section>

  <!-- Medidas de Controle -->
  <section class="rounded-xl border border-gray-200/80 bg-white p-6 shadow-sm dark:border-gray-600 dark:bg-gray-800">
    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-2">Medidas de Controle</h3>
    @if(count($doenca->doe_medidas_controle))
      <div class="flex flex-wrap gap-2">
        @foreach($doenca->doe_medidas_controle as $m)
          <span class="inline-block rounded bg-slate-200 px-2 py-0.5 text-xs font-medium text-slate-900 dark:bg-slate-600 dark:text-slate-100">{{ $m }}</span>
        @endforeach
      </div>
    @else
      <p class="italic text-gray-500 dark:text-gray-400 text-sm">Nenhuma medida de controle registrada.</p>
    @endif
  </section>

</div>
@endsection