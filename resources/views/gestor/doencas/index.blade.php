<!-- resources/views/gestor/doencas/index.blade.php -->
@extends('layouts.app')

@section('og_title', config('app.name') . ' · Doenças')
@section('og_description', 'Doenças monitoradas no município. Visualize, edite e cadastre doenças para as visitas de vigilância entomológica e controle vetorial.')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <x-breadcrumbs :items="[['label' => 'Página Inicial', 'url' => route('dashboard')], ['label' => 'Doenças']]" />
    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Doenças</h1>

    @if(session('success'))
        <x-alert type="success" :message="session('success')" />
    @endif
    @if(session('error'))
        <x-alert type="error" :message="session('error')" />
    @endif

    <!-- Card introdutório -->
    <section class="rounded-xl border border-gray-200/80 bg-white p-5 shadow-sm dark:border-gray-600 dark:bg-gray-800">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Doenças monitoradas</h2>
        <p class="mt-2 text-gray-600 dark:text-gray-400">
            Visualize, edite e exclua doenças do sistema. Para adicionar novas, clique no botão abaixo.
        </p>
        <a href="{{ route('gestor.doencas.create') }}"
           class="inline-flex items-center px-4 py-2 mt-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-lg shadow-md transition">
            <x-heroicon-o-plus class="mr-2 h-5 w-5 shrink-0" />
            Cadastrar Doença
        </a>
    </section>

    <!-- Busca (atualiza ao digitar) -->
    <section class="rounded-xl border border-gray-200/80 bg-white p-5 shadow-sm dark:border-gray-600 dark:bg-gray-800">
        <div class="flex flex-col sm:flex-row sm:items-end gap-4">
            <div class="flex-1">
                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Busca inteligente</label>
                <div class="flex items-center gap-2">
                    <input type="text" name="search" id="search" value="{{ old('search', request('search')) }}"
                           data-live-url="{{ route('gestor.doencas.index') }}" data-live-param="search"
                           data-live-loading-id="search-loading-doencas"
                           placeholder="Nome, sintomas, transmissão ou medidas..."
                           class="w-full rounded-lg border border-gray-200 bg-gray-50 text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 dark:focus:border-blue-600 dark:focus:ring-blue-600 px-4 py-2">
                    <span id="search-loading-doencas" class="hidden text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap" aria-live="polite">Buscando…</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Tabela de Doenças com pré-visualização -->
    <section class="rounded-xl border border-gray-200/80 bg-white p-5 shadow-sm dark:border-gray-600 dark:bg-gray-800">
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
            Exibindo {{ $doencas->count() }} de {{ $doencas->total() }} doença(s) cadastrada(s).
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
                                <div class="flex justify-center gap-1.5">
                                    <a href="{{ route('gestor.doencas.show', $doenca) }}"
                                       class="btn-acesso-principal inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg shadow-sm transition hover:opacity-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500/50 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-gray-900"
                                       title="{{ __('Visualizar') }}"
                                       aria-label="{{ __('Visualizar doença') }}">
                                       <x-heroicon-o-eye class="h-4 w-4 shrink-0" />
                                    </a>
                                    <a href="{{ route('gestor.doencas.edit', $doenca) }}"
                                        class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-600 shadow-sm transition hover:bg-slate-50 hover:text-slate-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400/40 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700"
                                        title="{{ __('Editar') }}"
                                        aria-label="{{ __('Editar doença') }}">
                                        <x-heroicon-o-pencil-square class="h-4 w-4 shrink-0" />
                                    </a>
                                    <form method="POST" action="{{ route('gestor.doencas.destroy', $doenca) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                onclick="return confirm('Tem certeza que deseja excluir esta doença? Esta ação não pode ser desfeita.')"
                                                class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border border-red-200 bg-red-50 text-red-600 shadow-sm transition hover:bg-red-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-400/40 dark:border-red-900/50 dark:bg-red-950/40 dark:text-red-300 dark:hover:bg-red-950/60"
                                                title="{{ __('Excluir') }}"
                                                aria-label="{{ __('Excluir doença') }}">
                                                <x-heroicon-o-trash class="h-4 w-4 shrink-0" />
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center">
                                <div class="flex flex-col items-center justify-center max-w-sm mx-auto">
                                    <div class="w-14 h-14 rounded-full bg-gray-100 dark:bg-gray-600 flex items-center justify-center mb-3">
                                        <x-heroicon-o-document-text class="h-7 w-7 shrink-0 text-gray-400 dark:text-gray-500" />
                                    </div>
                                    <p class="text-gray-600 dark:text-gray-400 font-medium">Nenhuma doença cadastrada.</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-500 mt-1">Cadastre as doenças monitoradas no município.</p>
                                    <a href="{{ route('gestor.doencas.create') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg shadow transition">
                                        <x-heroicon-o-plus class="mr-2 h-4 w-4 shrink-0" />
                                        Cadastrar doença
                                    </a>
                                </div>
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
