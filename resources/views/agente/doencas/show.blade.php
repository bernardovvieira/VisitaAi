<!-- resources/views/gestor/doencas/show.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6 max-w-4xl space-y-8">

  <!-- Botão Voltar -->
  <div>
    <a href="{{ route('agente.doencas.index') }}"
       class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-semibold text-sm rounded-lg shadow transition">
      <svg class="h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
      </svg>
      Voltar
    </a>
  </div>

  <!-- Introdução -->
  <section class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow">
    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Visão Geral da Doença</h2>
    <p class="mt-2 text-gray-600 dark:text-gray-400">
      Esta seção apresenta os detalhes completos de uma doença monitorada no sistema: nome, sintomas, formas de transmissão e medidas de controle.
    </p>
  </section>

  <!-- Dados Básicos -->
  <section class="bg-white dark:bg-gray-700 rounded-lg shadow p-6">
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
  <section class="bg-white dark:bg-gray-700 rounded-lg shadow p-6">
    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-2">Sintomas</h3>
    @if(count($doenca->doe_sintomas))
      <div class="flex flex-wrap gap-2">
        @foreach($doenca->doe_sintomas as $s)
          <span class="inline-block px-3 py-1 bg-green-100 dark:bg-green-800 text-green-800 dark:text-green-200 rounded-full text-xs">{{ $s }}</span>
        @endforeach
      </div>
    @else
      <p class="italic text-gray-500 dark:text-gray-400 text-sm">Nenhum sintoma registrado.</p>
    @endif
  </section>

  <!-- Transmissão -->
  <section class="bg-white dark:bg-gray-700 rounded-lg shadow p-6">
    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-2">Modos de Transmissão</h3>
    @if(count($doenca->doe_transmissao))
      <div class="flex flex-wrap gap-2">
        @foreach($doenca->doe_transmissao as $t)
          <span class="inline-block px-3 py-1 bg-blue-100 dark:bg-blue-800 text-blue-800 dark:text-blue-200 rounded-full text-xs">{{ $t }}</span>
        @endforeach
      </div>
    @else
      <p class="italic text-gray-500 dark:text-gray-400 text-sm">Nenhum modo de transmissão registrado.</p>
    @endif
  </section>

  <!-- Medidas de Controle -->
  <section class="bg-white dark:bg-gray-700 rounded-lg shadow p-6">
    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-2">Medidas de Controle</h3>
    @if(count($doenca->doe_medidas_controle))
      <div class="flex flex-wrap gap-2">
        @foreach($doenca->doe_medidas_controle as $m)
          <span class="inline-block px-3 py-1 bg-purple-100 dark:bg-purple-800 text-purple-800 dark:text-purple-200 rounded-full text-xs">{{ $m }}</span>
        @endforeach
      </div>
    @else
      <p class="italic text-gray-500 dark:text-gray-400 text-sm">Nenhuma medida de controle registrada.</p>
    @endif
  </section>

</div>
@endsection