@extends('layouts.app')

@section('content')
<!-- Overlay de carregamento -->
<div id="overlayCarregando" class="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center z-50 hidden">
    <div class="text-center">
        <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-white mx-auto mb-4"></div>
        <p class="text-white text-lg font-semibold">Gerando relatório, aguarde...</p>
    </div>
</div>

<div class="container mx-auto p-6 space-y-6 max-w-7xl">
    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Relatórios</h1>

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded relative mb-4">
            <strong>Erro:</strong> {{ session('error') }}
        </div>
    @endif

    {{-- Filtros --}}
    <div
    x-data="{
        tipo: '{{ request('tipo_relatorio', 'completo') }}',
        filtrosAplicados: new URLSearchParams(window.location.search).toString() !== '',
        get botaoAtivo() {
        return this.tipo === 'completo' || this.filtrosAplicados;
        }
    }"
    x-init="$watch('tipo', () => filtrosAplicados = false)">

        {{-- Filtros Avançados --}}
        <form method="GET"
            x-ref="formulario"
            @submit.prevent="filtrosAplicado = true; $nextTick(() => $refs.formulario.submit())"
            class="grid grid-cols-1 md:grid-cols-5 gap-4 bg-white dark:bg-gray-800 p-4 rounded-lg shadow">

            {{-- Tipo de Relatório --}}
            <div>
                <label class="text-sm text-gray-700 dark:text-gray-300">Tipo de Relatório</label>
                <select name="tipo_relatorio" x-model="tipo"
                        class="w-full rounded p-2 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    <option value="completo">Completo</option>
                    <option value="diario">Diário</option>
                    <option value="semanal">Semanal</option>
                    <option value="individual">Individual</option>
                </select>
            </div>

            {{-- Data para Diário --}}
            <div x-show="tipo === 'diario'" x-cloak class="md:col-span-2">
                <label class="text-sm text-gray-700 dark:text-gray-300">Data</label>
                <input type="date" name="data_unica" value="{{ request('data_unica') }}"
                    class="w-full rounded p-2 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100" />
            </div>

            {{-- Período para Semanal --}}
            <template x-if="tipo === 'semanal'">
                <div class="md:col-span-2 grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm text-gray-700 dark:text-gray-300">Início da Semana</label>
                        <input type="date" name="data_inicio" value="{{ request('data_inicio') }}"
                            class="w-full rounded p-2 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100" />
                    </div>
                    <div>
                        <label class="text-sm text-gray-700 dark:text-gray-300">Fim da Semana</label>
                        <input type="date" name="data_fim" value="{{ request('data_fim') }}"
                            class="w-full rounded p-2 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100" />
                    </div>
                </div>
            </template>

            {{-- Seleção individual --}}
            <div x-show="tipo === 'individual'" x-cloak class="md:col-span-3">
                <label class="text-sm text-gray-700 dark:text-gray-300">Selecione a Visita</label>
                <select name="visita_id" x-ref="visitaId"
                        class="w-full rounded p-2 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    @foreach($visitas as $v)
                        @php
                            $atividades = [
                                '1' => 'LI',
                                '2' => 'LI+T',
                                '3' => 'PPE+T',
                                '4' => 'T',
                                '5' => 'DF',
                                '6' => 'PVE',
                                '7' => 'LIRAa',
                                '8' => 'PE',
                            ];
                        @endphp
                        <option value="{{ $v->vis_id }}" @selected(request('visita_id') == $v->vis_id)>
                            #{{ $v->vis_id }} - {{ \Carbon\Carbon::parse($v->vis_data)->format('d/m/Y') }} - {{ $atividades[$v->vis_atividade] ?? 'Não informado' }} - {{ $v->local->loc_endereco }}{{ $v->local->loc_numero ? ', ' . $v->local->loc_numero : ' N/A' }} - Bairro/Localidade: {{ $v->local->loc_bairro }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Bairro (apenas para diário, semanal) --}}
            <div x-show="tipo !== 'individual' && tipo !== 'completo'" x-cloak>
                <label class="text-sm text-gray-700 dark:text-gray-300">Bairro</label>
                <input type="text" name="bairro" value="{{ request('bairro') }}"
                    placeholder="Ex: Centro"
                    class="w-full rounded p-2 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100" />
            </div>

            {{-- Bairro (apenas para completo) --}}
            <div x-show="tipo === 'completo'" x-cloak class="md:col-span-3">
                <label class="text-sm text-gray-700 dark:text-gray-300">Bairro</label>
                <input type="text" name="bairro" value="{{ request('bairro') }}"
                    placeholder="Ex: Centro"
                    class="w-full rounded p-2 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100" />
            </div>

            {{-- Botões --}}
            <div class="flex flex-col justify-end md:flex-row md:items-end gap-4 col-span-1">
                <div class="flex-1">
                    <button type="submit"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded">
                        Filtrar
                    </button>
                </div>
                <div class="flex-1">
                    <a href="{{ route('gestor.relatorios.index') }}"
                    class="w-full bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded text-center block">
                        Limpar
                    </a>
                </div>
            </div>
        </form>

        {{-- Botão Gerar PDF --}}
        <section class="w-full bg-white dark:bg-gray-800 shadow rounded-lg p-4 mt-4">
            <p class="text-sm text-gray-600 dark:text-gray-300 mb-2">
                Relatório de Visitas Epidemiológicas
            </p>
            <button
                :disabled="!botaoAtivo"
                @click.prevent="
                    if (!botaoAtivo) {
                        alert('Aplique os filtros antes de gerar o PDF.');
                        return;
                    }
                    gerarBase64Graficos();
                "
                class="w-full bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded shadow disabled:opacity-50 disabled:cursor-not-allowed">
                Gerar Relatório em PDF
            </button>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-4">
                Para gerar o documento com filtros aplicados, selecione os filtros desejados e clique em "Filtrar". Em seguida, clique no botão acima.
            </p>
        </section>
    </div>

    {{-- Cards Indicadores --}}
    <section class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow space-y-4">
    <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200">Indicadores Gerais</h2>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="p-4 bg-gray-50 dark:bg-gray-900 shadow rounded-lg">
            <p class="text-gray-500 dark:text-gray-400 text-sm">Locais Cadastrados</p>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalLocaisCadastrados }}</h2>
        </div>
        <div class="p-4 bg-gray-50 dark:bg-gray-900 shadow rounded-lg">
            <p class="text-gray-500 dark:text-gray-400 text-sm">Bairro com Mais Ocorrências</p>
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">{{ $bairroMaisFrequente ?: 'N/A' }}</h2>
        </div>
        <div class="p-4 bg-gray-50 dark:bg-gray-900  shadow rounded-lg">
            <p class="text-gray-500 dark:text-gray-400 text-sm">Total de Visitas</p>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalVisitas }}</h2>
        </div>
            <div class="p-4 bg-gray-50 dark:bg-gray-900  shadow rounded-lg">
            <p class="text-gray-500 dark:text-gray-400 text-sm">% Visitas com Pendência</p>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $percentualPendencias }}%</h2>
        </div>
        <div class="p-4 bg-gray-50 dark:bg-gray-900 shadow rounded-lg">
            <p class="text-gray-500 dark:text-gray-400 text-sm">Coletas Realizadas</p>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalComColeta }}</h2>
        </div>
        <div class="p-4 bg-gray-50 dark:bg-gray-900 shadow rounded-lg">
            <p class="text-gray-500 dark:text-gray-400 text-sm">Visitas com Tratamento</p>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $visitasComTratamento }}</h2>
        </div>
        <div class="p-4 bg-gray-50 dark:bg-gray-900 shadow rounded-lg">
            <p class="text-gray-500 dark:text-gray-400 text-sm">Média de Tratamentos</p>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($mediaTratamentosPorVisita, 1, ',', '.') }}</h2>
        </div>
        <div class="p-4 bg-gray-50 dark:bg-gray-900 shadow rounded-lg">
            <p class="text-gray-500 dark:text-gray-400 text-sm">Depósitos Eliminados</p>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalDepEliminados }}</h2>
        </div>
    </div>
    </section>

    {{-- Gráficos Epidemiológicos --}}
    <section class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
        <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200 mb-6">Gráficos Epidemiológicos</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="p-4 bg-gray-50 dark:bg-gray-900 rounded-lg shadow">
                <h3 class="text-md font-semibold text-gray-700 dark:text-gray-300 mb-2">Visitas por Bairro</h3>
                <p class="mt-2 mb-2 text-sm text-gray-600 dark:text-gray-400">
                    Este gráfico mostra a distribuição de visitas realizadas pelos agentes epidemiológicos em cada bairro.
                </p>
                <canvas id="graficoBairros" class="w-full" style="height: 240px; max-height: 240px;"></canvas>
            </div>
            <div class="p-4 bg-gray-50 dark:bg-gray-900 rounded-lg shadow">
                <h3 class="text-md font-semibold text-gray-700 dark:text-gray-300 mb-2">Distribuição de Doenças</h3>
                <p class="mt-2 mb-2 text-sm text-gray-600 dark:text-gray-400">
                    O gráfico de pizza mostra a proporção de cada doença registrada nas visitas epidemiológicas.
                </p>
                <canvas id="graficoDoencas" class="w-full" style="height: 240px; max-height: 240px;"></canvas>
            </div>
            <div class="p-4 bg-gray-50 dark:bg-gray-900 rounded-lg shadow">
                <h3 class="text-md font-semibold text-gray-700 dark:text-gray-300 mb-2">Visitas por Zona</h3>
                <p class="mt-2 mb-2 text-sm text-gray-600 dark:text-gray-400">
                    Este gráfico de barras mostra a quantidade de visitas realizadas em cada zona (Urbana/Rural).
                </p>
                <canvas id="graficoZonas" class="w-full h-60" style="height: 240px; max-height: 240px;"></canvas>
            </div>
            <div class="p-4 bg-gray-50 dark:bg-gray-900 rounded-lg shadow">
                <h3 class="text-md font-semibold text-gray-700 dark:text-gray-300 mb-2">Evolução de Visitas por Dia</h3>
                <p class="mt-2 mb-2 text-sm text-gray-600 dark:text-gray-400">
                    Este gráfico de linha mostra a evolução do número de visitas realizadas ao longo dos dias.
                </p>
                <canvas id="graficoDias" class="w-full h-60" style="height: 240px; max-height: 240px;"></canvas>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 col-span-2">
                <div class="p-4 bg-gray-50 dark:bg-gray-900 rounded-lg shadow">
                    <h3 class="text-md font-semibold text-gray-700 dark:text-gray-300 mb-2">Inspeções Realizadas</h3>
                    <p class="mt-2 mb-2 text-sm text-gray-600 dark:text-gray-400">
                        Este gráfico radar mostra a quantidade de inspeções realizadas em diferentes tipos de depósitos.
                    </p>
                    <canvas id="graficoInsp" class="w-full h-60" style="height: 240px; max-height: 240px;"></canvas>
                </div>
                <div class="p-4 bg-gray-50 dark:bg-gray-900 rounded-lg shadow">
                    <h3 class="text-md font-semibold text-gray-700 dark:text-gray-300 mb-2">Formas de Tratamento</h3>
                    <p class="mt-2 mb-2 text-sm text-gray-600 dark:text-gray-400">
                        Este gráfico de pizza mostra a distribuição das diferentes formas de tratamento aplicadas nas visitas.
                    </p>
                    <canvas id="graficoTratamentos" class="w-full h-60" style="height: 240px; max-height: 240px;"></canvas>
                </div>
            </div>
        </div>
    </section>

    {{-- Mapa de Calor por Local --}}
    <section class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
        <h2 class="text-lg font-bold text-gray-800 dark:text-gray-200 mb-4">Mapa de Calor por Local</h2>
        <div id="mapa-calor" class="w-full h-96 rounded-lg border"></div>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
            O mapa de calor indica a concentração de visitas realizadas, com cores mais quentes representando maior número de ocorrências em determinadas regiões.
        </p>
    </section>

    {{-- Tabela de Visitas --}}
