@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6 space-y-6 max-w-7xl">
    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Relatórios</h1>

    {{-- Filtros Avançados --}}
    <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
        <div>
            <label class="text-sm text-gray-700 dark:text-gray-300">Data Início</label>
            <input type="date" name="data_inicio" value="{{ request('data_inicio') }}" class="w-full rounded p-2 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100" />
        </div>
        <div>
            <label class="text-sm text-gray-700 dark:text-gray-300">Data Fim</label>
            <input type="date" name="data_fim" value="{{ request('data_fim') }}" class="w-full rounded p-2 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100" />
        </div>
        <div>
            <label class="text-sm text-gray-700 dark:text-gray-300">Bairro</label>
            <input type="text" name="bairro" value="{{ request('bairro') }}" placeholder="Ex: Centro" class="w-full rounded p-2 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100" />
        </div>

        <div class="flex flex-col justify-end md:flex-row md:items-end gap-4 col-span-1">
            <div class="flex-1">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded">
                    Filtrar
                </button>
            </div>
            <div class="flex-1">
                <a href="{{ route('gestor.relatorios.index') }}" class="w-full bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded text-center block">
                    Limpar Filtros
                </a>
            </div>
        </div>
    
    </form>

    {{-- Botão Gerar PDF --}}
    <section class="w-full bg-white dark:bg-gray-800 shadow rounded-lg p-4">
        <p class="text-sm text-gray-600 dark:text-gray-300 mb-2">
            Gere um relatório completo em PDF com gráficos, mapa de calor e tabela de visitas realizadas neste período.
        </p>
        <button onclick="gerarBase64Graficos()" class="w-full bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded shadow">
            Gerar Relatório em PDF
        </button>
    </section>

    {{-- Cards Indicadores --}}
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
                    O gráfico de pizza mostra a distribuição percentual das doenças registradas nas visitas.
                </p>
                <canvas id="graficoDoencas" class="w-full" style="height: 240px; max-height: 240px;"></canvas>
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
                    <th class="p-4 text-left font-semibold text-gray-700 dark:text-gray-300">Local</th>
                    <th class="p-4 text-left font-semibold text-gray-700 dark:text-gray-300">Doenças</th>
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
                        <td class="p-4 text-gray-800 dark:text-gray-100 leading-tight">
                            <div class="font-semibold">{{ $visita->local->loc_endereco }}, {{ $visita->local->loc_numero }}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                Bairro: {{ $visita->local->loc_bairro }}<br>
                                Cód: {{ $visita->local->loc_codigo_unico }}
                            </div>
                        </td>
                        <td class="p-4 text-gray-800 dark:text-gray-100">
                            @forelse ($visita->doencas as $doenca)
                                <span class="inline-block bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300 mr-1 mb-1" title="{{ $doenca->doe_nome }}">
                                    {{ \Str::limit($doenca->doe_nome, 18) }}
                                </span>
                            @empty
                                <span class="text-xs text-gray-500 italic">Nenhuma</span>
                            @endforelse
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
        await new Promise(resolve => setTimeout(resolve, 300));

        const canvasBairros = document.getElementById('graficoBairros');
        const canvasDoencas = document.getElementById('graficoDoencas');
        const mapaDiv = document.getElementById('mapa-calor');

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

        addField('graficoBairrosBase64', base64Bairros);
        addField('graficoDoencasBase64', base64Doencas);
        addField('mapaCalorBase64', base64Mapa);

        document.body.appendChild(form);
        form.submit();
    }
</script>
@endsection