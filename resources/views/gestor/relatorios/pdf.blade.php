@php
    $htmlLang = app()->getLocale() === 'en' ? 'en' : 'pt-BR';
    $nomeSistemaSub = __(config('ms_terminologia.sistema.nome_subtitulo'));
    $brand = config('app.brand', 'Visita Aí');
@endphp
<!DOCTYPE html>
<html lang="{{ $htmlLang }}">
<head>
  <meta charset="UTF-8">
  <title>{{ __('Registro de Atividades de Controle Vetorial') }}</title>
  <style>
    @page { size: A4 landscape; margin: 88px 30px 68px 30px; }
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
    header .header-nota { font-size: 9px; margin: 4px 0 0 0; color: #555; font-style: italic; }
    footer {
      position: fixed;
      bottom: -56px;
      left: 0;
      right: 0;
      text-align: center;
      font-size: 9px;
      color: #555;
      border-top: 1px solid #ccc;
      padding-top: 4px;
    }
    .page-number:after { content: "{{ str_replace('"', '\\"', __('PDF rodapé rótulo página')) }}\00a0" counter(page); }
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
  $mun = $primeiroLocal?->loc_cidade ?? __('Município');
  $uf = $primeiroLocal?->loc_estado ?? __('UF');
  $atividades = \App\Helpers\MsTerminologia::atividadesPncdCodigos();
  $tipoImovel = ['R' => 'R', 'C' => 'C', 'T' => 'TB'];
@endphp

<header>
  <h1>{{ __('Vigilância Entomológica e Controle Vetorial') }}</h1>
  <h2>{{ __('Registro de Atividades | Município de :mun / :uf', ['mun' => $mun, 'uf' => $uf]) }}</h2>
  <p class="header-nota">{{ $nomeSistemaSub }}</p>
</header>

<footer>
  <div class="page-number"></div>
  {{ __('Terminologia e atividades conforme Diretrizes Nacionais para Prevenção e Controle das Arboviroses Urbanas (Vigilância Entomológica e Controle Vetorial) | Ministério da Saúde.') }}@if(strtoupper($uf ?? '') === 'RS') {{ __('No âmbito estadual, em conformidade com a SES-RS e o CEVS.') }}@endif<br>
  {{ __('Documento gerado por :sub (:app) em :data.', ['sub' => $nomeSistemaSub, 'app' => $brand, 'data' => now()->format('d/m/Y H:i')]) }}
</footer>

<main>
  <div class="bloco-ident">
    <table>
      <tr>
        <td><span class="label">{{ __('Município:') }}</span>{{ $mun }} / {{ $uf }}</td>
        <td><span class="label">{{ __('Data do relatório:') }}</span>{{ \Carbon\Carbon::parse($data_inicio)->format('d/m/Y') }}@if($data_inicio != $data_fim) {{ __('PDF relatório entre datas') }} {{ \Carbon\Carbon::parse($data_fim)->format('d/m/Y') }}@endif</td>
      </tr>
      @if(!empty($bairrosPdf))
      <tr>
        <td colspan="2"><span class="label">{{ __('Bairro(s) filtrado(s):') }}</span>{{ is_array($bairrosPdf) ? implode(', ', $bairrosPdf) : $bairrosPdf }}</td>
      </tr>
      @endif
      <tr>
        <td><span class="label">{{ __('Total de visitas no período:') }}</span>{{ $visitas->count() }}</td>
        <td><span class="label">{{ __('Tipo de relatório:') }}</span>{{ $titulo }}</td>
      </tr>
    </table>
  </div>

  @php
    $graficosPdf = array_values(array_filter([
      $graficoBairrosBase64 ?? null,
      $graficoDoencasBase64 ?? null,
      $mapaCalorBase64 ?? null,
      $graficoDiasBase64 ?? null,
      $graficoTratamentosBase64 ?? null,
    ]));
  @endphp
  @if(count($graficosPdf) > 0)
  <div class="titulo-secao">{{ __('Análise visual') }}</div>
  <table style="width:100%; border-collapse:collapse; margin-bottom:12px; table-layout:fixed;">
    @foreach(array_chunk($graficosPdf, 2) as $par)
    <tr>
      @foreach($par as $src)
      <td style="width:50%; vertical-align:top; padding:4px 6px 10px 0; border:none;">
        <img src="{{ $src }}" alt="" style="width:100%; max-height:150px; object-fit:contain;" />
      </td>
      @endforeach
      @if(count($par) === 1)
      <td style="width:50%; border:none;"></td>
      @endif
    </tr>
    @endforeach
  </table>
  @endif

  <div class="titulo-secao">{{ __('Vigilância Entomológica e Controle Vetorial') }}</div>
  <table class="tabela-principal">
    <thead>
      <tr>
        <th style="width:2%">#</th>
        <th style="width:4%">{{ __('Data') }}</th>
        <th style="width:2.5%">{{ __('Ciclo') }}</th>
        <th style="width:5%">{{ __('Atividade') }}</th>
        <th style="width:2%">{{ __('N/R') }}</th>
        <th style="width:2%">{{ __('Conc.') }}</th>
        <th style="width:2%">{{ __('Quart.') }}</th>
        <th style="width:2%">{{ __('Seq.') }}</th>
        <th style="width:2%">{{ __('Lado') }}</th>
        <th style="width:5%" class="text-left">{{ __('Bairro') }}</th>
        <th style="width:10%" class="text-left">{{ __('Logradouro') }}</th>
        <th style="width:2.5%">{{ __('Nº') }}</th>
        <th style="width:6%" class="text-left">{{ __('Compl.') }}</th>
        <th style="width:3.5%">{{ __('Cód.') }}</th>
        <th style="width:2%">{{ __('Tipo') }}</th>
        <th style="width:2%">{{ __('Zona') }}</th>
        <th style="width:2.5%">{{ __('Pend.') }}</th>
        <th style="width:1.2%">A1</th>
        <th style="width:1.2%">A2</th>
        <th style="width:1.2%">B</th>
        <th style="width:1.2%">C</th>
        <th style="width:1.2%">D1</th>
        <th style="width:1.2%">D2</th>
        <th style="width:1.2%">E</th>
        <th style="width:4.5%">{{ __('Amostra') }}</th>
        <th style="width:2%">{{ __('Tub.') }}</th>
        <th style="width:4.5%">{{ __('Dep.Elim.') }}</th>
        <th style="width:2.5%">{{ __('Coleta') }}</th>
        <th style="width:12%" class="text-left">{{ __('Tratamento') }}</th>
        <th style="width:7%" class="text-left">{{ __('Profissional (ACE/ACS)') }}</th>
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
          if (isset($t->qtd_depositos_tratados) && $t->qtd_depositos_tratados > 0) $p[] = $t->qtd_depositos_tratados . ' ' . __('PDF tratamento dep abrev');
          if (isset($t->qtd_cargas) && $t->qtd_cargas > 0) $p[] = $t->qtd_cargas . ' ' . __('PDF tratamento carg abrev');
          return implode(' ', $p);
        })->filter()->implode('; ');
        $amostra = trim(($visita->vis_amos_inicial ?? '') . '-' . ($visita->vis_amos_final ?? ''));
        if ($amostra === '-') $amostra = '-';
      @endphp
      <tr>
        <td>{{ $visita->vis_id }}</td>
        <td>{{ \Carbon\Carbon::parse($visita->vis_data)->format('d/m/Y') }}</td>
        <td>{{ $visita->vis_ciclo ?? '-' }}</td>
        <td>{{ $atividades[$visita->vis_atividade ?? ''] ?? '-' }}</td>
        <td>{{ $visita->vis_visita_tipo === 'R' ? 'R' : ($visita->vis_visita_tipo === 'N' ? 'N' : '-') }}</td>
        <td>{{ (($visita->vis_pendencias ?? false) ? ($visita->vis_concluida ?? false) : true) ? __('PDF coluna concluída sim') : __('PDF coluna concluída não') }}</td>
        <td>{{ $loc?->loc_quarteirao ?? '-' }}</td>
        <td>{{ $loc?->loc_sequencia ?? '-' }}</td>
        <td>{{ $loc?->loc_lado ?? '-' }}</td>
        <td class="text-left">{{ $loc?->loc_bairro ?? '-' }}</td>
        <td class="text-left">{{ $loc?->loc_endereco ?? '-' }}</td>
        <td>{{ $loc?->loc_numero ?? __('S/N') }}</td>
        <td class="text-left">{{ $loc?->loc_complemento ?? '-' }}</td>
        <td>{{ $loc?->loc_codigo_unico ?? '-' }}</td>
        <td>{{ $loc ? ($tipoImovel[$loc->loc_tipo ?? ''] ?? $loc->loc_tipo ?? '-') : '-' }}</td>
        <td>{{ $loc && $loc->loc_zona === 'U' ? __('PDF zona urb abrev') : ($loc && $loc->loc_zona === 'R' ? __('PDF zona rur abrev') : '-') }}</td>
        <td>{{ ($visita->vis_pendencias ?? false) ? __('Sim') : __('Não') }}</td>
        <td>{{ $visita->insp_a1 ?? 0 }}</td>
        <td>{{ $visita->insp_a2 ?? 0 }}</td>
        <td>{{ $visita->insp_b ?? 0 }}</td>
        <td>{{ $visita->insp_c ?? 0 }}</td>
        <td>{{ $visita->insp_d1 ?? 0 }}</td>
        <td>{{ $visita->insp_d2 ?? 0 }}</td>
        <td>{{ $visita->insp_e ?? 0 }}</td>
        <td>{{ $amostra }}</td>
        <td>{{ $visita->vis_qtd_tubitos ?? '-' }}</td>
        <td>{{ $visita->vis_depositos_eliminados ?? 0 }}</td>
        <td>{{ ($visita->vis_coleta_amostra ?? false) ? __('Sim') : __('Não') }}</td>
        <td class="text-left">{{ $tratText ?: '-' }}</td>
        <td class="text-left">{{ $visita->usuario?->use_nome ?? '-' }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>

  @if(isset($imoveisComplementoResumo) && $imoveisComplementoResumo->isNotEmpty())
  <div class="titulo-secao">{{ __('PDF seção cadastro complementar') }}</div>
  <p style="font-size:9px;color:#555;margin:0 0 8px;line-height:1.35;">{{ __('PDF seção cadastro complementar nota') }}</p>
  <table class="tabela-principal" style="font-size:8px;">
    <thead>
      <tr>
        <th style="width:6%" class="text-left">{{ __('Cód.') }}</th>
        <th style="width:18%" class="text-left">{{ __('Logradouro') }}</th>
        <th style="width:8%">{{ __('Nº') }}</th>
        <th style="width:10%" class="text-left">{{ __('Bairro') }}</th>
        <th style="width:5%">{{ __('Ocup.') }}</th>
        <th style="width:6%">{{ __('Socioecon.') }}</th>
        <th style="width:7%">{{ __('Decl. mor.') }}</th>
        <th style="width:20%" class="text-left">{{ __('Faixa renda familiar') }}</th>
      </tr>
    </thead>
    <tbody>
      @foreach($imoveisComplementoResumo as $locPdf)
        @php
          $lsePdf = $locPdf->socioeconomico;
        @endphp
        <tr>
          <td class="text-left">{{ $locPdf->loc_codigo_unico ?? '-' }}</td>
          <td class="text-left">{{ $locPdf->loc_endereco ?? '-' }}</td>
          <td>{{ $locPdf->loc_numero ?? __('S/N') }}</td>
          <td class="text-left">{{ $locPdf->loc_bairro ?? '-' }}</td>
          <td>{{ (int) ($locPdf->moradores_count ?? 0) }}</td>
          <td>{{ $lsePdf ? __('Sim') : __('Não') }}</td>
          <td>{{ $lsePdf && $lsePdf->lse_n_moradores_declarado !== null ? $lsePdf->lse_n_moradores_declarado : '-' }}</td>
          <td class="text-left">{{ $lsePdf && $lsePdf->lse_renda_familiar_faixa ? \Illuminate\Support\Str::limit((string) $lsePdf->lse_renda_familiar_faixa, 42) : '-' }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
  @endif

  <div class="legenda">
    <strong>{{ __('PDF legenda atividade') }}</strong> {{ implode(', ', array_values($atividades)) }} &nbsp;|&nbsp;
    <strong>{{ __('PDF legenda nr') }}</strong> {{ __('PDF legenda nr texto') }} &nbsp;|&nbsp;
    <strong>{{ __('PDF legenda conc') }}</strong> {{ __('PDF legenda conc texto') }} &nbsp;|&nbsp;
    <strong>{{ __('Cód.') }}</strong> {{ __('PDF legenda código texto') }} &nbsp;|&nbsp;
    <strong>{{ __('PDF legenda tipo imóvel') }}</strong> {{ __('PDF legenda tipo imóvel texto') }} &nbsp;|&nbsp;
    <strong>{{ __('Pend.') }}</strong> {{ __('PDF legenda pend texto') }} &nbsp;|&nbsp;
    <strong>{{ __('PDF legenda a1e') }}</strong> {{ __('PDF legenda a1e texto') }} &nbsp;|&nbsp;
    <strong>{{ __('Amostra') }}</strong> {{ __('PDF legenda amostra texto') }} &nbsp;|&nbsp;
    <strong>{{ __('Dep.Elim.') }}</strong> {{ __('PDF legenda dep elim texto') }} &nbsp;|&nbsp;
    <strong>{{ __('Tub.') }}</strong> {{ __('PDF legenda tub texto') }} &nbsp;|&nbsp;
    <strong>{{ __('Coleta') }}</strong> {{ __('PDF legenda coleta texto') }}
  </div>
</main>
</body>
</html>
