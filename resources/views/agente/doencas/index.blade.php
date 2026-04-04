<!-- resources/views/agente/doencas/index.blade.php -->
@extends('layouts.app')

@section('og_title', config('app.name') . ' · ' . __('Doenças'))
@section('og_description', __('Doenças monitoradas. Consulte as doenças que você pode registrar nas visitas de vigilância entomológica e controle vetorial.'))

@section('content')
<div class="v-page">
    <x-breadcrumbs :items="[['label' => __('Página Inicial'), 'url' => route('dashboard')], ['label' => __('Doenças')]]" />
    <x-page-header :eyebrow="__('Referência epidemiológica')" :title="__('Doenças')" />

  <section class="v-card">
    <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Doenças monitoradas</h2>
    <p class="mt-2 text-gray-600 dark:text-gray-400">
      Consulte as doenças que você pode registrar nas visitas. Clique em Ver detalhes para mais informações.
    </p> 
  </section>

  <section class="v-card">
    <div class="flex flex-col sm:flex-row sm:items-end gap-4">
      <div class="flex-1">
        <label for="search" class="v-toolbar-label mb-1">Busca inteligente</label>
        <input type="text" id="search" name="search" value="{{ old('search', request('search')) }}"
               data-live-url="{{ route('agente.doencas.index') }}" data-live-param="search"
               placeholder="Nome, sintomas, transmissão ou medidas..."
               class="v-input">
      </div>
    </div>
  </section>

  <!-- bring the summary inside the same styled section -->
  <section class="v-card">
    <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
      Exibindo {{ $doencas->count() }} de {{ $doencas->total() }} doença(s) monitorada(s).
      @if(request('search'))
        <span class="text-gray-500">Resultados para: <strong>{{ request('search') }}</strong></span>
      @endif
    </p>

    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
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
            <tr class="v-table-row-interactive">
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
                <a href="{{ route('agente.doencas.show', $doenca) }}"
                    class="v-btn-icon-primary"
                    title="{{ __('Visualizar') }}"
                    aria-label="{{ __('Visualizar doença') }}">
                    <x-heroicon-o-eye class="h-4 w-4 shrink-0" />
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
    <x-pagination-relatorio :paginator="$doencas" item-label="doenças" />
  </section>
</div>
@endsection