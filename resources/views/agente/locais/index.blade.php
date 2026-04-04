@extends('layouts.app')

@section('og_title', config('app.name') . ' · ' . __('Locais'))
@section('og_description', __('Locais de visitação. Visualize, cadastre e edite locais para realização de visitas de vigilância entomológica e controle vetorial.'))

@section('content')
<div class="v-page">
    <x-breadcrumbs :items="[['label' => __('Página Inicial'), 'url' => route('dashboard')], ['label' => __('Locais')]]" />
    <x-page-header :eyebrow="__('Cadastro territorial')" :title="__('Locais')" />

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
        <div class="v-card v-alert-erp mb-4 border-amber-200/60 dark:border-amber-900/40" role="alert">
            <p class="text-sm font-medium text-amber-950 dark:text-amber-100">{{ __('Coordenadas duplicadas') }}</p>
            <p class="mt-1 text-sm text-amber-900/90 dark:text-amber-200/85">{{ __('Existem imóveis com a mesma coordenada (latitude e longitude) cadastrada. Revise os locais para evitar duplicidade.') }}</p>
        </div>
    @endif

    <!-- Card introdutório -->
    <section class="v-card">
        <h2 class="v-section-title">Locais de visitação</h2>
        <p class="mt-2 text-gray-600 dark:text-gray-400">
            Visualize, cadastre e edite locais para realização de visitas de vigilância entomológica e controle vetorial.
        </p>
        <p class="mt-3 text-sm text-gray-600 dark:text-gray-400">
            <strong>Uso offline:</strong> Sem internet você pode cadastrar o local no dispositivo e sincronizar depois. Antes de ir a campo, abra esta lista e a tela de <strong>Cadastrar local</strong> pelo menos uma vez com internet para poder usá-las offline.
        </p>
        <a href="{{ route('agente.locais.create') }}"
           class="v-btn-compact v-btn-compact--blue mt-4">
            <x-heroicon-o-plus class="h-4 w-4 shrink-0" aria-hidden="true" />
            Cadastrar Local
        </a>
    </section>

    <!-- Busca (filtra ao digitar) -->
    <section class="v-card">
        <div class="flex flex-col sm:flex-row sm:items-end gap-4">
            <div class="flex-1">
                <label for="search" class="v-toolbar-label mb-1">Busca inteligente</label>
                <div class="flex items-center gap-2">
                    <input type="text" id="search" name="search" value="{{ old('search', request('search')) }}"
                           data-live-url="{{ route('agente.locais.index') }}" data-live-param="search"
                           data-live-loading-id="search-loading-locais"
                           placeholder="Endereço, bairro, código, tipo (residencial, comercial, terreno) ou zona (urbano, rural)..."
                           class="v-input">
                    <span id="search-loading-locais" class="hidden text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap" aria-live="polite">Buscando…</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Tabela de Locais -->
    <section class="v-card">
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
            Exibindo {{ $locais->count() }} de {{ $locais->total() }} local(is) cadastrados.
            @if(request('search'))
                <span class="text-gray-500">Resultados para: <strong>{{ request('search') }}</strong></span>
            @endif
        </p>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
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
                        <tr class="v-table-row-interactive">
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
                                    <span class="inline-block rounded bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-800 dark:bg-slate-700 dark:text-slate-200">
                                        Urbana
                                    </span>
                                @elseif ($local->loc_zona == 'R')
                                    <span class="inline-block rounded bg-amber-100 px-2 py-1 text-xs font-semibold text-amber-900 dark:bg-amber-900/70 dark:text-amber-200">
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
                                <div class="flex flex-wrap items-center justify-center gap-1.5">
                                    <a href="{{ route('agente.locais.show', $local) }}"
                                       class="v-btn-icon-primary"
                                       title="{{ __('Visualizar') }}"
                                       aria-label="{{ __('Visualizar local') }}">
                                       <x-heroicon-o-eye class="h-4 w-4 shrink-0" />
                                    </a>
                                    @if(!$local->isPrimary())
                                    <a href="{{ route('agente.locais.edit', $local) }}"
                                        class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-600 shadow-sm transition hover:bg-slate-50 hover:text-slate-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400/40 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700"
                                        title="{{ __('Editar') }}"
                                        aria-label="{{ __('Editar local') }}">
                                        <x-heroicon-o-pencil-square class="h-4 w-4 shrink-0" />
                                    </a>
                                    <form method="POST" action="{{ route('agente.locais.destroy', $local) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('Tem certeza que deseja excluir este local? Esta ação não pode ser desfeita.')"
                                                class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border border-red-200 bg-red-50 text-red-600 shadow-sm transition hover:bg-red-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-400/40 dark:border-red-900/50 dark:bg-red-950/40 dark:text-red-300 dark:hover:bg-red-950/60"
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
                            <td colspan="9" class="p-8 text-center">
                                <div class="flex flex-col items-center justify-center max-w-sm mx-auto">
                                    <div class="w-14 h-14 rounded-full bg-gray-100 dark:bg-gray-600 flex items-center justify-center mb-3">
                                        <x-heroicon-o-map-pin class="h-7 w-7 shrink-0 text-gray-400 dark:text-gray-500" />
                                    </div>
                                    <p class="text-gray-600 dark:text-gray-400 font-medium">Nenhum local cadastrado.</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-500 mt-1">Cadastre o primeiro local para realizar visitas.</p>
                                    <a href="{{ route('agente.locais.create') }}" class="v-btn-compact v-btn-compact--blue mt-4">
                                        <x-heroicon-o-plus class="h-4 w-4 shrink-0" aria-hidden="true" />
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

    <section class="v-card">
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