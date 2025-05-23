@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6 max-w-4xl space-y-8">

  <!-- Botão Voltar -->
  <div>
    <a href="{{ route('agente.locais.index') }}"
       class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-semibold text-sm rounded-lg shadow transition">
      <svg class="h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
      </svg>
      Voltar
    </a>
  </div>

  <!-- Detalhes do Local -->
  <section class="p-6 bg-white dark:bg-gray-700 rounded-lg shadow space-y-4">
    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Detalhes do Local</h2>

    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-sm text-gray-700 dark:text-gray-300">
      <div>
        <dt class="font-medium">Endereço</dt>
        <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ $local->loc_endereco }}</dd>
      </div>
      <div>
        <dt class="font-medium">Bairro</dt>
        <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ $local->loc_bairro }}</dd>
      </div>
      <div>
        <dt class="font-medium">Coordenadas</dt>
        <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ $local->loc_coordenadas }}</dd>
      </div>
      <div>
        <dt class="font-medium">Código Único</dt>
        <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ $local->loc_codigo_unico }}</dd>
      </div>
    </dl>
  </section>
</div>
@endsection
