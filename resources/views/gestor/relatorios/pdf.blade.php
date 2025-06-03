<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Visita Aí - Relatório Epidemiológico</title>
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
  </style>
</head>
<body>

<!-- Tipos: individual, diario, semanal, completo -->

<header>
  <h1>{{ $titulo }}</h1>
  <h2 style="border-left: none">Município de {{ $visitas->first()->local->loc_cidade ?? 'Município' }} - {{ $visitas->first()->local->loc_estado ?? 'UF' }}</h2>
</header>

<footer>
  <div class="page-number"></div>
  Gerado automaticamente em {{ now()->format('d/m/Y H:i') }} pelo Sistema Visita Aí.
</footer>

<main>
  <p style="margin-bottom: 25px;">
    @if ($tipo === 'individual')
      Data da visita: <strong>{{ \Carbon\Carbon::parse($data_inicio)->format('d/m/Y') }}</strong>
    @elseif ($tipo === 'diario')
      Data do relatório: <strong>{{ \Carbon\Carbon::parse($data_inicio)->format('d/m/Y') }}</strong>
    @else
      Período selecionado: <strong>{{ \Carbon\Carbon::parse($data_inicio)->format('d/m/Y') }}</strong> a
      <strong>{{ \Carbon\Carbon::parse($data_fim)->format('d/m/Y') }}</strong>
    @endif
    <br>
    @if ($tipo !== 'individual')
      Bairro filtrado: <strong>{{ $bairro ?: 'Todos os bairros' }}</strong>
    @endif
  </p>

  <h2>1. Introdução</h2>
  <p>
    Este relatório tem por finalidade apresentar um panorama consolidado das informações epidemiológicas referentes ao município de <strong>{{ $visitas->first()->local->loc_cidade ?? 'Município' }}</strong>, com base nos dados coletados em campo por agentes de endemias e agentes comunitários de saúde. As informações aqui contidas abrangem registros de visitas domiciliares, inspeções em potenciais focos, tratamentos realizados e ocorrências relacionadas a doenças monitoradas no território.
  </p>
  <p>
    A sistematização desses dados tem como objetivo subsidiar a gestão pública na definição de estratégias de intervenção, no direcionamento de recursos e na implementação de medidas preventivas e corretivas. Além disso, visa promover maior transparência das ações desenvolvidas, fortalecendo a vigilância epidemiológica e contribuindo para a melhoria contínua da saúde coletiva no município.
  </p>

  <h2>2. Dados Gerais</h2>
  <p>
    @if ($tipo === 'individual')
      Esta seção apresenta as informações detalhadas de uma visita epidemiológica específica, realizada em <strong>{{ \Carbon\Carbon::parse($data_inicio)->format('d/m/Y') }}</strong>. Os dados contemplam o local inspecionado, o tipo de atividade executada, os depósitos observados e, quando aplicável, os tratamentos realizados.
    @else
      @if ($visitas->count() === 1)
        No período selecionado, foi registrada <strong>1</strong> visita epidemiológica. Essa visita representa uma amostra pontual das ações desenvolvidas no território.
      @else
        No período em análise, foram registradas <strong>{{ $visitas->count() }}</strong> visitas epidemiológicas, abrangendo diferentes bairros e localidades do município. Esses registros fornecem subsídios relevantes para a compreensão do cenário atual e para o direcionamento de políticas públicas de saúde.
      @endif
    @endif
  </p>

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
      @foreach(\App\Models\Doenca::all() as $doenca)
        <tr>
          <td>{{ $doenca->doe_nome }}</td>
          <td>{{ is_array($doenca->doe_sintomas) ? implode(', ', $doenca->doe_sintomas) : $doenca->doe_sintomas }}</td>
          <td>{{ is_array($doenca->doe_transmissao) ? implode(', ', $doenca->doe_transmissao) : $doenca->doe_transmissao }}</td>
          <td>{{ is_array($doenca->doe_medidas_controle) ? implode(', ', $doenca->doe_medidas_controle) : $doenca->doe_medidas_controle }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>

  <h2>4. Visitas Realizadas</h2>
  <table>
  <thead>
    <tr>
      <th>Código</th>
      <th>Data</th>
      <th>Pendência</th>
      <th>Atividade</th>
      <th>Local</th>
      <th>Agente</th>
    </tr>
  </thead>
  <tbody>
    @foreach($visitas as $visita)
      <tr>
        <td>
          <span style="display: inline-block; background-color: #e5e7eb; color: #374151; font-size: 12px; font-weight: 600; padding: 2px 6px; border-radius: 4px; vertical-align: middle;">
            #{{ $visita->vis_id }}
          </span>
        </td>
        <td>
          {{ \Carbon\Carbon::parse($visita->vis_data)->format('d/m/Y') }}<br>
          <i style="text-transform: lowercase;">{{ ucfirst(\Carbon\Carbon::parse($visita->vis_data)->translatedFormat('l')) }}</i>
        </td>
        <td>
          @if($visita->vis_pendencias)
            <span style="display: inline-block; background-color: #fee2e2; color: #991b1b; font-size: 12px; font-weight: 600; padding: 2px 6px; border-radius: 4px; vertical-align: middle;">
              Pendente
            </span>
          @else
            <span style="display: inline-block; background-color: #d1fae5; color: #065f46; font-size: 12px; font-weight: 600; padding: 2px 6px; border-radius: 4px; vertical-align: middle;">
              Concluída
            </span>
          @endif
        </td>
      @php
          $atividades = [
              '1' => 'LI (Levantamento de Índice)',
              '2' => 'LI+T (Levantamento + Tratamento)',
              '3' => 'PPE+T (Ponto Estratégico + Tratamento)',
              '4' => 'T (Tratamento)',
              '5' => 'DF (Delimitação de Foco)',
              '6' => 'PVE (Pesquisa Vetorial Especial)',
              '7' => 'LIRAa (Levantamento de Índice Rápido)',
              '8' => 'PE (Ponto Estratégico)',
          ];
        @endphp
        <td>{{ $atividades[$visita->vis_atividade] ?? 'Não informado' }}</td>
        <td>
          <strong>{{ $visita->local->loc_endereco }}, {{ $visita->local->loc_numero ?: 'S/N' }}</strong><br>
          Bairro: {{ $visita->local->loc_bairro }}<br>
          {{ $visita->local->loc_cidade }}/{{ $visita->local->loc_estado }} - {{ $visita->local->loc_pais }}<br>
          CEP: {{ $visita->local->loc_cep }}<br>
          Complemento: {{ $visita->local->loc_complemento ?: 'N/A' }}<br>
          Código: {{ $visita->local->loc_codigo_unico }} |
          Cod. Localidade: {{ $visita->local->loc_codigo ?? 'N/A' }}<br>
          Categoria: {{ $visita->local->loc_categoria ?? 'N/A' }} |
          Tipo: {{ ['R' => 'Residencial', 'C' => 'Comercial', 'T' => 'Terreno Baldio'][$visita->local->loc_tipo] ?? 'N/A' }}<br>
          Zona: {{ ['U' => 'Urbana', 'R' => 'Rural'][$visita->local->loc_zona] ?? 'N/A' }} |
          Quarteirão: {{ $visita->local->loc_quarteirao ?? 'N/A' }} |
          Seq.: {{ $visita->local->loc_sequencia ?? 'N/A' }} |
          Lado: {{ $visita->local->loc_lado ?? 'N/A' }}
        </td>
        <td>{{ $visita->usuario->use_nome ?? '—' }}</td>
      </tr>

      {{-- Linha adicional: Inspeções e Total de Tratamentos --}}
      <tr>
        <td colspan="6" style="padding: 10px 6px; font-size: 12px; color: #374151; background-color: #f9fafb;">
          <strong>Inspecionados:</strong>
          A1: {{ $visita->insp_a1 ?? 0 }},
          A2: {{ $visita->insp_a2 ?? 0 }},
          B: {{ $visita->insp_b ?? 0 }},
          C: {{ $visita->insp_c ?? 0 }},
          D1: {{ $visita->insp_d1 ?? 0 }},
          D2: {{ $visita->insp_d2 ?? 0 }},
          E: {{ $visita->insp_e ?? 0 }},
          <strong>Total:</strong>
          {{
            ($visita->insp_a1 ?? 0) + ($visita->insp_a2 ?? 0) + ($visita->insp_b ?? 0) +
            ($visita->insp_c ?? 0) + ($visita->insp_d1 ?? 0) + ($visita->insp_d2 ?? 0) + ($visita->insp_e ?? 0)
          }}.
          <strong>Houve algum tratamento?</strong>
          @if(count($visita->tratamentos) > 0)
            Sim
          @else
            Não
          @endif
        </td>
      </tr>

      {{-- Linha adicional: Detalhes dos Tratamentos --}}
      @if (!empty($visita->tratamentos) && count($visita->tratamentos))
        <tr>
          <td colspan="6" style="padding: 10px 6px; font-size: 12px; color: #374151; background-color: #f3f4f6;">
            <strong>Detalhes dos Tratamentos:</strong><br>
            @foreach ($visita->tratamentos as $t)
              • Forma: {{ $t->trat_forma ?? '—' }},
              Tipo: {{ $t->trat_tipo ?? '—' }},
              Depósitos Tratados: {{ $t->qtd_depositos_tratados ?? 0 }},
              Gramas: {{ $t->qtd_gramas ?? 0 }},
              Cargas: {{ $t->qtd_cargas ?? 0 }}<br>
            @endforeach
          </td>
        </tr>
      @endif
    @endforeach
  </tbody>
</table>


  <!-- <h2>5. Análises Visuais</h2>
  @foreach([
    'graficoDoencasBase64' => 'Distribuição das doenças registradas.',
    'graficoBairrosBase64' => 'Número de visitas por bairro.',
    'graficoZonasBase64' => 'Quantidade de visitas por zona (Urbana/Rural).',
    'graficoDiasBase64' => 'Evolução do número de visitas ao longo dos dias.',
    'graficoInspBase64' => 'Total de inspeções por tipo de depósito.',
    'graficoTratamentosBase64' => 'Distribuição das formas de tratamento aplicadas.',
    'mapaCalorBase64' => 'Mapa de calor dos locais com visitas registradas.'
  ] as $grafico => $descricao)
    @if(isset($$grafico) && $$grafico)
      <div class="img-container">
        <img src="{{ $$grafico }}" alt="Gráfico">
        <p class="descricao">{{ $descricao }}</p>
      </div>
    @endif
  @endforeach -->

 <h2>6. Conclusão</h2>
  <p>
    Os dados consolidados neste relatório evidenciam a relevância da vigilância epidemiológica como ferramenta estratégica para o controle e a prevenção de agravos à saúde pública. As informações levantadas durante as visitas permitem identificar áreas críticas, avaliar a efetividade das ações realizadas e orientar a alocação de recursos de forma mais eficiente.
  </p>
  <p>
    Reforça-se, portanto, a necessidade de manutenção de um monitoramento constante, especialmente em regiões com maior recorrência de focos, bem como a promoção de campanhas educativas voltadas à conscientização da população. A integração entre agentes de saúde, gestores e comunidade é fundamental para o fortalecimento das ações de enfrentamento e para a construção de um ambiente mais saudável e resiliente.
  </p>

</main>
</body>
</html>