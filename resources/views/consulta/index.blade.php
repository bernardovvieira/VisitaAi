@extends('layouts.app')

@section('public')
@endsection

@section('content')
<div class="container mx-auto p-6 space-y-6 max-w-7xl">
    <div class="mb-4">
        <a href="{{ url('/') }}"
        class="inline-flex items-center gap-2 text-sm text-blue-600 dark:text-blue-400 hover:underline font-medium transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
            Voltar para Início
        </a>
    </div>

    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Consulta Pública</h1>

    {{-- Filtro por matrícula --}}
    <form action="{{ route('consulta.matricula') }}" method="GET" class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Consultar por Matrícula do Imóvel</label>
        <div class="flex flex-col md:flex-row gap-4">
            <input type="text" name="matricula" placeholder="Ex: 123456" required
                   class="w-full rounded p-2 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100" />
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded">
                Consultar
            </button>
        </div>
    </form>

    @if (session('erro'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mt-4" role="alert">
            <strong class="font-bold">Erro:</strong>
            <span class="block sm:inline">{{ session('erro') }}</span>
        </div>
    @endif

    {{-- Indicadores gerais do município --}}
    <section class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="p-4 bg-white dark:bg-gray-800 shadow rounded-lg">
            <p class="text-gray-500 dark:text-gray-400 text-sm">Total de Visitas</p>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalVisitas }}</h2>
        </div>
        <div class="p-4 bg-white dark:bg-gray-800 shadow rounded-lg">
            <p class="text-gray-500 dark:text-gray-400 text-sm">Locais com Foco</p>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $locaisComFoco }}</h2>
        </div>
        <div class="p-4 bg-white dark:bg-gray-800 shadow rounded-lg">
            <p class="text-gray-500 dark:text-gray-400 text-sm">Doença Mais Recorrente</p>
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">{{ $doencaMaisRecorrente ?: 'N/A' }}</h2>
        </div>
        <div class="p-4 bg-white dark:bg-gray-800 shadow rounded-lg">
            <p class="text-gray-500 dark:text-gray-400 text-sm">Bairro com Mais Ocorrências</p>
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">{{ $bairroMaisFrequente ?: 'N/A' }}</h2>
        </div>
    </section>

    {{-- Gráficos --}}
    <section class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
        <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200 mb-6">Distribuições Epidemiológicas</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="p-4 bg-gray-50 dark:bg-gray-900 rounded-lg shadow">
                <h3 class="text-md font-semibold text-gray-700 dark:text-gray-300 mb-2">Visitas por Bairro</h3>
                <p class="mt-2 mb-2 text-sm text-gray-600 dark:text-gray-400">
                    Este gráfico mostra a distribuição de visitas realizadas pelos agentes epidemiológicos em cada bairro.
                </p>
                <canvas id="graficoPublicoBairros" class="w-full" style="height: 240px; max-height: 240px;"></canvas>
            </div>
            <div class="p-4 bg-gray-50 dark:bg-gray-900 rounded-lg shadow">
                <h3 class="text-md font-semibold text-gray-700 dark:text-gray-300 mb-2">Distribuição de Doenças</h3>
                <p class="mt-2 mb-2 text-sm text-gray-600 dark:text-gray-400">
                    O gráfico de pizza mostra a proporção de cada doença registrada nas visitas epidemiológicas.
                </p>
                <canvas id="graficoPublicoDoencas" class="w-full" style="height: 240px; max-height: 240px;"></canvas>
            </div>
        </div>
    </section>

    {{-- Mapa --}}
    <section class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
        <h2 class="text-lg font-bold text-gray-800 dark:text-gray-200 mb-4">Mapa de Calor por Local</h2>
        <div id="mapa-publico" class="w-full h-96 rounded-lg border"></div>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
            O mapa de calor indica a concentração de visitas realizadas, com cores mais quentes representando maior número de ocorrências em determinadas regiões da cidade.
        </p>
    </section>

    {{-- Tabela de Doenças Monitoradas --}}
    <section class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow space-y-4">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Doenças Monitoradas no Município</h2>
        <p class="text-sm text-gray-600 dark:text-gray-400">
            Abaixo estão listadas todas as doenças cadastradas no sistema municipal de monitoramento, com informações sobre sintomas, formas de transmissão e medidas de controle.
        </p>

        <div class="overflow-x-auto rounded-lg">
            <table class="min-w-full text-sm divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-semibold">
                    <tr>
                        <th class="px-4 py-3 text-left">Doença</th>
                        <th class="px-4 py-3 text-left">Sintomas</th>
                        <th class="px-4 py-3 text-left">Transmissão</th>
                        <th class="px-4 py-3 text-left">Medidas de Controle</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700 text-gray-800 dark:text-gray-100">
                    @foreach($doencas as $doenca)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-600 transition">
                            <td class="px-4 py-3 font-medium">{{ $doenca->doe_nome }}</td>
                            <td class="px-4 py-3">
                                {{ is_array($doenca->doe_sintomas) ? implode(', ', $doenca->doe_sintomas) : $doenca->doe_sintomas }}
                            </td>
                            <td class="px-4 py-3">
                                {{ is_array($doenca->doe_transmissao) ? implode(', ', $doenca->doe_transmissao) : $doenca->doe_transmissao }}
                            </td>
                            <td class="px-4 py-3">
                                {{ is_array($doenca->doe_medidas_controle) ? implode(', ', $doenca->doe_medidas_controle) : $doenca->doe_medidas_controle }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
</div>

{{-- Scripts --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.heat/dist/leaflet-heat.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<script>
    const visitasPublicas = JSON.parse('{!! addslashes(json_encode($visitas)) !!}');

    const bairros = {};
    const doencas = {};
    const pontos = [];

    visitasPublicas.forEach(v => {
        const bairro = v.local?.loc_bairro ?? 'Desconhecido';
        bairros[bairro] = (bairros[bairro] || 0) + 1;

        (v.doencas || []).forEach(d => {
            doencas[d.doe_nome] = (doencas[d.doe_nome] || 0) + 1;
        });

        const lat = parseFloat(v.local?.loc_latitude || 0);
        const lng = parseFloat(v.local?.loc_longitude || 0);
        if (lat !== 0 && lng !== 0) pontos.push([lat, lng, 1]);
    });

    new Chart(document.getElementById('graficoPublicoBairros'), {
        type: 'bar',
        data: {
            label: 'Visitas por Bairro',
            labels: Object.keys(bairros),
            datasets: [{
                label: 'Visitas',
                data: Object.values(bairros),
                backgroundColor: 'rgba(59,130,246,0.6)'
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

    new Chart(document.getElementById('graficoPublicoDoencas'), {
        type: 'pie',
        data: {
            labels: Object.keys(doencas),
            datasets: [{
                data: Object.values(doencas),
                backgroundColor: ['#60a5fa','#f87171','#34d399','#fbbf24','#a78bfa']
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
                    labels: { color: '#ddd' }
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

    let centroLat = -28.65, centroLng = -52.42;
    if (pontos.length > 0) {
        centroLat = pontos.reduce((sum, p) => sum + p[0], 0) / pontos.length;
        centroLng = pontos.reduce((sum, p) => sum + p[1], 0) / pontos.length;
    }

    const mapa = L.map('mapa-publico').setView([centroLat, centroLng], 14);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(mapa);
    L.heatLayer(pontos, {
        radius: 25,
        blur: 15
    }).addTo(mapa);
</script>
@endsection