<section class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow">
    <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Visitas Registradas</h2>

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white dark:bg-gray-800 rounded-lg shadow text-sm">
            <thead class="bg-gray-100 dark:bg-gray-700">
                <tr>
                    <th class="p-4 text-left font-semibold text-gray-700 dark:text-gray-300">Código</th>
                    <th class="p-4 text-left font-semibold text-gray-700 dark:text-gray-300">Data</th>
                    <th class="p-4 text-left font-semibold text-gray-700 dark:text-gray-300">Pendência</th>
                    <th class="p-4 text-left font-semibold text-gray-700 dark:text-gray-300">Local</th>
                    <th class="p-4 text-left font-semibold text-gray-700 dark:text-gray-300">Atividade</th>
                    <th class="p-4 text-left font-semibold text-gray-700 dark:text-gray-300">Agente</th>
                    <th class="p-4 text-center font-semibold text-gray-700 dark:text-gray-300">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($visitas as $visita)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-600">
                        <td class="p-4">
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
                        @php
                            $atividades = [
                                '1' => 'LI',
                                '2' => 'LI+T',
                                '3' => 'PPE+T',
                                '4' => 'T',
                                '5' => 'DF',
                                '6' => 'PVE',
                                '7' => 'LIRAa',
                                '8' => 'PE',
                            ];
                        @endphp

                        <td class="p-4 text-gray-800 dark:text-gray-100">
                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                {{ $atividades[$visita->vis_atividade] ?? 'Não informado' }}
                            </div>
                        </td>
                        <td class="p-4 text-gray-800 dark:text-gray-100 leading-tight">
                            <div class="font-semibold">{{ $visita->local->loc_endereco }}, {{ $visita->local->loc_numero }}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                Bairro/Localidade: {{ $visita->local->loc_bairro }}<br>
                                Cód.: {{ $visita->local->loc_codigo_unico }}
                            </div>
                        </td>
                        <td class="p-4 text-gray-800 dark:text-gray-100 whitespace-nowrap">
                            {{ $visita->usuario->use_nome ?? '—' }}
                        </td>
                        <td class="p-4 text-center">
                            <a href="{{ route('gestor.visitas.show', $visita) }}"
                               class="inline-flex items-center px-3 py-1.5 text-xs font-semibold bg-blue-600 hover:bg-blue-700 text-white rounded shadow transition">
                                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" stroke-width="2"
                                     viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path d="M2.458 12C3.732 7.943 7.523 5 12 5s8.268 2.943 9.542 7-3.732 7-9.542 7-8.268-2.943-9.542-7z"/>
                                </svg>
                                Visualizar
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-6 text-center text-gray-600 dark:text-gray-400 italic">Nenhuma visita encontrada.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>

