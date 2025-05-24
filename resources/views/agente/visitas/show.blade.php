@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6 space-y-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Detalhes da Visita</h1>

    <section class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow space-y-4">
        <div>
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Informações Gerais</h2>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                Abaixo estão os detalhes completos da visita selecionada.
            </p>
        </div>

        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm text-gray-800 dark:text-gray-100">
            <div>
                <dt class="font-medium">Data da Visita</dt>
                <dd>{{ \Carbon\Carbon::parse($visita->vis_data)->format('d/m/Y') }}</dd>
            </div>

            <div>
                <dt class="font-medium">Agente Responsável</dt>
                <dd>{{ $visita->usuario->use_nome }}</dd>
            </div>

            <div class="sm:col-span-2">
                <dt class="font-medium">Local Visitado</dt>
                <dd>{{ $visita->local->loc_endereco }}, {{ $visita->local->loc_numero }} - {{ $visita->local->loc_bairro }}, {{ $visita->local->loc_cidade }}/{{ $visita->local->loc_estado }}</dd>
            </div>

            <div class="sm:col-span-2">
                <dt class="font-medium">Doenças Detectadas</dt>
                <dd class="mt-1">
                    @foreach($visita->doencas as $doenca)
                        <span class="inline-block bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300 mr-1 mb-1">
                            {{ $doenca->doe_nome }}
                        </span>
                    @endforeach
                </dd>
            </div>

            <div class="sm:col-span-2">
                <dt class="font-medium">Observações</dt>
                <dd class="mt-1 text-gray-700 dark:text-gray-200">
                    {{ $visita->vis_observacoes ?: 'Nenhuma observação registrada.' }}
                </dd>
            </div>
        </dl>

        <div class="flex justify-end gap-2 pt-4">
            <a href="{{ route('agente.visitas.edit', $visita) }}"
               class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow transition">
                Editar
            </a>
            <a href="{{ route('agente.visitas.index') }}"
               class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg shadow transition">
                Voltar
            </a>
        </div>
    </section>
</div>
@endsection