@extends('layouts.app')

@section('og_title', config('app.name') . ' — Visitas')
@section('og_description', 'Visitas de vigilância entomológica e controle vetorial registradas. Visualize, busque, edite ou remova suas visitas.')

@section('content')
<div class="max-w-7xl mx-auto space-y-6"
     x-data="{ online: true }"
     x-init="
       $nextTick(function() { online = typeof window.visitaConnectionOnline === 'boolean' ? window.visitaConnectionOnline : true; });
       window.addEventListener('visita-connection-change', function(e) { online = e.detail.online; });
       setInterval(function() { if (typeof window.visitaConnectionOnline === 'boolean') online = window.visitaConnectionOnline; }, 1500);
     ">
    <x-breadcrumbs :items="[['label' => 'Página Inicial', 'url' => route('dashboard')], ['label' => 'Visitas']]" />
    <h1 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-gray-100">{{ __('Visitas') }}</h1>

    @if(session('success'))
        <x-alert type="success" :message="session('success')" />
        @if(session('created_visita_id'))
            <div class="flex flex-wrap gap-3 mt-2">
                <a href="{{ route('agente.visitas.show', session('created_visita_id')) }}" class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:underline">Ver visita registrada</a>
            </div>
        @endif
    @endif
    @if(session('warning'))
        <x-alert type="warning" :message="session('warning')" :autodismiss="false" />
    @endif
    @if(session('error'))
        <x-alert type="error" :message="session('error')" />
    @endif

    <div id="visita-offline-pending-alert" class="hidden p-3 mb-4 rounded bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-200 border border-amber-200 dark:border-amber-800" role="alert">
        <span id="visita-offline-pending-alert-msg"></span>
    </div>

    <section class="rounded-xl border border-gray-200/80 bg-white p-5 shadow-sm dark:border-gray-600 dark:bg-gray-800">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Visitas</h2>
        <p class="mt-2 text-gray-600 dark:text-gray-400">
            Veja as visitas que você já registrou, busque, edite ou cadastre novas.
        </p>
        <p class="mt-3 text-sm text-gray-600 dark:text-gray-400">
            <strong>Lançamento offline:</strong> Se você estiver sem internet em campo, pode registrar a visita normalmente e usar o botão "Guardar no dispositivo para enviar depois" no formulário. A visita fica salva só no seu aparelho. Quando tiver conexão de novo, use o botão abaixo para enviar todas de uma vez.
        </p>
        <div class="flex flex-wrap gap-3 mt-4">
            <a href="{{ route('agente.visitas.create') }}"
               class="inline-flex items-center rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500/60">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Cadastrar visita
            </a>
            <span x-show="online">
                <a href="{{ route('agente.visitas.sync') }}"
                   class="inline-flex items-center rounded-xl bg-amber-500 px-4 py-2.5 text-sm font-semibold text-amber-950 shadow-sm transition hover:bg-amber-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/50 dark:text-amber-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Enviar visitas salvas no dispositivo
                </a>
            </span>
        </div>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
            Use o botão acima quando tiver internet para enviar as visitas que você guardou sem conexão.
        </p>
    </section>

    <!-- Busca (atualiza ao digitar) -->
    <section class="rounded-xl border border-gray-200/80 bg-white p-5 shadow-sm dark:border-gray-600 dark:bg-gray-800">
        <div class="flex flex-col sm:flex-row sm:items-end gap-4">
            <div class="flex-1">
                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Busca inteligente</label>
                <div class="flex items-center gap-2">
                    <input type="text" id="search" name="busca" value="{{ old('busca', request('busca')) }}"
                           data-live-url="{{ route('agente.visitas.index') }}" data-live-param="busca"
                           data-live-loading-id="search-loading"
                           placeholder="Local, profissional, doença, atividade, pendentes, concluídas ou data..."
                           class="w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-2.5 text-gray-900 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                    <span id="search-loading" class="hidden text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap" aria-live="polite">Buscando…</span>
                </div>
            </div>
        </div>
    </section>

    @if($locaisComPendenciasNaoRevisitadas->isNotEmpty())
        <section class="rounded-xl border border-amber-300/90 bg-amber-50 p-5 shadow-sm dark:border-amber-700 dark:bg-amber-950/40">
            <h3 class="mb-2 text-lg font-semibold text-amber-950 dark:text-amber-100">
                Locais em que há pendências e não foram revisitados
            </h3>
            <ul class="list-inside list-disc space-y-2 text-sm text-amber-950 dark:text-amber-100">
                @foreach ($locaisComPendenciasNaoRevisitadas as $local)
                    @php
                        $ultimaPendencia = $local->visitas()->where('vis_pendencias', true)->latest('vis_data')->first();
                    @endphp
                    <li>
                        {{ $local->loc_endereco }}, {{ $local->loc_numero ?? 'S/N' }} – {{ $local->loc_bairro }}, {{ $local->loc_cidade }}/{{ $local->loc_estado }}
                        <span class="ml-2 text-xs italic text-amber-800 dark:text-amber-200/90">
                            Última pendência em {{ \Carbon\Carbon::parse($ultimaPendencia->vis_data)->format('d/m/Y') }}
                        </span>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif

    <!-- Contador de resultados -->
    <section class="rounded-xl border border-gray-200/80 bg-white p-5 shadow-sm dark:border-gray-600 dark:bg-gray-800">
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
            Exibindo {{ $visitas->count() }} de {{ $visitas->total() }} visita(s) registradas.
            @if(request('busca'))
                <span class="text-gray-500">Resultados para: <strong>{{ request('busca') }}</strong></span>
            @endif
        </p>

        <div class="overflow-x-auto rounded-lg ring-1 ring-gray-200/80 dark:ring-gray-600">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900/80">
                    <tr>
                        <th class="p-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Código</th>
                        <th class="p-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Data</th>
                        <th class="p-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Pendência</th>
                        <th class="p-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Atividade</th>
                        <th class="p-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Local</th>
                        <th class="p-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Profissional (ACE/ACS)</th>
                        <th class="p-4 text-center text-sm font-semibold text-gray-700 dark:text-gray-300">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($visitas as $visita)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-600">
                            <td class="p-4 text-gray-800 dark:text-gray-100">
                                <span class="inline-block bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-gray-200 text-xs font-semibold px-2 py-1 rounded">
                                    #{{ $visita->vis_id }}
                                </span>
                            </td>
                           <td class="p-4 text-gray-800 dark:text-gray-100 leading-tight">
                                <div class="font-semibold">
                                    {{ \Carbon\Carbon::parse($visita->vis_data)->format('d/m/Y') }}
                                </div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ \Carbon\Carbon::parse($visita->vis_data)->translatedFormat('l') }}
                                </div>
                            </td>
                            <td class="p-4 text-gray-800 dark:text-gray-100">
                                @if($visita->vis_pendencias)
                                    <span class="inline-block bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">
                                        Pendente
                                    </span>

                                    @php
                                        $revisitaPosterior = $visita->local->visitas()
                                            ->where('vis_data', '>', $visita->vis_data)
                                            ->orderBy('vis_data')
                                            ->first();
                                    @endphp

                                    @if($revisitaPosterior)
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 italic">
                                            Revisitado em {{ \Carbon\Carbon::parse($revisitaPosterior->vis_data)->format('d/m/Y') }}
                                        </div>
                                    @endif
                                @else
                                    <span class="inline-block bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">
                                        Concluída
                                    </span>
                                @endif
                            </td>
                            <td class="p-4 text-gray-800 dark:text-gray-100" title="{{ \App\Helpers\MsTerminologia::atividadeNome($visita->vis_atividade) }}">
                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ \App\Helpers\MsTerminologia::atividadeLabel($visita->vis_atividade) ?: 'Não informado' }}
                                </div>
                            </td>
                            <td class="p-4 text-gray-800 dark:text-gray-100 leading-tight">
                                <div class="font-semibold">{{ $visita->local->loc_endereco }}, {{ $visita->local->loc_numero }}</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                    Bairro/Localidade: {{ $visita->local->loc_bairro }}<br>
                                    Cód.: {{ $visita->local->loc_codigo_unico }}<br>
                                    Resp.: {{ $visita->local->loc_responsavel_nome ?? 'Não informado' }}
                                </div>
                            </td>
                            <td class="p-4 text-gray-800 dark:text-gray-100">
                                <div class="font-semibold">{{ $visita->usuario->use_nome }}</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ \App\Models\User::perfilLabel($visita->usuario->use_perfil) }}
                                </div>
                            </td>
                            <td class="p-4 text-center">
                                <div x-show="online" class="flex justify-center gap-3">
                                    <a href="{{ route('agente.visitas.show', $visita) }}"
                                        class="btn-acesso-principal inline-flex items-center gap-2 px-3 py-2 text-white text-sm font-medium rounded-lg shadow transition"
                                        aria-label="Visualizar visita">
                                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        Visualizar
                                    </a>
                                    <a href="{{ route('agente.visitas.edit', $visita) }}"
                                        class="inline-flex items-center gap-2 px-3 py-2 bg-gray-700 hover:bg-gray-800 text-white text-sm font-medium rounded-lg shadow transition"
                                        aria-label="Editar visita">
                                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 20h9M16.5 3.5a2.121 2.121 0 113 3L7 19l-4 1 1-4L16.5 3.5z" />
                                        </svg>
                                        Editar
                                    </a> 
                                    <form method="POST" action="{{ route('agente.visitas.destroy', $visita) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('Tem certeza que deseja excluir esta visita? Esta ação não pode ser desfeita.')"
                                                class="inline-flex items-center gap-2 px-3 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg shadow transition"
                                                aria-label="Excluir visita">
                                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                    d="M4 6H20 M9 6V4a2 2 0 012-2h2a2 2 0 012 2v2 M6 6v14a2 2 0 002 2h8a2 2 0 002-2V6 M10 11v6 M14 11v6" />
                                                </svg>
                                            Excluir
                                        </button>
                                    </form>
                                </div>
                                <p x-show="!online" class="text-xs text-gray-500 dark:text-gray-400">Offline — edição e exclusão não disponíveis</p>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center">
                                <div class="flex flex-col items-center justify-center max-w-sm mx-auto">
                                    <div class="w-14 h-14 rounded-full bg-gray-100 dark:bg-gray-600 flex items-center justify-center mb-3">
                                        <svg class="w-7 h-7 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2V5a2 2 0 00-2-2H9a2 2 0 00-2 2v2" />
                                        </svg>
                                    </div>
                                    <p class="text-gray-600 dark:text-gray-400 font-medium">Nenhuma visita registrada.</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-500 mt-1">Registre a primeira visita para começar.</p>
                                    <a href="{{ route('agente.visitas.create') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg shadow transition">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        Registrar visita
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <x-pagination-relatorio :paginator="$visitas->appends(request()->query())" item-label="visitas" />
    </section>
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