@extends('layouts.app')

@section('og_title', config('app.name') . ' · ' . __('Auditoria'))
@section('og_description', __('Registros de auditoria. Histórico de ações realizadas pelos usuários no sistema.'))

@section('content')
<div class="v-page">
    <x-breadcrumbs :items="[['label' => __('Página Inicial'), 'url' => route('dashboard')], ['label' => __('Auditoria')]]" />
    <x-page-header :eyebrow="__('Governança')" :title="__('Auditoria')">
        <x-slot name="lead">
            <p>{{ __('Histórico de ações no sistema (usuário, entidade, IP e dispositivo quando disponível). Use a busca para filtrar.') }}</p>
        </x-slot>
    </x-page-header>

    <x-flash-alerts />

    <x-section-card class="v-card--muted">
        <label for="search" class="v-toolbar-label">{{ __('Busca inteligente') }}</label>
        <div class="mt-1 flex items-center gap-2">
            <input type="text" id="search" name="search" value="{{ old('search', request('search')) }}"
                   data-live-url="{{ route('gestor.logs.index') }}" data-live-param="search"
                   data-live-loading-id="search-loading-logs"
                   placeholder="{{ __('Usuário, ação, entidade, descrição ou IP…') }}"
                   class="v-input" />
            <span id="search-loading-logs" class="hidden shrink-0 text-xs text-slate-500 dark:text-slate-400" aria-live="polite">{{ __('Buscando…') }}</span>
        </div>
    </x-section-card>

    <x-section-card class="v-card--flush overflow-hidden">
        <div class="v-table-meta">
            <span>
                {{ __('Exibindo :atual de :total registro(s) de auditoria.', ['atual' => $logs->count(), 'total' => $logs->total()]) }}
                @if(request('search'))
                    <span class="text-slate-500 dark:text-slate-500">{{ __('Resultados para:') }} <strong class="text-slate-700 dark:text-slate-300">{{ request('search') }}</strong></span>
                @endif
            </span>
        </div>
        <div class="v-table-wrap">
            <table class="v-data-table">
                <thead>
                    <tr>
                        <th scope="col">{{ __('Data') }}</th>
                        <th scope="col">{{ __('Usuário') }}</th>
                        <th scope="col">{{ __('Ação') }}</th>
                        <th scope="col">{{ __('Entidade') }}</th>
                        <th scope="col">{{ __('Descrição') }}</th>
                        <th scope="col">{{ __('IP / Dispositivo') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td class="whitespace-nowrap tabular-nums text-slate-700 dark:text-slate-300">{{ \Carbon\Carbon::parse($log->log_data)->format('d/m/Y H:i') }}</td>
                            <td class="font-medium text-slate-900 dark:text-slate-100">{{ $log->usuario->use_nome ?? __('Desconhecido') }}</td>
                            <td><span class="inline-flex rounded-md bg-slate-100 px-2 py-0.5 text-xs font-semibold uppercase tracking-wide text-slate-700 dark:bg-slate-800 dark:text-slate-200">{{ ucfirst($log->log_acao) }}</span></td>
                            <td class="text-slate-700 dark:text-slate-300">{{ $log->log_entidade }}</td>
                            <td class="max-w-xs text-slate-600 dark:text-slate-400">{{ Str::limit($log->log_descricao, 120) }}</td>
                            <td class="text-sm">
                                @if($log->log_ip || $log->log_user_agent)
                                    <span class="tabular-nums text-slate-800 dark:text-slate-200" title="{{ $log->log_user_agent ?? '' }}">{{ $log->log_ip ?? __('N/D') }}</span>
                                    @if($log->log_user_agent)
                                        <span class="mt-0.5 block max-w-[14rem] truncate text-xs text-slate-500 dark:text-slate-400" title="{{ $log->log_user_agent }}">{{ Str::limit($log->log_user_agent, 48) }}</span>
                                    @endif
                                @else
                                    <span class="text-slate-400">{{ __('N/D') }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="!p-0">
                                <div class="v-empty-state px-4">
                                    <div class="v-empty-state__icon" aria-hidden="true">
                                        <x-heroicon-o-clipboard-document-list class="h-7 w-7 shrink-0" />
                                    </div>
                                    <p class="v-empty-state__title">{{ __('Nenhum registro de auditoria encontrado.') }}</p>
                                    <p class="v-empty-state__text">{{ __('Ajuste os termos de busca ou volte mais tarde.') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <x-pagination-relatorio :paginator="$logs" item-label="registros" />
    </x-section-card>
</div>
@endsection
