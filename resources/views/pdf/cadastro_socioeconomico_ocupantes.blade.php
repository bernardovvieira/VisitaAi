<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>{{ __('Cadastro socioeconômico de ocupantes') }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 9pt; color: #111; }
        h1 { font-size: 13pt; margin: 0 0 6px 0; }
        h2 { font-size: 10pt; margin: 14px 0 6px 0; border-bottom: 1px solid #333; padding-bottom: 2px; }
        /* tables use fixed layout and wrap aggressively to avoid overflow */
        table { width: 100%; border-collapse: collapse; margin-bottom: 8px; table-layout: fixed; word-wrap: break-word; overflow-wrap: break-word; }
        th, td { border: 1px solid #444; padding: 4px 6px; vertical-align: top; overflow-wrap: anywhere; word-wrap: break-word; white-space: normal; hyphens: auto; word-break: break-all; }
        th { background: #f0f0f0; font-weight: bold; text-align: left; width: 32%; }
        .muted { color: #555; font-size: 8pt; }
        .small { font-size: 8pt; }
        .page-break { page-break-after: always; }
        .renda { font-weight: 700; color: #111; }
    </style>
    /* Reserve space for header/footer */
        @page { margin: 70px 20px 70px 20px; }
</head>
<body>
@php
    use App\Support\SocioeconomicoEtiquetas as SE;
@endphp

<!-- Header (fixed) -->
<div class="header" style="position: fixed; top: 0; left: 0; right: 0; height: 56px; padding: 8px 12px; border-bottom: 1px solid #ccc;">
    <div style="display:flex; justify-content:space-between; align-items:center; font-size:10pt;">
        <div style="font-weight:700;">Visita Aí</div>
        <div style="font-size:9pt; color:#555;">Cadastro Socioeconômico — Lista de locais</div>
        <div style="font-size:9pt; color:#333;">Bitwise Technologies</div>
    </div>
</div>

<!-- Footer placeholder (dompdf will draw text using PHP script for accurate page numbers) -->
<div class="footer" style="position: fixed; bottom: 0; left: 0; right: 0; height: 48px; padding: 6px 12px; border-top: 1px solid #ccc; font-size:9pt; color:#555;">
    <div style="display:flex; justify-content:space-between; align-items:center;">
        <div>Visita Aí — Bitwise Technologies</div>
        <div> <!-- page numbers rendered by dompdf script --> </div>
    </div>
</div>

<script type="text/php">
    if (isset($pdf)) {
        $font = $fontMetrics->getFont('DejaVuSans', 'normal');
        $y = $pdf->get_height() - 35; // position above footer border
        $pdf->page_text(40, $y, 'Visita Aí — Bitwise Technologies', $font, 8, array(0,0,0));
        $pdf->page_text($pdf->get_width() - 120, $y, 'Página {PAGE_NUM} / {PAGE_COUNT}', $font, 8, array(0,0,0));
    }
</script>

@forelse($locais as $local)
    @php
        $s = $local->socioeconomico;
    @endphp

    <h1>{{ __('CADASTRO SOCIOECONÔMICO') }}</h1>
    <p class="muted">{{ config('app.name') }} · {{ __('Código do imóvel') }}: <strong>{{ $local->loc_codigo_unico }}</strong></p>
    <p class="small">{{ $local->loc_endereco }}, {{ $local->loc_numero ?? 'S/N' }}, {{ $local->loc_bairro }}, {{ $local->loc_cidade }}/{{ $local->loc_estado }}, CEP {{ $local->loc_cep ?? '-' }}</p>

    <h2>{{ $titulos['entrevista'] ?? '1. Entrevista e domicílio' }}</h2>
    <table>
        <tr><th>{{ __('Data da entrevista') }}</th><td>{{ $s?->lse_data_entrevista?->format('d/m/Y') ?? '-' }}</td></tr>
        <tr><th>{{ __('Telefone de contato') }}</th><td>{{ $s?->lse_telefone_contato ?? '-' }}</td></tr>
        <tr><th>{{ __('Condição da moradia') }}</th><td>{{ SE::opcao('condicao_casa_opcoes', $s?->lse_condicao_casa) }}</td></tr>
        <tr><th>{{ __('Posição de quem respondeu') }}</th><td>{{ SE::opcao('posicao_entrevistado_opcoes', $s?->lse_posicao_entrevistado) }}</td></tr>
        <tr><th>{{ __('Nº moradores declarado') }}</th><td>{{ $s?->lse_n_moradores_declarado ?? '-' }}</td></tr>
    </table>

    <h2>{{ $titulos['economia'] ?? '2. Economia do grupo familiar' }}</h2>
    <table>
        <tr><th>{{ __('Renda formal/informal') }}</th><td>{{ SE::opcao('renda_formal_informal_opcoes', $s?->lse_renda_formal_informal) }}</td></tr>
        <tr><th>{{ __('Renda familiar (faixa)') }}</th><td>{{ SE::municipioRenda($s?->lse_renda_familiar_faixa) }}</td></tr>
        <tr><th>{{ __('Principal fonte de renda') }}</th><td>{{ $s?->lse_principal_fonte_renda ?? '-' }}</td></tr>
        <tr><th>{{ __('Pessoas que contribuem') }}</th><td>{{ $s?->lse_qtd_contribuintes ?? '-' }}</td></tr>
        <tr><th>{{ __('Gastos mensais (faixa)') }}</th><td>{{ SE::opcao('gastos_mensais_faixa_opcoes', $s?->lse_gastos_mensais_faixa) }}</td></tr>
        <tr><th>{{ __('Benefícios sociais') }}</th><td>{{ $s?->lse_beneficios_sociais ?? '-' }}</td></tr>
    </table>

    <h2>{{ $titulos['moradores'] ?? 'Composição familiar' }}</h2>
    <table>
        <thead>
            <tr>
                <th style="width:35%">{{ __('Nome / Profissão') }}</th>
                <th style="width:5%">{{ __('Ref.') }}</th>
                <th style="width:15%">{{ __('Parentesco') }}</th>
                <th style="width:8%">{{ __('Sexo') }}</th>
                <th style="width:12%">{{ __('Nasc.') }}</th>
                <th style="width:25%">{{ __('Escolaridade • Renda • Trabalho') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($local->moradores as $m)
                <tr>
                    <td>
                        <strong>{{ $m->mor_nome ?? '-' }}</strong>
                        @if($m->mor_profissao)
                            <div class="small">{{ $m->mor_profissao }}</div>
                        @endif
                    </td>
                    <td class="center">{{ $m->mor_referencia_familiar ? '★' : '-' }}</td>
                    <td>{{ SE::opcao('parentesco_opcoes', $m->mor_parentesco) }}</td>
                    <td>{{ SE::opcao('sexo_opcoes', $m->mor_sexo) }}</td>
                    <td>{{ $m->mor_data_nascimento?->format('d/m/Y') ?? '-' }}</td>
                    <td>
                        {{ SE::municipioEscolaridade($m->mor_escolaridade) }}
                        @if($m->mor_renda_faixa) • <span class="renda">{{ SE::municipioRenda($m->mor_renda_faixa) }}</span>@endif
                        @if($m->mor_situacao_trabalho) • {{ SE::municipioTrabalho($m->mor_situacao_trabalho) }}@endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="6">{{ __('Nenhum morador cadastrado.') }}</td></tr>
            @endforelse
        </tbody>
    </table>

    <p class="small">{{ __('Documentos (RG/CPF) foram omitidos do PDF por padrão; constam no sistema quando informados.') }}</p>
    <p class="muted" style="margin-top: 10px;">{{ __('Documento gerado pelo sistema em ') }}{{ now()->format('d/m/Y H:i') }}.</p>

    @if (! $loop->last)
        <div class="page-break"></div>
    @endif
@empty
    <h1>{{ __('CADASTRO SOCIOECONÔMICO') }}</h1>
    <p>{{ __('Nenhum ocupante cadastrado para exportação.') }}</p>
@endforelse
</body>
</html>