</div>

{{-- Scripts --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.heat/dist/leaflet-heat.js"></script>
<script src="https://unpkg.com/leaflet-image/leaflet-image.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<script>
    const visitas = JSON.parse('{!! addslashes(json_encode($visitas)) !!}');

    const contagemPorBairro = {};
    const contagemPorDoenca = {};

    visitas.forEach(v => {
        const bairro = v.local?.loc_bairro ?? 'Desconhecido';
        contagemPorBairro[bairro] = (contagemPorBairro[bairro] || 0) + 1;

        (v.doencas || []).forEach(d => {
            contagemPorDoenca[d.doe_nome] = (contagemPorDoenca[d.doe_nome] || 0) + 1;
        });
    });

    new Chart(document.getElementById('graficoBairros'), {
        type: 'bar',
        data: {
            labels: Object.keys(contagemPorBairro),
            datasets: [{
                label: 'Visitas por Bairro',
                data: Object.values(contagemPorBairro),
                backgroundColor: 'rgba(59, 130, 246, 0.6)',
                borderColor: 'rgba(59, 130, 246, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                title: { display: true, text: 'Visitas por Bairro' }
            }
        }
    });

    new Chart(document.getElementById('graficoDoencas'), {
    type: 'pie',
    data: {
        labels: Object.keys(contagemPorDoenca),
        datasets: [{
            data: Object.values(contagemPorDoenca),
            backgroundColor: ['#60a5fa', '#f87171', '#34d399', '#fbbf24', '#a78bfa', '#fb7185', '#38bdf8', '#facc15', '#4ade80', '#f472b6']
        }]
    },
    options: {
        responsive: true,
        plugins: {
            title: {
                display: true,
                text: 'Distribuição de Doenças'
            },
            legend: {
                position: 'bottom',
                labels: {
                    color: '#ddd' // adapta à cor do tema dark
                }
            },
            datalabels: {
                formatter: (value, context) => {
                    const total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                    const percentage = ((value / total) * 100).toFixed(1);
                    return `${percentage}%`;
                },
                color: '#fff',
                font: {
                    weight: 'bold'
                }
            }
        }
    },
    plugins: [ChartDataLabels]
    });

    // Mapa de calor
    const pontos = visitas
        .map(v => {
            const lat = parseFloat(v?.local?.loc_latitude || 0);
            const lng = parseFloat(v?.local?.loc_longitude || 0);
            return lat !== 0 && lng !== 0 ? [lat, lng, 1] : null;
        })
        .filter(p => p !== null);

    // Cálculo do centro (média dos pontos válidos)
    let centroLat = -28.65, centroLng = -52.42; // fallback
    if (pontos.length > 0) {
        const total = pontos.length;
        centroLat = pontos.reduce((sum, p) => sum + p[0], 0) / total;
        centroLng = pontos.reduce((sum, p) => sum + p[1], 0) / total;
    }

    // Inicialização do mapa centralizado no centro calculado
    const mapa = L.map('mapa-calor').setView([centroLat, centroLng], 14);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors',
        maxZoom: 18,
    }).addTo(mapa);

    L.heatLayer(pontos, {
        radius: 25,
        blur: 15,
        maxZoom: 17,
        gradient: {
            0.2: 'blue',
            0.4: 'lime',
            0.6: 'yellow',
            0.8: 'orange',
            1.0: 'red'
        }
    }).addTo(mapa);

    // Função para gerar o PDF
    async function gerarBase64Graficos(callback) {
        // Mostrar overlay
        document.getElementById('overlayCarregando').classList.remove('hidden');

        await new Promise(resolve => setTimeout(resolve, 300));

        const canvasBairros = document.getElementById('graficoBairros');
        const canvasDoencas = document.getElementById('graficoDoencas');
        const mapaDiv = document.getElementById('mapa-calor');
        const canvasZonas = document.getElementById('graficoZonas');
        const canvasDias = document.getElementById('graficoDias');
        const canvasInsp = document.getElementById('graficoInsp');
        const canvasTratamentos = document.getElementById('graficoTratamentos');

        const base64Bairros = canvasBairros.toDataURL('image/png');
        const base64Doencas = canvasDoencas.toDataURL('image/png');
        const base64Mapa = await new Promise(resolve => {
            leafletImage(mapa, function(err, canvas) {
                if (err || !canvas) {
                    console.error("Erro ao capturar o mapa:", err);
                    return resolve("");
                }
                resolve(canvas.toDataURL("image/png"));
            });
        });

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("gestor.relatorios.pdf") }}';
        form.target = '_blank';

        const token = document.createElement('input');
        token.type = 'hidden';
        token.name = '_token';
        token.value = '{{ csrf_token() }}';
        form.appendChild(token);

        const addField = (name, value) => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = name;
            input.value = value;
            form.appendChild(input);
        }

        // Filtros do formulário
        const dataInicio = document.querySelector('[name="data_inicio"]')?.value || '';
        const dataFim = document.querySelector('[name="data_fim"]')?.value || '';
        const bairro = document.querySelector('[name="bairro"]')?.value || '';
        const tipoRelatorio = document.querySelector('[name="tipo_relatorio"]')?.value || 'completo';
        const dataUnica = document.querySelector('[name="data_unica"]')?.value || '';
        
        addField('data_unica', dataUnica);
        addField('tipo_relatorio', tipoRelatorio);
        addField('data_inicio', dataInicio);
        addField('data_fim', dataFim);
        addField('bairro', bairro);

        if (tipoRelatorio === 'individual') {
            const visitaId = document.querySelector('[name="visita_id"]')?.value || '';
            addField('visita_id', visitaId);
        }

        // Imagens em base64
        addField('graficoBairrosBase64', base64Bairros);
        addField('graficoDoencasBase64', base64Doencas);
        addField('mapaCalorBase64', base64Mapa);
        addField('graficoZonasBase64', canvasZonas.toDataURL('image/png'));
        addField('graficoDiasBase64', canvasDias.toDataURL('image/png'));
        addField('graficoInspBase64', canvasInsp.toDataURL('image/png'));
        addField('graficoTratamentosBase64', canvasTratamentos.toDataURL('image/png'));

        document.body.appendChild(form);
        form.submit();

        // Recarrega a página após o envio
        setTimeout(() => {
            document.getElementById('overlayCarregando').classList.add('hidden');
            location.reload();
        }, 1000);
    }

    // Gráficos de visitas por zona
    const contagemPorZona = {};

    visitas.forEach(v => {
        let zona = v.local?.loc_zona ?? 'Indefinida';
        if (zona === 'U') zona = 'Urbana';
        else if (zona === 'R') zona = 'Rural';
        contagemPorZona[zona] = (contagemPorZona[zona] || 0) + 1;
    });    

    new Chart(document.getElementById('graficoZonas'), {
        type: 'bar',
        data: {
            labels: Object.keys(contagemPorZona),
            datasets: [{
                label: 'Visitas por Zona',
                data: Object.values(contagemPorZona),
                backgroundColor: ['#60a5fa', '#34d399'],
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                title: { display: true, text: 'Visitas por Zona' }
            }
        }
    });

    // Gráfico de evolução diária
    const contagemPorData = {};

    visitas.forEach(v => {
        const data = v.vis_data;
        contagemPorData[data] = (contagemPorData[data] || 0) + 1;
    });

    const datasOrdenadas = Object.keys(contagemPorData).sort();

    new Chart(document.getElementById('graficoDias'), {
        type: 'line',
        data: {
            labels: datasOrdenadas,
            datasets: [{
                label: 'Visitas por Dia',
                data: datasOrdenadas.map(d => contagemPorData[d]),
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.2)',
                tension: 0.3,
                fill: true,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: { display: true, text: 'Visitas por Dia' }
            },
            scales: {
                x: { ticks: { color: '#ccc' } },
                y: { beginAtZero: true, ticks: { color: '#ccc' } }
            }
        }
    });

    // Gráfico de inspeções
    const camposInsp = ['insp_a1', 'insp_a2', 'insp_b', 'insp_c', 'insp_d1', 'insp_d2', 'insp_e'];
    const somatorioInsp = camposInsp.map(campo => visitas.reduce((total, v) => total + (v[campo] || 0), 0));

    new Chart(document.getElementById('graficoInsp'), {
        type: 'radar',
        data: {
            labels: camposInsp.map(c => c.toUpperCase()),
            datasets: [{
                label: 'Total Inspeções',
                data: somatorioInsp,
                fill: true,
                backgroundColor: 'rgba(34,197,94,0.2)',
                borderColor: '#22c55e',
                pointBackgroundColor: '#22c55e'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: { display: true, text: 'Tipos de Depósitos Inspecionados' }
            },
            scales: {
                r: {
                    ticks: { color: '#ccc' },
                    pointLabels: { color: '#ccc' }
                }
            }
        }
    });

    // Gráfico de formas de tratamento
    const contagemTratamentoForma = {};

    visitas.forEach(v => {
        (v.tratamentos || []).forEach(t => {
            const forma = t.trat_forma ?? 'Indefinida';
            contagemTratamentoForma[forma] = (contagemTratamentoForma[forma] || 0) + 1;
        });
    });

    new Chart(document.getElementById('graficoTratamentos'), {
        type: 'pie',
        data: {
            labels: Object.keys(contagemTratamentoForma),
            datasets: [{
                data: Object.values(contagemTratamentoForma),
                backgroundColor: ['#3b82f6', '#10b981', '#facc15', '#f87171', '#a78bfa']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: { display: true, text: 'Distribuição de Formas de Tratamento' },
                legend: {
                    position: 'bottom',
                    labels: { color: '#ddd' }
                }
            }
        }
    });

</script>
@endsection