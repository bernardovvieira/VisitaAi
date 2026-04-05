@extends('layouts.app')

@section('og_title', config('app.name') . ' · ' . __('Visitas'))
@section('og_description', __('Visitas de vigilância entomológica e controle vetorial registradas. Visualize, busque, edite ou remova suas visitas.'))

@section('content')
<div class="v-page"
     x-data="{ online: true }"
     x-init="
       $nextTick(function() { online = typeof window.visitaConnectionOnline === 'boolean' ? window.visitaConnectionOnline : true; });
       window.addEventListener('visita-connection-change', function(e) { online = e.detail.online; });
       setInterval(function() { if (typeof window.visitaConnectionOnline === 'boolean') online = window.visitaConnectionOnline; }, 1500);
     ">
    <x-breadcrumbs :items="[['label' => __('Página Inicial'), 'url' => route('dashboard')], ['label' => __('Visitas')]]" />

    <x-page-header :eyebrow="__('Registro em campo')" :title="__('Visitas')">
        <x-slot name="lead">
            <p>{{ __('Veja as visitas que você registrou, busque, edite ou cadastre novas. Em campo sem internet, salve no dispositivo e envie depois pela sincronização.') }}</p>
        </x-slot>
    </x-page-header>

    <x-flash-alerts>
        <x-slot name="afterSuccess">
            @if(session('created_visita_id'))
                <div class="mt-2 flex flex-wrap gap-3">
                    <a href="{{ route('agente.visitas.show', session('created_visita_id')) }}" class="text-sm font-medium text-blue-600 hover:underline dark:text-blue-400">{{ __('Ver visita registrada') }}</a>
                </div>
            @endif
        </x-slot>
    </x-flash-alerts>

    <div id="visita-offline-pending-alert" class="hidden rounded-xl border border-amber-200 bg-amber-50 p-3 text-sm text-amber-900 dark:border-amber-800 dark:bg-amber-950/35 dark:text-amber-100" role="alert">
        <span id="visita-offline-pending-alert-msg"></span>
    </div>

        <x-section-card class="flex flex-col gap-4 sm:flex-row sm:flex-wrap sm:items-center sm:justify-between">
            <div class="min-w-0 flex-1">
                <h2 class="text-sm font-semibold text-slate-900 dark:text-slate-100">{{ __('Ações rápidas') }}</h2>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ __('Cadastrar visita ou enviar rascunhos guardados no aparelho.') }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('agente.visitas.create') }}"
                   class="v-btn-compact v-btn-compact--blue">
                    <x-heroicon-o-plus class="h-4 w-4 shrink-0" aria-hidden="true" />
                    {{ __('Cadastrar visita') }}
                </a>
                <span x-show="online" x-cloak>
                    <a href="{{ route('agente.visitas.sync') }}"
                       class="v-btn-compact v-btn-compact--amber">
                        <x-heroicon-o-arrow-path class="h-4 w-4 shrink-0" aria-hidden="true" />
                        {{ __('Enviar visitas do dispositivo') }}
                    </a>
                </span>
            </div>
        </x-section-card>

        @if($locaisComPendenciasNaoRevisitadas->isNotEmpty())
            <x-ui.callout variant="amber" :title="__('Pendências sem revisita')">
                <ul class="mt-3 space-y-2 text-sm text-amber-950 dark:text-amber-100">
                    @foreach ($locaisComPendenciasNaoRevisitadas as $local)
                        @php
                            $ultimaPendencia = $local->visitas()->where('vis_pendencias', true)->latest('vis_data')->first();
                        @endphp
                        <li class="flex flex-col gap-0.5 border-l-2 border-amber-400/80 pl-3 sm:flex-row sm:items-baseline sm:justify-between">
                            <span>{{ $local->loc_endereco }}, {{ $local->loc_numero ?? 'S/N' }}, {{ $local->loc_bairro }}, {{ $local->loc_cidade }}/{{ $local->loc_estado }}</span>
                            @if($ultimaPendencia)
                                <span class="text-xs font-medium text-amber-800 dark:text-amber-300">{{ __('Última pendência: :d', ['d' => \Carbon\Carbon::parse($ultimaPendencia->vis_data)->format('d/m/Y')]) }}</span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </x-ui.callout>
        @endif

        <x-section-card class="v-card--muted">
            <label for="search" class="v-toolbar-label">{{ __('Busca inteligente') }}</label>
            <div class="flex items-center gap-2">
                <input type="text" id="search" name="busca" value="{{ old('busca', request('busca')) }}"
                       data-live-url="{{ route('agente.visitas.index') }}" data-live-param="busca"
                       data-live-loading-id="search-loading"
                       placeholder="{{ __('Local, doença, atividade, pendentes ou data…') }}"
                       class="v-input" />
                <span id="search-loading" class="hidden shrink-0 text-xs text-slate-500 dark:text-slate-400" aria-live="polite">{{ __('Buscando…') }}</span>
            </div>
        </x-section-card>

        <x-section-card class="v-card--flush overflow-hidden">
        <div class="v-table-meta">
            <span>
                {{ __('Exibindo :atual de :total visita(s).', ['atual' => $visitas->count(), 'total' => $visitas->total()]) }}
                @if(request('busca'))
                    <span class="text-slate-500">{{ __('Filtro:') }} <strong class="text-slate-700 dark:text-slate-300">{{ request('busca') }}</strong></span>
                @endif
            </span>
        </div>

        <div class="v-table-wrap">
            <table class="v-data-table">
                <thead>
                    <tr>
                        <th scope="col" class="whitespace-nowrap">{{ __('Código') }}</th>
                        <th scope="col">{{ __('Data') }}</th>
                        <th scope="col">{{ __('Pendência') }}</th>
                        <th scope="col">{{ __('Atividade') }}</th>
                        <th scope="col" class="min-w-[12rem]">{{ __('Local') }}</th>
                        <th scope="col" class="align-bottom">
                            <span class="block leading-tight normal-case tracking-normal">{{ __('Profissional') }}</span>
                            <span class="mt-0.5 block text-[10px] font-normal normal-case tracking-normal text-slate-500 dark:text-slate-400">{{ __('ACE ou ACS') }}</span>
                        </th>
                        <th scope="col" class="w-28 text-center">{{ __('Ações') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($visitas as $visita)
                        <tr>
                            <td class="whitespace-nowrap">
                                <span class="inline-flex rounded-lg bg-slate-100 px-2 py-1 text-xs font-semibold tabular-nums text-slate-800 dark:bg-slate-800 dark:text-slate-200">#{{ $visita->vis_id }}</span>
                            </td>
                            <td class="leading-tight">
                                <div class="font-semibold">{{ \Carbon\Carbon::parse($visita->vis_data)->format('d/m/Y') }}</div>
                                <div class="text-xs text-slate-500 dark:text-slate-400">{{ \Carbon\Carbon::parse($visita->vis_data)->translatedFormat('l') }}</div>
                            </td>
                            <td>
                                @if($visita->vis_pendencias)
                                    <span class="inline-flex rounded-md bg-red-100 px-2 py-0.5 text-xs font-semibold text-red-900 dark:bg-red-950/60 dark:text-red-200">{{ __('Pendente') }}</span>
                                    @php
                                        $revisitaPosterior = $visita->local->visitas()
                                            ->where('vis_data', '>', $visita->vis_data)
                                            ->orderBy('vis_data')
                                            ->first();
                                    @endphp
                                    @if($revisitaPosterior)
                                        <div class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ __('Revisitado em :d', ['d' => \Carbon\Carbon::parse($revisitaPosterior->vis_data)->format('d/m/Y')]) }}</div>
                                    @endif
                                @else
                                    <span class="inline-flex rounded-md bg-emerald-100 px-2 py-0.5 text-xs font-semibold text-emerald-900 dark:bg-emerald-950/45 dark:text-emerald-200">{{ __('Concluída') }}</span>
                                @endif
                            </td>
                            <td class="text-slate-600 dark:text-slate-300" title="{{ \App\Helpers\MsTerminologia::atividadeNome($visita->vis_atividade) }}">
                                {{ \App\Helpers\MsTerminologia::atividadeLabel($visita->vis_atividade) ?: __('Não informado') }}
                            </td>
                            <td class="leading-snug">
                                <div class="font-semibold text-slate-900 dark:text-slate-100">{{ $visita->local->loc_endereco }}, {{ $visita->local->loc_numero }}</div>
                                <div class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">
                                    {{ $visita->local->loc_bairro }} · {{ __('Cód.') }} {{ $visita->local->loc_codigo_unico }}
                                    <br>{{ __('Resp.') }} {{ $visita->local->loc_responsavel_nome ?? __('Não informado') }}
                                </div>
                            </td>
                            <td>
                                <div class="font-semibold">{{ $visita->usuario->use_nome }}</div>
                                <div class="text-xs text-slate-500 dark:text-slate-400">{{ \App\Models\User::perfilLabel($visita->usuario->use_perfil) }}</div>
                            </td>
                            <td class="text-center">
                                <div x-show="online" class="flex justify-center gap-1">
                                    <a href="{{ route('agente.visitas.show', $visita) }}"
                                       class="v-btn-icon-primary v-btn-icon-primary--lg"
                                       title="{{ __('Visualizar') }}" aria-label="{{ __('Visualizar visita') }}">
                                        <x-heroicon-o-eye class="h-4 w-4 shrink-0" />
                                    </a>
                                    <a href="{{ route('agente.visitas.edit', $visita) }}"
                                       class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-600 shadow-sm transition hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700"
                                       title="{{ __('Editar') }}" aria-label="{{ __('Editar visita') }}">
                                        <x-heroicon-o-pencil-square class="h-4 w-4 shrink-0" />
                                    </a>
                                    <form method="POST" action="{{ route('agente.visitas.destroy', $visita) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('Tem certeza que deseja excluir esta visita? Esta ação não pode ser desfeita.')"
                                                class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-red-200 bg-red-50 text-red-600 shadow-sm transition hover:bg-red-100 dark:border-red-900/50 dark:bg-red-950/40 dark:text-red-300 dark:hover:bg-red-950/60"
                                                title="{{ __('Excluir') }}" aria-label="{{ __('Excluir visita') }}">
                                            <x-heroicon-o-trash class="h-4 w-4 shrink-0" />
                                        </button>
                                    </form>
                                </div>
                                <p x-show="!online" x-cloak class="text-xs text-slate-500 dark:text-slate-400">{{ __('Offline: edição e exclusão indisponíveis.') }}</p>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-10 text-center">
                                <div class="mx-auto flex max-w-md flex-col items-center">
                                    <div class="mb-3 flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 dark:bg-slate-800">
                                        <x-heroicon-o-clipboard-document-list class="h-7 w-7 shrink-0 text-slate-400" />
                                    </div>
                                    <p class="font-medium text-slate-700 dark:text-slate-200">{{ __('Nenhuma visita registrada.') }}</p>
                                    <p class="mt-1 text-sm text-slate-500">{{ __('Registre a primeira visita para começar.') }}</p>
                                    <a href="{{ route('agente.visitas.create') }}" class="v-btn-compact v-btn-compact--blue mt-5">
                                        <x-heroicon-o-plus class="h-4 w-4 shrink-0" aria-hidden="true" />
                                        {{ __('Registrar visita') }}
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-pagination-relatorio :paginator="$visitas->appends(request()->query())" item-label="visitas" />
        </x-section-card>
