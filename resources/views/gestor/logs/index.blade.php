@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Registro de atividades</h1>

    @if(session('success'))
        <x-alert type="success" :message="session('success')" />
    @endif

    <section class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Histórico de ações no sistema</h2>
        <p class="mt-2 text-gray-600 dark:text-gray-400">
            Listagem das ações realizadas pelos usuários. Use o campo abaixo para filtrar por nome do usuário, tipo de ação ou entidade.
        </p>
    </section>

    <section x-data="{ search: '{{ request('search') }}' }" class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow">
        <div class="flex flex-col sm:flex-row sm:items-center gap-4">
            <div class="flex-1">
                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Filtrar registros</label>
                <input type="text" id="search" name="search" x-model="search" x-init="$el.focus()"
                       @input.debounce.500ms="window.location.href = '{{ route('gestor.logs.index') }}' + '?search=' + encodeURIComponent(search)"
                       placeholder="Nome do usuário, tipo de ação ou entidade..."
                       class="w-full rounded-md bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm px-4 py-2">
            </div>
        </div>
    </section>

    <section class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow">
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
            Exibindo {{ $logs->count() }} de {{ $logs->total() }} registro(s).
            @if(request('search'))
                <span class="text-gray-500">Resultados para: <strong>{{ request('search') }}</strong></span>
            @endif
        </p>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white dark:bg-gray-800 rounded-lg shadow">
                <thead class="bg-gray-100 dark:bg-gray-700">
                    <tr>
                        <th class="p-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Data</th>
                        <th class="p-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Usuário</th>
                        <th class="p-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Ação</th>
                        <th class="p-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Entidade</th>
                        <th class="p-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Descrição</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($logs as $log)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-600">
                            <td class="p-4 text-gray-800 dark:text-gray-100">{{ \Carbon\Carbon::parse($log->log_data)->format('d/m/Y H:i') }}</td>
                            <td class="p-4 text-gray-800 dark:text-gray-100">{{ $log->usuario->use_nome ?? 'Desconhecido' }}</td>
                            <td class="p-4 text-gray-800 dark:text-gray-100">{{ ucfirst($log->log_acao) }}</td>
                            <td class="p-4 text-gray-800 dark:text-gray-100">{{ $log->log_entidade }}</td>
                            <td class="p-4 text-gray-800 dark:text-gray-100">{{ $log->log_descricao }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-6 text-center text-gray-600 dark:text-gray-400">Nenhum registro encontrado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $logs->links() }}
        </div>
    </section>
</div>
@endsection