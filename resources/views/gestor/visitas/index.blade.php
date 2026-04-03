@extends('layouts.app')

@section('og_title', config('app.name') . ' — Visitas')
@section('og_description', 'Visitas de vigilância entomológica e controle vetorial registradas. Visualize e busque visitas realizadas pelos profissionais (ACE/ACS).')

@section('content')
<div class="space-y-6">
    <x-breadcrumbs :items="[['label' => 'Página Inicial', 'url' => route('dashboard')], ['label' => 'Visitas']]" />
    <h1 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-gray-100">Visitas</h1>

    @if(session('success'))
        <x-alert type="success" :message="session('success')" />
    @endif
    @if(session('warning'))
        <x-alert type="warning" :message="session('warning')" :autodismiss="false" />
    @endif
    @if(session('error'))
        <x-alert type="error" :message="session('error')" />
    @endif

    <section class="rounded-xl border border-gray-200/80 bg-white p-5 shadow-sm dark:border-gray-600 dark:bg-gray-800">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Visitas</h2>
        <p class="mt-2 text-sm leading-relaxed text-gray-600 dark:text-gray-400">
            Visualize e busque visitas registradas pelos profissionais de campo (ACE/ACS).
        </p>
    </section>

    <!-- Busca (atualiza ao digitar) -->
    <section class="rounded-xl border border-gray-200/80 bg-white p-5 shadow-sm dark:border-gray-600 dark:bg-gray-800">
        <div class="flex flex-col sm:flex-row sm:items-end gap-4">
            <div class="flex-1">
                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Busca inteligente</label>
                <div class="flex items-center gap-2">
                    <input type="text" id="search" name="busca" value="{{ old('busca', request('busca')) }}"
                           data-live-url="{{ route('gestor.visitas.index') }}" data-live-param="busca"
                           data-live-loading-id="search-loading-gestor-visitas"
                           placeholder="Local, profissional, doença, atividade, pendentes, concluídas ou data..."
                           class="w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-2.5 text-gray-900 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                    <span id="search-loading-gestor-visitas" class="hidden text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap" aria-live="polite">Buscando…</span>
                </div>
            </div>
        </div>
    </section>

    @if($locaisComPendenciasNaoRevisitadas->isNotEmpty())
        <section class="rounded-xl border border-amber-300/90 bg-amber-50 p-5 shadow-sm dark:border-amber-700 dark:bg-amber-950/40">
            <h3 class="text-lg font-semibold text-yellow-800 dark:text-yellow-200 mb-2">
                Locais em que há pendências e não foram revisitados
            </h3>
            <ul class="space-y-2 text-sm text-yellow-900 dark:text-yellow-100 list-disc list-inside">
                @foreach ($locaisComPendenciasNaoRevisitadas as $local)
                    @php
                        $ultimaPendencia = $local->visitas()->where('vis_pendencias', true)->latest('vis_data')->first();
                    @endphp
                    <li>
                        {{ $local->loc_endereco }}, {{ $local->loc_numero ?? 'S/N' }} – {{ $local->loc_bairro }}, {{ $local->loc_cidade }}/{{ $local->loc_estado }}
                        <span class="text-xs text-yellow-700 dark:text-yellow-300 ml-2 italic">
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
                                <div class="flex justify-center gap-3">
                                    <a href="{{ route('gestor.visitas.show', $visita) }}"
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
                                </div>
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
                                    <p class="text-sm text-gray-500 dark:text-gray-500 mt-1">As visitas aparecerão aqui quando os profissionais (ACE/ACS) as registrarem.</p>
                                    <a href="{{ route('gestor.locais.index') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 dark:bg-gray-600 dark:hover:bg-gray-500 text-white text-sm font-semibold rounded-lg shadow transition">
                                        Ver locais cadastrados
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
@endsection