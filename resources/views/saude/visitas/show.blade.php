@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6 max-w-4xl space-y-10">

    {{-- Botão Voltar --}}
    <div>
        <a href="{{ route('saude.visitas.index') }}"
           class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-semibold text-sm rounded-lg shadow transition">
            <svg class="h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Voltar
        </a>
    </div>

    {{-- Cabeçalho --}}
    <section class="p-6 bg-white dark:bg-gray-700 rounded-lg shadow space-y-2">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Detalhes da Visita Epidemiológica</h1>
        <p class="text-sm text-gray-600 dark:text-gray-300">Informações completas da visita registrada no sistema.</p>
    </section>

    {{-- Local --}}
    <section class="p-6 bg-white dark:bg-gray-700 rounded-lg shadow space-y-6">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 border-b pb-2">Local Visitado</h2>
        <dl class="grid grid-cols-1 sm:grid-cols-4 gap-x-6 gap-y-4 text-sm text-gray-700 dark:text-gray-300">
            <div>
                <dt class="font-medium">Código Único do Imóvel</dt>
                <dd class="mt-1">
                    <span class="inline-block bg-purple-100 text-purple-800 dark:bg-purple-800 dark:text-purple-200 px-2 py-1 rounded text-xs font-semibold">
                        {{ $visita->local->loc_codigo_unico }}
                    </span>
                </dd>
            </div>
            <div>
                <dt class="font-medium">Código da Localidade</dt>
                <dd class="mt-1">{{ $visita->local->loc_codigo ?? 'N/A' }}</dd>
            </div> 
            <div>
                <dt class="font-medium">Categoria da Localidade</dt>
                <dd class="mt-1">{{ $visita->local->loc_categoria ?? 'N/A' }}</dd>
            </div>
            <div>
                <dt class="font-medium">Tipo</dt>
                <dd class="mt-1">{{ ['R' => 'Residencial', 'C' => 'Comercial', 'T' => 'Terreno Baldio'][$visita->local->loc_tipo] ?? 'N/A' }}</dd>
            </div>
            <div>
                <dt class="font-medium">Zona</dt>
                <dd class="mt-1">{{ ['U' => 'Urbana', 'R' => 'Rural'][$visita->local->loc_zona] ?? 'N/A' }}</dd>
            </div>
            <div>
                <dt class="font-medium">Quarteirão</dt>
                <dd class="mt-1">{{ $visita->local->loc_quarteirao ?? 'N/A' }}</dd>
            </div>
            <div>
                <dt class="font-medium">Sequência</dt>
                <dd class="mt-1">{{ $visita->local->loc_sequencia ?? 'N/A' }}</dd>
            </div>
            <div>
                <dt class="font-medium">Lado</dt>
                <dd class="mt-1">{{ $visita->local->loc_lado ?? 'N/A' }}</dd>
            </div>
            <div class="sm:col-span-4" style="padding-top: 0.5rem;">
                <dt class="font-medium">Endereço Completo</dt>
                <dd class="mt-1 text-gray-900 dark:text-gray-100">
                    {{ $visita->local->loc_endereco }}, {{ $visita->local->loc_numero ?? 'S/N' }} - {{ $visita->local->loc_bairro }}, {{ $visita->local->loc_cidade }}/{{ $visita->local->loc_estado }} - {{ $visita->local->loc_pais }} | CEP: {{ $visita->local->loc_cep }}<br>
                    <span class="text-sm text-gray-600 dark:text-gray-400">Complemento: {{ $visita->local->loc_complemento ?? 'N/A' }}</span>
                </dd>
            </div>
        </dl>

        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-sm text-gray-700 dark:text-gray-300">
            <div>
                <dt class="font-medium">Latitude</dt>
                <dd class="mt-1">{{ $visita->local->loc_latitude }}</dd>
            </div>
            <div>
                <dt class="font-medium">Longitude</dt>
                <dd class="mt-1">{{ $visita->local->loc_longitude }}</dd>
            </div>
        </dl>

        <div>
            <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-2">Localização no Mapa</h3>
            <div id="map" class="h-72 rounded-md shadow border border-gray-300"></div>
            <p class="text-sm mt-2 text-gray-600 dark:text-gray-400 italic">
                A posição exibida é baseada nas coordenadas fornecidas.
            </p>
        </div>
    </section>

    {{-- Dados da Visita --}}
    <section class="p-6 bg-white dark:bg-gray-700 rounded-lg shadow space-y-6">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 border-b pb-2">Dados da Visita</h2>
        <dl class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm text-gray-700 dark:text-gray-300">
            <div>
                <dt class="font-medium">Data</dt>
                <dd>{{ \Carbon\Carbon::parse($visita->vis_data)->format('d/m/Y') }}</dd>
            </div>
            <div>
                <dt class="font-medium">Ciclo/Ano</dt>
                <dd>{{ $visita->vis_ciclo ?? 'N/A' }}</dd>
            </div>
            <div>
                <dt class="font-medium">Atividade PNCD</dt>
                <dd>
                    @php
                        $atividades = [
                            '1' => '1 - LI (Levantamento de Índice)',
                            '2' => '2 - LI+T (Levantamento + Tratamento)',
                            '3' => '3 - PPE+T (Ponto Estratégico + Tratamento)',
                            '4' => '4 - T (Tratamento)',
                            '5' => '5 - DF (Delimitação de Foco)',
                            '6' => '6 - PVE (Pesquisa Vetorial Especial)',
                            '7' => '7 - LIRAa (Levantamento de Índice Rápido)',
                            '8' => '8 - PE (Ponto Estratégico)',
                        ];
                    @endphp
                    {{ $atividades[$visita->vis_atividade] ?? 'Não informado' }}
                </dd>
            </div>
            <div>
                <dt class="font-medium">Tipo de Visita</dt>
                <dd>
                    @if ($visita->vis_visita_tipo === 'N')
                        <span class="inline-block bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-200 px-2 py-1 rounded text-xs font-semibold">Normal</span>
                    @elseif ($visita->vis_visita_tipo === 'R')
                        <span class="inline-block bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-200 px-2 py-1 rounded text-xs font-semibold">Recuperação</span>
                    @else
                        <span class="text-gray-500 dark:text-gray-400 italic">Não informado</span>
                    @endif
                </dd>
            </div>
            <div>
                <dt class="font-medium">Pendências</dt>
                <dd>
                    @if ($visita->vis_pendencias)
                        <span class="inline-block bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-200 px-2 py-1 rounded text-xs font-semibold">Houve pendência</span>
                    @else
                        <span class="inline-block bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-200 px-2 py-1 rounded text-xs font-semibold">Nenhuma pendência</span>   
                    @endif
                </dd>
            </div>
        </dl>
    </section>

    {{-- Observações --}}
    <section class="p-6 bg-white dark:bg-gray-700 rounded-lg shadow space-y-2">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 border-b pb-2">Observações</h2>
        <p class="text-sm text-gray-900 dark:text-gray-100">{{ $visita->vis_observacoes ?: 'Nenhuma observação registrada.' }}</p>
    </section>

    {{-- Doenças --}}
    @if ($visita->doencas->count())
    <section class="p-6 bg-white dark:bg-gray-700 rounded-lg shadow space-y-4">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 border-b pb-2">Doenças Identificadas</h2>
        <ul class="list-disc list-inside text-sm text-gray-700 dark:text-gray-300">
            @foreach ($visita->doencas as $doenca)
                <li>{{ $doenca->doe_nome }}</li>
            @endforeach
        </ul>
    </section>
    @endif

    {{-- Depósitos Inspecionados --}}
    <section class="p-6 bg-white dark:bg-gray-700 rounded-lg shadow space-y-6">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 border-b pb-2">Depósitos Inspecionados</h2>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            @foreach (['a1','a2','b','c','d1','d2','e'] as $tipo)
                <div class="bg-gray-100 dark:bg-gray-800 p-4 rounded shadow text-center">
                    <p class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">{{ strtoupper($tipo) }}</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ $visita->{'insp_'.$tipo} ?? 0 }}</p>
                </div>
            @endforeach
            <div class="bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 col-span-2 sm:col-span-4 p-4 rounded-lg text-center font-bold text-lg shadow">
                Eliminados: {{ $visita->vis_depositos_eliminados ?? 0 }}
            </div>
        </div>
    </section>

    {{-- Coleta de Amostra --}}
    @if ($visita->vis_coleta_amostra)
    <section class="p-6 bg-white dark:bg-gray-700 rounded-lg shadow space-y-4">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 border-b pb-2">Coleta de Amostra</h2>
        <dl class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm text-gray-700 dark:text-gray-300">
            <div>
                <dt class="font-medium">Nº Inicial</dt>
                <dd>{{ $visita->vis_amos_inicial }}</dd>
            </div>
            <div>
                <dt class="font-medium">Nº Final</dt>
                <dd>{{ $visita->vis_amos_final }}</dd>
            </div>
            <div>
                <dt class="font-medium">Tubitos</dt>
                <dd>{{ $visita->vis_qtd_tubitos }}</dd>
            </div>
        </dl>
    </section>
    @endif

    <section class="p-6 bg-white dark:bg-gray-700 rounded-lg shadow space-y-4">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 border-b pb-2">Tratamentos Realizados</h2>

        @if ($visita->tratamentos && count($visita->tratamentos))
            @foreach ($visita->tratamentos as $t)
                <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-md border border-gray-300 dark:border-gray-600 text-sm text-gray-800 dark:text-gray-100 space-y-1">
                    @if (!empty($t->trat_forma))
                        <p><strong>Forma:</strong> {{ $t->trat_forma }}</p>
                    @endif
                    @if (!empty($t->trat_tipo))
                        <p><strong>Tipo:</strong> {{ $t->trat_tipo }}</p>
                    @endif
                    @if (!is_null($t->linha))
                        <p><strong>Linha:</strong> {{ $t->linha }}</p>
                    @endif
                    @if (!is_null($t->qtd_gramas))
                        <p><strong>Gramas:</strong> {{ $t->qtd_gramas }}</p>
                    @endif
                    @if (!is_null($t->qtd_depositos_tratados))
                        <p><strong>Depósitos Tratados:</strong> {{ $t->qtd_depositos_tratados }}</p>
                    @endif
                    @if (!is_null($t->qtd_cargas))
                        <p><strong>Cargas:</strong> {{ $t->qtd_cargas }}</p>
                    @endif
                </div>
            @endforeach
        @else
            <p class="text-sm text-gray-600 dark:text-gray-300 italic">
                Nenhum tratamento foi realizado durante esta visita.
            </p>
        @endif
    </section>
</div>

{{-- Mapa --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const lat = parseFloat("{{ $visita->local->loc_latitude }}") || -28.7;
    const lng = parseFloat("{{ $visita->local->loc_longitude }}") || -52.3;
    const map = L.map('map').setView([lat, lng], 16);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    L.marker([lat, lng]).addTo(map);
});
</script>
@endsection