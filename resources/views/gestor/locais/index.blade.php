<!-- resources/views/gestor/locais/index.blade.php -->
@extends('layouts.app')

@section('og_title', config('app.name') . ' · Locais')
@section('og_description', 'Locais cadastrados pelos profissionais (ACE/ACS). Visualize os detalhes de cada endereço de visitação (vigilância entomológica e controle vetorial).')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <x-breadcrumbs :items="[['label' => 'Página Inicial', 'url' => route('dashboard')], ['label' => 'Locais']]" />
    <h1 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-gray-100">Locais</h1>

    @if(session('success'))
        <x-alert type="success" :message="session('success')" />
    @endif
    @if(session('warning'))
        <x-alert type="warning" :message="session('warning')" :autodismiss="false" />
    @endif
    @if(session('error'))
        <x-alert type="error" :message="session('error')" />
    @endif

    @if(!empty($coordenadasDuplicadas))
    <div class="p-3 mb-4 rounded bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-200 border border-amber-200 dark:border-amber-800" role="alert">
        Existem imóveis com a mesma coordenada (latitude e longitude) cadastrada. Revise os locais para evitar duplicidade.
    </div>
    @endif

    <section class="rounded-xl border border-gray-200/80 bg-white p-5 shadow-sm dark:border-gray-600 dark:bg-gray-800">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Locais cadastrados</h2>
        <p class="mt-2 text-gray-600 dark:text-gray-400">
            Locais registrados pelos profissionais (ACE/ACS). Visualize os detalhes de cada endereço.
        </p>
    </section>

    <section class="rounded-xl border border-gray-200/80 bg-white p-5 shadow-sm dark:border-gray-600 dark:bg-gray-800">
        <div class="flex flex-col sm:flex-row sm:items-end gap-4">
            <div class="flex-1">
                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Busca inteligente</label>
                <div class="flex items-center gap-2">
                    <input type="text" id="search" name="search" value="{{ old('search', request('search')) }}"
                           data-live-url="{{ route('gestor.locais.index') }}" data-live-param="search"
                           data-live-loading-id="search-loading-gestor-locais"
                           placeholder="Endereço, bairro, código, tipo (residencial, comercial, terreno) ou zona (urbano, rural)..."
                           class="w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-2.5 text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                    <span id="search-loading-gestor-locais" class="hidden text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap" aria-live="polite">Buscando…</span>
                </div>
            </div>
        </div>
    </section>

    <section class="rounded-xl border border-gray-200/80 bg-white p-5 shadow-sm dark:border-gray-600 dark:bg-gray-800">
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
                                <a href="{{ route('gestor.locais.show', $local) }}"
                                    class="btn-acesso-principal inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg shadow-sm transition hover:opacity-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500/50 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-gray-900"
                                    title="{{ __('Visualizar') }}"
                                    aria-label="{{ __('Visualizar local') }}">
                                    <x-heroicon-o-eye class="h-4 w-4 shrink-0" />
                                </a>
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
                                    <p class="text-sm text-gray-500 dark:text-gray-500 mt-1">Os locais aparecerão aqui quando os profissionais (ACE/ACS) os cadastrarem.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-pagination-relatorio :paginator="$locais" item-label="locais" />
    </section>

    <section class="rounded-xl border border-gray-200/80 bg-white p-5 shadow-sm dark:border-gray-600 dark:bg-gray-800">
        <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-1">O que significa &quot;Primário&quot;?</h3>
        <p class="text-sm text-gray-600 dark:text-gray-400">O local <strong>primário</strong> é o endereço de referência do município (cidade/estado) no sistema. Foi configurado previamente pelo gestor e não pode ser editado nem excluído pela interface. Os demais locais são os imóveis visitados pelos profissionais (ACE/ACS).</p>
    </section>
</div>
@endsection