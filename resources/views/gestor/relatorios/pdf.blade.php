<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Visitas Epidemiológicas</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #111;
        }
        h1, h2 {
            text-align: center;
            margin: 0 0 8px 0;
        }
        h2 {
            margin-top: 20px;
            font-size: 16px;
        }
        .info {
            margin-bottom: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }
        th, td {
            border: 1px solid #aaa;
            padding: 6px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background-color: #f0f0f0;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #555;
        }
        .grafico, .mapa {
            text-align: center;
            margin-top: 16px;
        }
        .grafico img, .mapa img {
            max-width: 600px;
            max-height: 400px;
        }
    </style>
</head>
<body>

    <h1>Relatório de Visitas Epidemiológicas</h1>
    <p class="info"><strong>Data de emissão:</strong> {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</p>

    {{-- Gráficos --}}
    <h2>Gráficos Epidemiológicos</h2>

    <div class="grafico">
        <p><strong>Distribuição de Doenças</strong></p>
        @if(isset($graficoDoencasBase64))
            <img src="{{ $graficoDoencasBase64 }}" alt="Gráfico de Doenças">
        @else
            <p>Gráfico não disponível.</p>
        @endif
    </div>

    <div class="grafico">
        <p><strong>Visitas por Bairro</strong></p>
        @if(isset($graficoBairrosBase64))
            <img src="{{ $graficoBairrosBase64 }}" alt="Gráfico de Bairros">
        @else
            <p>Gráfico não disponível.</p>
        @endif
    </div>

    {{-- Mapa de Calor --}}
    <h2>Mapa de Calor</h2>
    <div class="mapa">
        @if(isset($mapaCalorBase64))
            <img src="{{ $mapaCalorBase64 }}" alt="Mapa de Calor">
        @else
            <p>Mapa não disponível.</p>
        @endif
    </div>

    {{-- Tabela de Visitas --}}
    <h2>Visitas Registradas</h2>
    <table>
        <thead>
            <tr>
                <th>Código</th>
                <th>Data</th>
                <th>Bairro</th>
                <th>Endereço</th>
                <th>Doenças</th>
                <th>Agente</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($visitas as $visita)
                <tr>
                    <td>#{{ $visita->vis_id }}</td>
                    <td>{{ \Carbon\Carbon::parse($visita->vis_data)->format('d/m/Y') }}</td>
                    <td>{{ $visita->local->loc_bairro ?? '—' }}</td>
                    <td>{{ $visita->local->loc_endereco }}, {{ $visita->local->loc_numero }}</td>
                    <td>
                        @foreach ($visita->doencas as $doenca)
                            {{ $doenca->doe_nome }}@if (!$loop->last), @endif
                        @endforeach
                    </td>
                    <td>{{ $visita->usuario->use_nome ?? '—' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Sistema Visita Aí – Universidade de Passo Fundo – {{ now()->year }}
    </div>

</body>
</html>