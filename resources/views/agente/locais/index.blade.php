@extends('layouts.app')

@section('og_title', config('app.name') . ' — Locais')
@section('og_description', 'Locais de visitação. Visualize, cadastre e edite locais para realização de visitas de vigilância entomológica e controle vetorial.')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <x-breadcrumbs :items="[['label' => 'Página Inicial', 'url' => route('dashboard')], ['label' => 'Locais']]" />
    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Locais</h1>

    @if(session('success'))
        <x-alert type="success" :message="session('success')" />
        @if(session('created_local_id'))
            <div class="flex flex-wrap gap-3 mt-2">
                <a href="{{ route('agente.locais.show', session('created_local_id')) }}" class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:underline">Ver local cadastrado</a>
            </div>
        @endif
    @endif
    @if(session('warning'))
        <x-alert type="warning" :message="session('warning')" />
    @endif
    @if(session('error'))
        <x-alert type="error" :message="session('error')" />
    @endif

    <div id="locais-offline-pending-alert" class="hidden p-3 mb-4 rounded bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-200 border border-amber-200 dark:border-amber-800" role="alert">
        <span id="locais-offline-pending-alert-msg"></span>
    </div>

    @if(!empty($coordenadasDuplicadas))
    <div class="p-3 mb-4 rounded bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-200 border border-amber-200 dark:border-amber-800" role="alert">
        Existem imóveis com a mesma coordenada (latitude e longitude) cadastrada. Revise os locais para evitar duplicidade.
    </div>
    @endif

    <!-- Card introdutório -->
    <section class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Locais de visitação</h2>
        <p class="mt-2 text-gray-600 dark:text-gray-400">
            Visualize, cadastre e edite locais para realização de visitas de vigilância entomológica e controle vetorial.
        </p>
        <p class="mt-3 text-sm text-gray-600 dark:text-gray-400">
            <strong>Uso offline:</strong> Sem internet você pode cadastrar o local no dispositivo e sincronizar depois. Antes de ir a campo, abra esta lista e a tela de <strong>Cadastrar local</strong> pelo menos uma vez com internet para poder usá-las offline.
        </p>
        <a href="{{ route('agente.locais.create') }}"
           class="inline-flex items-center px-4 py-2 mt-4 bg-green-600 hover:bg-green-700 text-white font-semibold text-sm rounded-lg shadow-md transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Cadastrar Local
        </a>
    </section>

    <!-- Busca (filtra ao digitar) -->
    <section class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow">
        <div class="flex flex-col sm:flex-row sm:items-end gap-4">
            <div class="flex-1">
                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Busca inteligente</label>
                <div class="flex items-center gap-2">
                    <input type="text" id="search" name="search" value="{{ old('search', request('search')) }}"
                           data-live-url="{{ route('agente.locais.index') }}" data-live-param="search"
                           data-live-loading-id="search-loading-locais"
                           placeholder="Endereço, bairro, código, tipo (residencial, comercial, terreno) ou zona (urbano, rural)..."
                           class="w-full rounded-md bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm px-4 py-2">
                    <span id="search-loading-locais" class="hidden text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap" aria-live="polite">Buscando…</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Tabela de Locais -->
    <section class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow">
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
            Exibindo {{ $locais->count() }} de {{ $locais->total() }} local(is) cadastrados.
            @if(request('search'))
                <span class="text-gray-500">Resultados para: <strong>{{ request('search') }}</strong></span>
            @endif
        </p>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white dark:bg-gray-800 rounded-lg shadow">
                <thead class="bg-gray-100 dark:bg-gray-700">
                    <tr>
                        <th class="p-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Código</th>
                        <th class="p-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Zona</th>
                        <th class="p-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Imóvel</th>
                        <th class="p-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Endereço</th>
                        <th class="p-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Bairro</th>
                        <th class="p-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Cidade</th>
                        <th class="p-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Responsável</th>
                        <th class="p-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Coordenadas</th>
                        <th class="p-4 text-center text-sm font-semibold text-gray-700 dark:text-gray-300">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($locais as $local)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-600">
                            <td class="p-4 text-gray-800 dark:text-gray-100">
                                <span class="inline-block bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-gray-200 text-xs font-semibold px-2 py-1 rounded">
                                    #{{ $local->loc_codigo_unico }}
                                </span>
                                @if($local->isPrimary())
                                    <span class="block mt-1 text-xs text-gray-500 dark:text-gray-400" title="Local primário do município">Primário</span>
                                @endif
                            </td>
                            <td class="p-4 text-gray-800 dark:text-gray-100">
                                @if ($local->loc_zona == 'U')
                                    <span class="inline-block bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-200 px-2 py-1 rounded text-xs font-semibold">
                                        Urbana
                                    </span>
                                @elseif ($local->loc_zona == 'R')
                                    <span class="inline-block bg-green-100 text-teal-800 dark:bg-teal-800 dark:text-teal-200 px-2 py-1 rounded text-xs font-semibold">
                                        Rural
                                    </span>
                                @endif
                            </td>
                            <td class="p-4 text-gray-800 dark:text-gray-100 leading-tight">
                                @if ($local->loc_tipo == 'R')
                                    <div class="text-sm text-gray-600 dark:text-gray-400" title="Residencial">
                                        Residencial
                                    </div>
                                @elseif ($local->loc_tipo == 'C')
                                    <div class="text-sm text-gray-600 dark:text-gray-400" title="Comercial">
                                        Comercial
                                    </div>
                                @elseif ($local->loc_tipo == 'T')
                                    <div class="text-sm text-gray-600 dark:text-gray-400" title="Terreno Baldio">
                                        Terreno Baldio
                                    </div>
                                @endif
                            </td>
                            <td class="p-4 text-gray-800 dark:text-gray-100">{{ $local->loc_endereco }}, @if($local->loc_numero) {{ $local->loc_numero }} @else N/A @endif</td>
                            <td class="p-4 text-gray-800 dark:text-gray-100">{{ $local->loc_bairro }}</td>
                            <td class="p-4 text-gray-800 dark:text-gray-100">{{ $local->loc_cidade }}</td>
                            <td class="p-4 text-gray-800 dark:text-gray-100">{{ $local->loc_responsavel_nome ?? 'Não informado' }}</td>
                            <td class="p-4 text-gray-800 dark:text-gray-100">{{ $local->loc_latitude }}, {{ $local->loc_longitude }}</td>
                            <td class="p-4 text-center">
                                <div class="flex justify-center items-center gap-3 flex-wrap">
                                    <a href="{{ route('agente.locais.show', $local) }}"
                                       class="btn-acesso-principal inline-flex items-center gap-2 px-3 py-2 text-white text-sm font-medium rounded-lg shadow transition"
                                       aria-label="Visualizar local">
                                       <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        Visualizar
                                    </a>
                                    @if(!$local->isPrimary())
                                    <a href="{{ route('agente.locais.edit', $local) }}"
                                        class="inline-flex items-center gap-2 px-3 py-2 bg-gray-700 hover:bg-gray-800 text-white text-sm font-medium rounded-lg shadow transition"
                                        aria-label="Editar local">
                                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 20h9M16.5 3.5a2.121 2.121 0 113 3L7 19l-4 1 1-4L16.5 3.5z" />
                                        </svg>
                                        Editar
                                    </a>
                                    <form method="POST" action="{{ route('agente.locais.destroy', $local) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('Tem certeza que deseja excluir este local? Esta ação não pode ser desfeita.')"
                                                class="inline-flex items-center gap-2 px-3 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg shadow transition"
                                                aria-label="Excluir local">
                                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 6H20 M9 6V4a2 2 0 012-2h2a2 2 0 012 2v2 M6 6v14a2 2 0 002 2h8a2 2 0 002-2V6 M10 11v6 M14 11v6" />
                                                </svg>
                                                Excluir
                                            </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="p-8 text-center">
                                <div class="flex flex-col items-center justify-center max-w-sm mx-auto">
                                    <div class="w-14 h-14 rounded-full bg-gray-100 dark:bg-gray-600 flex items-center justify-center mb-3">
                                        <svg class="w-7 h-7 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                    </div>
                                    <p class="text-gray-600 dark:text-gray-400 font-medium">Nenhum local cadastrado.</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-500 mt-1">Cadastre o primeiro local para realizar visitas.</p>
                                    <a href="{{ route('agente.locais.create') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg shadow transition">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        Cadastrar local
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <x-pagination-relatorio :paginator="$locais" item-label="locais" />
    </section>

    <section class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow">
        <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-1">O que significa &quot;Primário&quot;?</h3>
        <p class="text-sm text-gray-600 dark:text-gray-400">O local <strong>primário</strong> é o endereço de referência do município (cidade/estado) no sistema. Foi configurado previamente pelo gestor e não pode ser editado nem excluído pela interface. Os demais locais são os imóveis visitados pelos profissionais (ACE/ACS).</p>
    </section>
</div>
<script>
(function() {
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
        window.VisitaOfflineGetLocalPendingCount('agente').then(function(count) {
            if (count > 0) {
                msgEl.textContent = 'Você tem ' + count + ' local(is) guardado(s) no dispositivo. Sincronize quando se reconectar.';
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