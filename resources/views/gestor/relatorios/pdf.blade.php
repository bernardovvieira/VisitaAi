<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>PNCD — Registro do Serviço Antivetorial</title>
  <style>
    @page { size: A4 landscape; margin: 88px 30px 58px 30px; }
    body { font-family: Arial, sans-serif; font-size: 10px; color: #222; line-height: 1.35; }
    header {
      position: fixed;
      top: -78px;
      left: 0;
      right: 0;
      text-align: center;
      padding: 4px 0 8px 0;
      border-bottom: 2px solid #0e4a76;
    }
    header h1 { font-size: 14px; margin: 0; color: #0e4a76; text-transform: uppercase; line-height: 1.2; }
    header h2 { font-size: 11px; margin: 2px 0 0 0; font-weight: normal; color: #333; }
    footer {
      position: fixed;
      bottom: -48px;
      left: 0;
      right: 0;
      text-align: center;
      font-size: 9px;
      color: #555;
      border-top: 1px solid #ccc;
      padding-top: 4px;
    }
    .page-number:after { content: "Página " counter(page); }
    main { padding-top: 5px; }
    .bloco-ident {
      border: 1px solid #999;
      padding: 8px 10px;
      margin-bottom: 12px;
      background: #f8fafc;
    }
    .bloco-ident table { width: 100%; border: none; margin: 0; font-size: 10px; }
    .bloco-ident td { border: none; padding: 3px 12px 3px 0; vertical-align: top; }
    .bloco-ident .label { font-weight: bold; color: #0e4a76; }
    .bloco-ident .label::after { content: '\00a0'; }
    .tabela-principal {
      width: 100%;
      max-width: 100%;
      border-collapse: collapse;
      margin: 10px 0;
      font-size: 7.5px;
      table-layout: fixed;
    }
    .tabela-principal th, .tabela-principal td {
      border: 1px solid #666;
      padding: 3px 4px;
      text-align: center;
      vertical-align: middle;
      word-wrap: break-word;
      overflow-wrap: break-word;
      white-space: normal;
    }
    .tabela-principal td.text-left, .tabela-principal th.text-left { word-wrap: break-word; overflow-wrap: break-word; text-align: left; }
    .tabela-principal th { background: #0e4a76; color: #fff; font-weight: bold; }
    .tabela-principal td.text-left { text-align: left; }
    .tabela-principal tr:nth-child(even) { background: #f9fafb; }
    .legenda {
      margin-top: 14px;
      padding: 8px 10px;
      border: 1px solid #ccc;
      font-size: 9px;
      background: #fafafa;
    }
    .legenda strong { color: #0e4a76; }
    .titulo-secao { font-size: 11px; font-weight: bold; color: #0e4a76; margin: 12px 0 6px 0; }
  </style>
</head>
<body>

@php
  $primeiroLocal = $visitas->first()?->local;
  $mun = $primeiroLocal?->loc_cidade ?? 'Município';
  $uf = $primeiroLocal?->loc_estado ?? 'UF';
  $atividades = [
    '1' => '1-LI',
    '2' => '2-LI+T',
    '3' => '3-PPE+T',
    '4' => '4-T',
    '5' => '5-DF',
    '6' => '6-PVE',
    '7' => '7-LIRAa',
    '8' => '8-PE',
  ];
  $tipoImovel = ['R' => 'R', 'C' => 'C', 'T' => 'TB'];
@endphp

<header>
  <h1>PNCD — Programa Nacional de Controle</h1>
  <h2>Registro do Serviço Antivetorial — Município de {{ $mun }} / {{ $uf }}</h2>
</header>

<footer>
  <div class="page-number"></div>
  Documento gerado pelo Sistema Visita Aí · Bitwise Technologies em {{ now()->format('d/m/Y H:i') }}.
</footer>

<main>
  <div class="bloco-ident">
    <table>
      <tr>
        <td><span class="label">Município:</span>{{ $mun }} / {{ $uf }}</td>
        <td><span class="label">Data do relatório:</span>{{ \Carbon\Carbon::parse($data_inicio)->format('d/m/Y') }}@if($data_inicio != $data_fim) a {{ \Carbon\Carbon::parse($data_fim)->format('d/m/Y') }}@endif</td>
      </tr>
      @if(!empty($bairrosPdf))
      <tr>
        <td colspan="2"><span class="label">Bairro(s) filtrado(s):</span>{{ is_array($bairrosPdf) ? implode(', ', $bairrosPdf) : $bairrosPdf }}</td>
      </tr>
      @endif
      <tr>
        <td><span class="label">Total de visitas no período:</span>{{ $visitas->count() }}</td>
        <td><span class="label">Tipo de relatório:</span>{{ $titulo }}</td>
      </tr>
    </table>
  </div>

  <div class="titulo-secao">Pesquisa Entomológica / Tratamento</div>
  <table class="tabela-principal">
    <thead>
      <tr>
        <th style="width:2%">#</th>
        <th style="width:4%">Data</th>
        <th style="width:2.5%">Ciclo</th>
        <th style="width:5%">Atividade</th>
        <th style="width:2%">N/R</th>
        <th style="width:2%">Conc.</th>
        <th style="width:2%">Quart.</th>
        <th style="width:2%">Seq.</th>
        <th style="width:2%">Lado</th>
        <th style="width:5%" class="text-left">Bairro</th>
        <th style="width:10%" class="text-left">Logradouro</th>
        <th style="width:2.5%">Nº</th>
        <th style="width:6%" class="text-left">Compl.</th>
        <th style="width:3.5%">Cód.</th>
        <th style="width:2%">Tipo</th>
        <th style="width:2%">Zona</th>
        <th style="width:2.5%">Pend.</th>
        <th style="width:1.2%">A1</th>
        <th style="width:1.2%">A2</th>
        <th style="width:1.2%">B</th>
        <th style="width:1.2%">C</th>
        <th style="width:1.2%">D1</th>
        <th style="width:1.2%">D2</th>
        <th style="width:1.2%">E</th>
        <th style="width:4.5%">Amostra</th>
        <th style="width:2%">Tub.</th>
        <th style="width:4.5%">Dep.Elim.</th>
        <th style="width:2.5%">Coleta</th>
        <th style="width:12%" class="text-left">Tratamento</th>
        <th style="width:7%" class="text-left">Agente</th>
      </tr>
    </thead>
    <tbody>
      @foreach($visitas as $visita)
      @php
        $loc = $visita->local;
        $tratText = $visita->tratamentos->map(function($t) {
          $p = [];
          if (!empty($t->trat_forma)) $p[] = $t->trat_forma;
          if (!empty($t->trat_tipo)) $p[] = '(' . $t->trat_tipo . ')';
          if (isset($t->qtd_gramas) && $t->qtd_gramas > 0) $p[] = $t->qtd_gramas . 'g';
          if (isset($t->qtd_depositos_tratados) && $t->qtd_depositos_tratados > 0) $p[] = $t->qtd_depositos_tratados . ' dep';
          if (isset($t->qtd_cargas) && $t->qtd_cargas > 0) $p[] = $t->qtd_cargas . ' carg';
          return implode(' ', $p);
        })->filter()->implode('; ');
        $amostra = trim(($visita->vis_amos_inicial ?? '') . '-' . ($visita->vis_amos_final ?? ''));
        if ($amostra === '-') $amostra = '—';
      @endphp
      <tr>
        <td>{{ $visita->vis_id }}</td>
        <td>{{ \Carbon\Carbon::parse($visita->vis_data)->format('d/m/Y') }}</td>
        <td>{{ $visita->vis_ciclo ?? '—' }}</td>
        <td>{{ $atividades[$visita->vis_atividade ?? ''] ?? '—' }}</td>
        <td>{{ $visita->vis_visita_tipo === 'R' ? 'R' : ($visita->vis_visita_tipo === 'N' ? 'N' : '—') }}</td>
        <td>{{ (($visita->vis_pendencias ?? false) ? ($visita->vis_concluida ?? false) : true) ? 'S' : 'N' }}</td>
        <td>{{ $loc?->loc_quarteirao ?? '—' }}</td>
        <td>{{ $loc?->loc_sequencia ?? '—' }}</td>
        <td>{{ $loc?->loc_lado ?? '—' }}</td>
        <td class="text-left">{{ $loc?->loc_bairro ?? '—' }}</td>
        <td class="text-left">{{ $loc?->loc_endereco ?? '—' }}</td>
        <td>{{ $loc?->loc_numero ?? 'S/N' }}</td>
        <td class="text-left">{{ $loc?->loc_complemento ?? '—' }}</td>
        <td>{{ $loc?->loc_codigo_unico ?? '—' }}</td>
        <td>{{ $loc ? ($tipoImovel[$loc->loc_tipo ?? ''] ?? $loc->loc_tipo ?? '—') : '—' }}</td>
        <td>{{ $loc && $loc->loc_zona === 'U' ? 'Urb' : ($loc && $loc->loc_zona === 'R' ? 'Rur' : '—') }}</td>
        <td>{{ ($visita->vis_pendencias ?? false) ? 'Sim' : 'Não' }}</td>
        <td>{{ $visita->insp_a1 ?? 0 }}</td>
        <td>{{ $visita->insp_a2 ?? 0 }}</td>
        <td>{{ $visita->insp_b ?? 0 }}</td>
        <td>{{ $visita->insp_c ?? 0 }}</td>
        <td>{{ $visita->insp_d1 ?? 0 }}</td>
        <td>{{ $visita->insp_d2 ?? 0 }}</td>
        <td>{{ $visita->insp_e ?? 0 }}</td>
        <td>{{ $amostra }}</td>
        <td>{{ $visita->vis_qtd_tubitos ?? '—' }}</td>
        <td>{{ $visita->vis_depositos_eliminados ?? 0 }}</td>
        <td>{{ ($visita->vis_coleta_amostra ?? false) ? 'Sim' : 'Não' }}</td>
        <td class="text-left">{{ $tratText ?: '—' }}</td>
        <td class="text-left">{{ $visita->usuario?->use_nome ?? '—' }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>

  <div class="legenda">
    <strong>Atividade:</strong> 1-LI, 2-LI+T, 3-PPE+T, 4-T, 5-DF, 6-PVE, 7-LIRAa, 8-PE &nbsp;|&nbsp;
    <strong>N/R:</strong> N-Normal, R-Recuperação &nbsp;|&nbsp;
    <strong>Conc.:</strong> Concluída S/N (sem pendência = S) &nbsp;|&nbsp;
    <strong>Cód.:</strong> Código único do imóvel &nbsp;|&nbsp;
    <strong>Tipo:</strong> R-Residencial, C-Comércio, TB-Terreno Baldio, PE-Ponto Estratégico &nbsp;|&nbsp;
    <strong>Pend.:</strong> Pendência (recusado, fechado ou retorno) &nbsp;|&nbsp;
    <strong>Amostra:</strong> Inicial-Final &nbsp;|&nbsp;
    <strong>Dep.Elim.:</strong> Depósitos eliminados &nbsp;|&nbsp;
    <strong>Tub.:</strong> Tubitos &nbsp;|&nbsp;
    <strong>Coleta:</strong> Coleta de amostra Sim/Não
  </div>
</main>
</body>
</html>
