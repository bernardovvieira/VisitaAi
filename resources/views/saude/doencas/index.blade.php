<!-- resources/views/agente/doencas/index.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6 space-y-6">

  <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Doenças Monitoradas</h1>

  <section class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow">
    <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Informações</h2>
    <p class="mt-2 text-gray-600 dark:text-gray-400">
      Abaixo estão as doenças que você está autorizado a visualizar. Para detalhes, clique em “Ver detalhes”.
    </p> 
  </section>

  <section x-data="{ search: '{{ request('search') }}' }" class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow">
    <div class="flex flex-col sm:flex-row sm:items-center gap-4">
      <div class="flex-1">
        <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Buscar Doença</label>
        <input type="text" id="search" name="search" x-model="search" x-init="$el.focus()"
               @input.debounce.500ms="window.location.href = '{{ route('saude.doencas.index') }}' + '?search=' + encodeURIComponent(search)"
               placeholder="Filtrar por nome, sintomas, transmissão ou medidas..."
               class="w-full rounded-md bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm px-4 py-2">
      </div>
    </div>
  </section>

  <!-- bring the summary inside the same styled section -->
  <section class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow">
    <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
      Exibindo {{ $doencas->count() }} de {{ $doencas->total() }} doença(s) monitorada(s).
      @if(request('search'))
        <span class="text-gray-500">Resultados para: <strong>{{ request('search') }}</strong></span>
      @endif
    </p>

    <div class="overflow-x-auto">
      <table class="min-w-full bg-white dark:bg-gray-800 rounded-lg shadow">
          <thead class="bg-gray-100 dark:bg-gray-700">
          <tr>
            <th class="p-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">ID</th>
            <th class="p-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Nome</th>
            <th class="p-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Sintomas</th>
            <th class="p-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Transmissão</th>
            <th class="p-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Medidas</th>
            <th class="p-4 text-center text-sm font-semibold text-gray-700 dark:text-gray-300">Ações</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
          @forelse($doencas as $doenca)
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-600">
              <td class="p-4 text-gray-800 dark:text-gray-100">
                  <span class="inline-block bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-gray-200 text-xs font-semibold px-2 py-1 rounded">
                      #{{ $doenca->doe_id }}
                  </span>
              </td>
              <td class="p-4 text-gray-800 dark:text-gray-100">{{ $doenca->doe_nome }}</td>
              <td class="p-4 text-gray-800 dark:text-gray-100">{{ Str::limit(implode(', ', $doenca->doe_sintomas), 30) }}</td>
              <td class="p-4 text-gray-800 dark:text-gray-100">{{ Str::limit(implode(', ', $doenca->doe_transmissao), 30) }}</td>
              <td class="p-4 text-gray-800 dark:text-gray-100">{{ Str::limit(implode(', ', $doenca->doe_medidas_controle), 30) }}</td>
              <td class="p-4 text-center">
                @can('view', $doenca)
                <a href="{{ route('saude.doencas.show', $doenca) }}"
                    class="inline-flex items-center gap-2 px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow transition">
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    Visualizar
                </a>
                @else
                  <span class="text-gray-500 text-sm italic">Sem acesso</span>
                @endcan
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="p-6 text-center text-gray-600 dark:text-gray-400">
                Nenhuma doença monitorada disponível.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </section>

  <div class="mt-4">
    {{ $doencas->links() }}
  </div>
</div>
@endsection