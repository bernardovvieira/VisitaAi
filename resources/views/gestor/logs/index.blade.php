@extends('layouts.app')

@section('og_title', config('app.name') . ' — Auditoria')
@section('og_description', 'Registros de auditoria. Histórico de ações realizadas pelos usuários no sistema.')

@section('content')
<div class="space-y-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Auditoria</h1>

    @if(session('success'))
        <x-alert type="success" :message="session('success')" />
    @endif

    <section class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Registros de auditoria</h2>
        <p class="mt-2 text-gray-600 dark:text-gray-400">
            Histórico de ações realizadas pelos usuários (inclui IP e dispositivo quando disponível). Use o filtro para buscar por usuário, ação, entidade ou IP.
        </p>
    </section>

    <section class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow">
        <div class="flex flex-col sm:flex-row sm:items-end gap-4">
            <div class="flex-1">
                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Busca inteligente</label>
                <input type="text" id="search" name="search" value="{{ old('search', request('search')) }}"
                       data-live-url="{{ route('gestor.logs.index') }}" data-live-param="search"
                       placeholder="Usuário, ação, entidade, descrição ou IP..."
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
                        <th class="p-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">IP / Dispositivo</th>
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
                            <td class="p-4 text-gray-800 dark:text-gray-100">
                                @if($log->log_ip || $log->log_user_agent)
                                    <span title="{{ $log->log_user_agent ?? '' }}">{{ $log->log_ip ?? '—' }}</span>
                                    @if($log->log_user_agent)
                                        <br><span class="text-xs text-gray-500 dark:text-gray-400 truncate max-w-[200px] inline-block" title="{{ $log->log_user_agent }}">{{ Str::limit($log->log_user_agent, 50) }}</span>
                                    @endif
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-6 text-center text-gray-600 dark:text-gray-400">Nenhum registro encontrado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-pagination-relatorio :paginator="$logs" item-label="registros" />
    </section>
</div>
@endsection