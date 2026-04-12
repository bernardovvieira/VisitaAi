<!-- resources/views/saude/doencas/index.blade.php -->
@extends('layouts.app')

@section('og_title', config('app.name') . ' · ' . __('Doenças'))
@section('og_description', __('Doenças monitoradas. Consulte as doenças que você pode registrar nas visitas LIRAa.'))

@section('content')
<div class="v-page">
  <x-breadcrumbs :items="[['label' => __('Página Inicial'), 'url' => route('saude.dashboard')], ['label' => __('Doenças')]]" />
  <x-page-header :eyebrow="__('Referência epidemiológica')" :title="__('Doenças')" />

  <p class="text-sm text-gray-600 dark:text-gray-400 border-b border-gray-200 pb-4 dark:border-gray-700">
    Consulte as doenças que você pode registrar nas visitas. Clique em Ver detalhes para mais informações.
  </p>

  <x-section-card>
    <div class="flex flex-col sm:flex-row sm:items-end gap-4">
      <div class="flex-1">
        <label for="search" class="v-toolbar-label mb-1">Busca inteligente</label>
        <input type="text" id="search" name="search" value="{{ old('search', request('search')) }}"
               data-live-url="{{ route('saude.doencas.index') }}" data-live-param="search"
               placeholder="Nome, sintomas, transmissão ou medidas..."
               class="v-input">
      </div>
    </div>
  </x-section-card>

  <!-- bring the summary inside the same styled section -->
  <x-section-card>
    <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
      Exibindo {{ $doencas->count() }} de {{ $doencas->total() }} {{ $doencas->total() === 1 ? 'doença monitorada' : 'doenças monitoradas' }}.
      @if(request('search'))
        <span class="text-gray-500">Resultados para: <strong>{{ request('search') }}</strong></span>
      @endif
    </p>

    <div class="v-table-wrap">
      <table class="v-data-table">
          <thead>
          <tr>
            <th scope="col" class="whitespace-nowrap">ID</th>
            <th scope="col">Nome</th>
            <th scope="col">Sintomas</th>
            <th scope="col">Transmissão</th>
            <th scope="col">Medidas</th>
            <th scope="col" class="text-center">Ações</th>
          </tr>
        </thead>
        <tbody>
          @forelse($doencas as $doenca)
            <tr>
              <td class="whitespace-nowrap">
                  <span class="inline-flex rounded-lg bg-slate-100 px-2 py-1 text-xs font-semibold tabular-nums text-slate-800 dark:bg-slate-800 dark:text-slate-200">
                      #{{ $doenca->doe_id }}
                  </span>
              </td>
              <td class="font-medium text-slate-900 dark:text-slate-100">{{ $doenca->doe_nome }}</td>
              <td class="text-slate-700 dark:text-slate-300">{{ Str::limit(implode(', ', $doenca->doe_sintomas), 30) }}</td>
              <td class="text-slate-700 dark:text-slate-300">{{ Str::limit(implode(', ', $doenca->doe_transmissao), 30) }}</td>
              <td class="text-slate-700 dark:text-slate-300">{{ Str::limit(implode(', ', $doenca->doe_medidas_controle), 30) }}</td>
              <td class="text-center">
                @can('view', $doenca)
                <a href="{{ route('saude.doencas.show', $doenca) }}"
                    class="v-btn-icon-primary"
                    title="{{ __('Visualizar') }}"
                    aria-label="{{ __('Visualizar doença') }}">
                    <x-heroicon-o-eye class="h-4 w-4 shrink-0" />
                </a>
                @else
                  <span class="text-xs italic text-slate-500 dark:text-slate-400">Sem acesso</span>
                @endcan
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="!p-0">
                <div class="px-4 py-8 text-center text-sm text-slate-600 dark:text-slate-400">Nenhuma doença monitorada disponível.</div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <x-pagination-relatorio :paginator="$doencas" item-label="doenças" />
  </x-section-card>
</div>
@endsection