</div>
<script>
(function() {
    var alertEl = document.getElementById('visita-offline-pending-alert');
    var msgEl = document.getElementById('visita-offline-pending-alert-msg');
    if (!alertEl || !msgEl) return;
    function isOnline() {
        if (typeof window.visitaConnectionOnline === 'boolean') return window.visitaConnectionOnline;
        return navigator.onLine;
    }
    function updateOfflinePendingAlert() {
        if (isOnline()) {
            alertEl.classList.add('hidden');
            return;
        }
        var perfil = window.VisitaOfflineProfile || 'agente';
        if (typeof window.VisitaOfflineGetPendingCount !== 'function') return;
        window.VisitaOfflineGetPendingCount(perfil).then(function(count) {
            if (count > 0) {
                msgEl.textContent = 'Você tem ' + count + ' visita(s) guardada(s) no dispositivo. Sincronize quando se reconectar.';
                alertEl.classList.remove('hidden');
            } else {
                alertEl.classList.add('hidden');
            }
        }).catch(function() { alertEl.classList.add('hidden'); });
    }
    window.addEventListener('visita-connection-change', function(e) { if (!e.detail.online) updateOfflinePendingAlert(); else alertEl.classList.add('hidden'); });
    document.addEventListener('visita-connection-change', function(e) { if (!e.detail.online) updateOfflinePendingAlert(); else alertEl.classList.add('hidden'); });
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', function() { setTimeout(updateOfflinePendingAlert, 300); });
    else setTimeout(updateOfflinePendingAlert, 300);
})();
</script>
@endsection
