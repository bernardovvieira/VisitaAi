<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Relatório Epidemiológico - Visita Aí</title>
  <style>
    @page {
      margin: 120px 50px 100px 50px;
    }

    body {
      font-family: 'Arial', sans-serif;
      font-size: 12px;
      color: #222;
      line-height: 1.6;
      position: relative;
    }

    header {
      position: fixed;
      top: -100px;
      left: 0;
      right: 0;
      text-align: center;
      padding-bottom: 8px;
      border-bottom: 2px solid #0e4a76;
    }

    header h1 {
      font-size: 18px;
      margin: 0;
      color: #0e4a76;
      text-transform: uppercase;
    }

    header h2 {
      font-size: 13px;
      margin-top: 4px;
      font-weight: normal;
    }

    footer {
      position: fixed;
      bottom: -60px;
      left: 0;
      right: 0;
      text-align: center;
      font-size: 10px;
      color: #555;
      border-top: 1px solid #ccc;
      padding-top: 6px;
    }

    .page-number:after {
      content: "Página " counter(page);
    }

    h2 {
      font-size: 14px;
      text-transform: uppercase;
      border-left: 5px solid #0e4a76;
      padding-left: 10px;
      margin-top: 40px;
      margin-bottom: 10px;
      color: #0e4a76;
    }

    h3 {
      font-size: 12.5px;
      margin-top: 30px;
      color: #333;
    }

    p {
      text-align: justify;
      margin: 10px 0;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin: 20px 0;
      font-size: 11.5px;
    }

    th, td {
      border: 1px solid #999;
      padding: 6px 8px;
    }

    th {
      background-color: #e9f2fa;
      text-align: center;
      font-weight: bold;
    }

    .img-container {
      text-align: center;
      margin: 40px 0 10px;
    }

    .img-container img {
      max-width: 65%;
      border: 1px solid #ccc;
    }

    .descricao {
      font-size: 10px;
      text-align: center;
      color: #666;
      margin-top: 6px;
    }

    .assinatura {
      margin-top: 80px;
      text-align: center;
    }

    .assinatura p {
      margin-top: 60px;
      width: 300px;
      border-top: 1px solid #333;
      margin-left: auto;
      margin-right: auto;
      padding-top: 6px;
      font-size: 11px;
    }

    .marca-dagua {
      position: fixed;
      top: 200px;
      left: 0;
      right: 0;
      opacity: 0.05;
      z-index: 0;
      text-align: center;
    }

    .marca-dagua img {
      width: 350px;
    }
  </style>
</head>
<body>

  <!-- Cabeçalho -->
  <header>
    <h1>Relatório Epidemiológico</h1>
    <h2 style="border-left: none">Município de {{ $visitas->first()->local->loc_cidade ?? 'Município' }} - {{ $visitas->first()->local->loc_estado ?? 'UF' }}</h2>
  </header>

  <!-- Rodapé -->
  <footer>
    <div class="page-number"></div>
    Gerado automaticamente em {{ now()->format('d/m/Y H:i') }} pelo Sistema Visita Aí.
  </footer>

  <main>
    <p style="margin-bottom: 25px;">
        Período selecionado: <strong>{{ \Carbon\Carbon::parse($data_inicio)->format('d/m/Y') }}</strong> a
        <strong>{{ \Carbon\Carbon::parse($data_fim)->format('d/m/Y') }}</strong><br>
        Bairro filtrado: <strong>{{ $bairro ?: 'Todos os bairros' }}</strong>
    </p>

    <!-- Introdução -->
    <h2>1. Introdução</h2>
    <p>
      Este documento apresenta os dados epidemiológicos do município de <strong>{{ $visitas->first()->local->loc_cidade ?? 'Município' }}</strong>, consolidando informações coletadas em visitas técnicas realizadas por agentes de saúde. Seu objetivo é auxiliar o planejamento estratégico e ações preventivas.
    </p>

    <!-- Dados principais -->
    <h2>2. Dados Gerais</h2>
    <p>
      Foram registradas <strong>{{ $visitas->count() }}</strong> visitas únicas. A doença mais frequente foi <strong>{{ $doencaMaisFrequente['nome'] ?? '—' }}</strong>, com <strong>{{ $doencaMaisFrequente['quantidade'] ?? 0 }}</strong> ocorrência{{ ($doencaMaisFrequente['quantidade'] ?? 0) !== 1 ? 's' : '' }}.
    </p>

    <!-- Doenças -->
    <h2>3. Doenças Monitoradas</h2>
    <table>
      <thead>
        <tr>
          <th>Doença</th>
          <th>Sintomas</th>
          <th>Transmissão</th>
          <th>Medidas de Controle</th>
        </tr>
      </thead>
      <tbody>
        @foreach($doencasDetectadas as $doenca)
        <tr>
          <td>{{ $doenca->doe_nome }}</td>
          <td>{{ is_array($doenca->doe_sintomas) ? implode(', ', $doenca->doe_sintomas) : $doenca->doe_sintomas }}</td>
          <td>{{ is_array($doenca->doe_transmissao) ? implode(', ', $doenca->doe_transmissao) : $doenca->doe_transmissao }}</td>
          <td>{{ is_array($doenca->doe_medidas_controle) ? implode(', ', $doenca->doe_medidas_controle) : $doenca->doe_medidas_controle }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>

    <!-- Visitas -->
    <h2>4. Visitas Realizadas</h2>
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Data</th>
          <th>Endereço</th>
          <th>Bairro</th>
          <th>Doenças</th>
          <th>Agente</th>
        </tr>
      </thead>
      <tbody>
        @foreach($visitas as $visita)
        <tr>
          <td>{{ $visita->vis_id }}</td>
          <td>{{ \Carbon\Carbon::parse($visita->vis_data)->translatedFormat('d/m/Y (l)') }}</td>
          <td>{{ $visita->local->loc_endereco }}, {{ $visita->local->loc_numero }}</td>
          <td>{{ $visita->local->loc_bairro }}</td>
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

    <!-- Gráficos -->
    <h2>5. Análises Visuais</h2>

    <div class="img-container">
      @if($graficoDoencasBase64)
        <img src="{{ $graficoDoencasBase64 }}" alt="Gráfico de Doenças">
        <p class="descricao">Distribuição das doenças registradas.</p>
      @endif
    </div>

    <div class="img-container">
      @if($graficoBairrosBase64)
        <img src="{{ $graficoBairrosBase64 }}" alt="Gráfico por Bairros">
        <p class="descricao">Número de visitas por bairro.</p>
      @endif
    </div>

    <div class="img-container">
      @if($mapaCalorBase64)
        <img src="{{ $mapaCalorBase64 }}" alt="Mapa de Calor">
        <p class="descricao">Áreas com maior concentração de focos.</p>
      @endif
    </div>

    <!-- Conclusão -->
    <h2>6. Conclusão</h2>
    <p>
      Reforça-se a importância da vigilância contínua nas áreas com maior incidência e a adoção de campanhas educativas. Os dados aqui apresentados subsidiam ações de prevenção e resposta imediata a surtos no município.
    </p>
  </main>
</body>
</html>