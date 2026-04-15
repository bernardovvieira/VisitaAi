@extends('layouts.app')

@section('og_title', config('app.brand') . ' · ' . __('Locais'))
@section('og_description', __('Locais cadastrados para a operação de campo. Visualize, crie e edite registros territoriais.'))

@section('content')
@php($locaisRouteProfile = $locaisRouteProfile ?? auth()->user()->locaisRouteProfile())
<div class="v-page v-page--wide v-page--dense">
    <x-breadcrumbs :items="[['label' => __('Página Inicial'), 'url' => route('dashboard')], ['label' => __('Locais')]]" />

    <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
        <x-page-header class="min-w-0 flex-1" :eyebrow="__('Cadastro territorial')" :title="__('Locais')">
            <x-slot name="lead">
                <p>{{ __('Locais usados na operação de campo. Consulte endereço, código único e coordenadas.') }}</p>
            </x-slot>
        </x-page-header>

        <div class="flex shrink-0 justify-end lg:pt-1">
            <a href="{{ route($locaisRouteProfile.'.locais.create') }}"
               class="v-btn-compact v-btn-compact--blue">
                <x-heroicon-o-plus class="h-4 w-4 shrink-0" aria-hidden="true" />
                {{ __('Novo local') }}
            </a>
        </div>
    </div>

    <x-flash-alerts>
        <x-slot name="afterSuccess">
            @if(session('created_local_id'))
                <div class="mt-2 flex flex-wrap gap-3">
                    <a href="{{ route($locaisRouteProfile.'.locais.show', session('created_local_id')) }}" class="text-sm font-medium text-blue-600 hover:underline dark:text-blue-400">{{ __('Ver local cadastrado') }}</a>
                </div>
            @endif
        </x-slot>
    </x-flash-alerts>

    <div id="locais-offline-pending-alert" class="hidden rounded-lg border border-amber-200/60 bg-amber-100 px-3 py-2 text-amber-800 dark:border-amber-900/40 dark:bg-amber-900/30 dark:text-amber-200" role="alert">
        <span id="locais-offline-pending-alert-msg"></span>
    </div>

    @if(!empty($coordenadasDuplicadas))
        <x-ui.callout variant="amber" class="v-alert-erp mb-4 border-amber-200/60 dark:border-amber-900/40" role="alert">
            <p class="text-sm font-medium text-amber-950 dark:text-amber-100">{{ __('Coordenadas duplicadas') }}</p>
            <p class="mt-1 text-sm text-amber-900/90 dark:text-amber-200/85">{{ __('Existem imóveis com a mesma coordenada (latitude e longitude) cadastrada. Revise os locais para evitar duplicidade.') }}</p>
        </x-ui.callout>
    @endif

    <x-section-card class="v-card--flush overflow-hidden">
        <div class="v-list-toolbar">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <x-form-field name="search" :label="__('Busca inteligente')" class="min-w-0 flex-1">
                    <div class="flex items-center gap-2">
                        <input type="text" id="search" name="search" value="{{ old('search', request('search')) }}"
                               data-live-url="{{ route($locaisRouteProfile.'.locais.index') }}" data-live-param="search"
                               data-live-loading-id="search-loading-locais"
                               placeholder="{{ __('Endereço, bairro, código, tipo (residencial, comercial, terreno) ou zona (urbano, rural)…') }}"
                               class="v-input" />
                        <span id="search-loading-locais" class="hidden shrink-0 text-xs text-slate-500 dark:text-slate-400" aria-live="polite">{{ __('Buscando…') }}</span>
                    </div>
                </x-form-field>
            </div>
        </div>
        <div class="v-table-meta">
            <span>
                {{ __('Exibindo :atual de :total :item cadastrados.', ['atual' => $locais->count(), 'total' => $locais->total(), 'item' => $locais->total() === 1 ? __('local') : __('locais')]) }}
                @if(request('search'))
                    <span class="text-slate-500 dark:text-slate-500">{{ __('Resultados para:') }} <strong class="text-slate-700 dark:text-slate-300">{{ request('search') }}</strong></span>
                @endif
            </span>
        </div>

        <div class="v-table-wrap">
            <table class="v-data-table">
                <thead>
                    <tr>
                        <th scope="col" class="whitespace-nowrap">{{ __('Código') }}</th>
                        <th scope="col">{{ __('Zona') }}</th>
                        <th scope="col">{{ __('Imóvel') }}</th>
                        <th scope="col" class="min-w-[8rem]">{{ __('Endereço') }}</th>
                        <th scope="col">{{ __('Bairro') }}</th>
                        <th scope="col">{{ __('Cidade') }}</th>
                        <th scope="col">{{ __('Responsável') }}</th>
                        <th scope="col" class="whitespace-nowrap">{{ __('Coordenadas') }}</th>
                        <th scope="col" class="text-center">{{ __('Ações') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($locais as $local)
                        <tr>
                            <td class="whitespace-nowrap">
                                <span class="inline-flex rounded-lg bg-slate-100 px-2 py-1 text-xs font-semibold tabular-nums text-slate-800 dark:bg-slate-800 dark:text-slate-200">
                                    #{{ $local->loc_codigo_unico }}
                                </span>
                                @if($local->isPrimary())
                                    <span class="mt-1 block text-xs text-slate-500 dark:text-slate-400" title="{{ __('Local primário do município') }}">{{ __('Primário') }}</span>
                                @endif
                            </td>
                            <td>
                                @if ($local->loc_zona == 'U')
                                    <span class="inline-block rounded bg-violet-100 px-2 py-1 text-xs font-semibold text-violet-900 dark:bg-violet-900/60 dark:text-violet-200">
                                        {{ __('Urbana') }}
                                    </span>
                                @elseif ($local->loc_zona == 'R')
                                    <span class="inline-block rounded bg-amber-100 px-2 py-1 text-xs font-semibold text-amber-900 dark:bg-amber-900/70 dark:text-amber-200">
                                        {{ __('Rural') }}
                                    </span>
                                @endif
                            </td>
                            <td class="leading-tight text-slate-700 dark:text-slate-300">
                                @if ($local->loc_tipo == 'R')
                                    <div class="text-xs text-slate-600 dark:text-slate-400" title="{{ __('Residencial') }}">
                                        {{ __('Residencial') }}
                                    </div>
                                @elseif ($local->loc_tipo == 'C')
                                    <div class="text-xs text-slate-600 dark:text-slate-400" title="{{ __('Comercial') }}">
                                        {{ __('Comercial') }}
                                    </div>
                                @elseif ($local->loc_tipo == 'T')
                                    <div class="text-xs text-slate-600 dark:text-slate-400" title="{{ __('Terreno baldio') }}">
                                        {{ __('Terreno baldio') }}
                                    </div>
                                @endif
                            </td>
                            <td class="text-slate-800 dark:text-slate-100">{{ $local->loc_endereco }}, @if($local->loc_numero){{ $local->loc_numero }}@else<span class="text-slate-500">{{ __('N/D') }}</span>@endif</td>
                            <td class="text-slate-800 dark:text-slate-100">{{ $local->loc_bairro }}</td>
                            <td class="text-slate-800 dark:text-slate-100">{{ $local->loc_cidade }}</td>
                            <td class="text-slate-800 dark:text-slate-100">{{ $local->loc_responsavel_nome ?? __('Não informado') }}</td>
                            <td class="tabular-nums text-xs text-slate-600 dark:text-slate-400">{{ $local->loc_latitude }}, {{ $local->loc_longitude }}</td>
                            <td class="text-center">
                                <div class="flex flex-wrap items-center justify-center gap-1.5">
                                    <a href="{{ route($locaisRouteProfile.'.locais.show', $local) }}"
                                       class="v-btn-icon-primary"
                                       title="{{ __('Visualizar') }}"
                                       aria-label="{{ __('Visualizar local') }}">
                                       <x-heroicon-o-eye class="h-4 w-4 shrink-0" />
                                    </a>
                                    @if(!$local->isPrimary())
                                    <a href="{{ route($locaisRouteProfile.'.locais.edit', $local) }}"
                                        class="v-btn-icon-slate"
                                        title="{{ __('Editar') }}"
                                        aria-label="{{ __('Editar local') }}">
                                        <x-heroicon-o-pencil-square class="h-4 w-4 shrink-0" />
                                    </a>
                                    <form method="POST" action="{{ route($locaisRouteProfile.'.locais.destroy', $local) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" data-confirm-message="{{ __('Tem certeza que deseja excluir este local? Esta ação não pode ser desfeita.') }}"
                                                class="v-btn-icon-danger"
                                                title="{{ __('Excluir') }}"
                                                aria-label="{{ __('Excluir local') }}">
                                                <x-heroicon-o-trash class="h-4 w-4 shrink-0" />
                                            </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="!p-0">
                                <x-empty-state
                                    :title="__('Nenhum local cadastrado.')"
                                    :description="__('Crie o primeiro local para iniciar os registros de campo.')"
                                    icon="heroicon-o-map-pin"
                                    class="border-0 bg-transparent px-4"
                                >
                                    <a href="{{ route($locaisRouteProfile.'.locais.create') }}" class="v-btn-compact v-btn-compact--blue mt-4">
                                        <x-heroicon-o-plus class="h-4 w-4 shrink-0" aria-hidden="true" />
                                        {{ __('Novo local') }}
                                    </a>
                                </x-empty-state>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <x-pagination-relatorio :paginator="$locais" item-label="locais" />
    </x-section-card>

    <x-section-card>
        <x-ui.disclosure variant="footer-lgpd">
            <x-slot name="summary">
                <span>{{ __('O que significa um imóvel marcado como "Primário"?') }}</span>
            </x-slot>
            <p class="text-xs leading-relaxed">{!! __('Um imóvel marcado como <strong>primário</strong> é o endereço de referência do município no sistema (cidade/estado). Esse registro é definido na configuração inicial e, por segurança, não pode ser editado nem excluído pela interface. Os demais imóveis são os locais de campo cadastrados e visitados pelos profissionais (ACE/ACS).') !!}</p>
        </x-ui.disclosure>
    </x-section-card>
</div>
<script>
(function() {
    document.querySelectorAll('[data-confirm-message]').forEach(function (element) {
        element.addEventListener('click', function (event) {
            var message = element.dataset.confirmMessage || '';
            if (!message || confirm(message)) {
                return;
            }
            event.preventDefault();
        });
    });

    var alertEl = document.getElementById('locais-offline-pending-alert');
    var msgEl = document.getElementById('locais-offline-pending-alert-msg');
    if (!alertEl || !msgEl) return;
    function isOnline() {
        if (typeof window.visitaConnectionOnline === 'boolean') return window.visitaConnectionOnline;
        return navigator.onLine;
    }
    function update() {
        if (isOnline()) { alertEl.classList.add('hidden'); return; }
        if (typeof window.VisitaOfflineGetLocalPendingCount !== 'function') return;
        window.VisitaOfflineGetLocalPendingCount(@json($locaisRouteProfile === 'saude' ? 'saude' : 'agente')).then(function(count) {
            if (count > 0) {
                msgEl.textContent = 'Você tem ' + count + (count === 1 ? ' local guardado' : ' locais guardados') + ' no dispositivo. Sincronize quando se reconectar.';
                alertEl.classList.remove('hidden');
            } else { alertEl.classList.add('hidden'); }
        }).catch(function() { alertEl.classList.add('hidden'); });
    }
    window.addEventListener('visita-connection-change', function(e) { if (!e.detail.online) update(); else alertEl.classList.add('hidden'); });
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', function() { setTimeout(update, 300); });
    else setTimeout(update, 300);
})();
</script>
@endsection