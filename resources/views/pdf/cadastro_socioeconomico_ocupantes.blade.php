<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>{{ __('Cadastro socioeconômico de ocupantes') }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 9pt; color: #111; }
        h1 { font-size: 13pt; margin: 0 0 6px 0; }
        h2 { font-size: 10pt; margin: 14px 0 6px 0; border-bottom: 1px solid #333; padding-bottom: 2px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        th, td { border: 1px solid #444; padding: 4px 6px; vertical-align: top; }
        th { background: #f0f0f0; font-weight: bold; text-align: left; width: 32%; }
        .muted { color: #555; font-size: 8pt; }
        .small { font-size: 8pt; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>
@php
    use App\Support\SocioeconomicoEtiquetas as SE;
@endphp

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
                <th>{{ __('Nome') }}</th>
                <th>{{ __('Ref.') }}</th>
                <th>{{ __('Parentesco') }}</th>
                <th>{{ __('Sexo') }}</th>
                <th>{{ __('Nasc.') }}</th>
                <th>{{ __('Est. civil') }}</th>
                <th>{{ __('Escolaridade') }}</th>
                <th>{{ __('Profissão') }}</th>
                <th>{{ __('Renda (faixa)') }}</th>
                <th>{{ __('Trabalho') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($local->moradores as $m)
                <tr>
                    <td>{{ $m->mor_nome ?? '-' }}</td>
                    <td>{{ $m->mor_referencia_familiar ? '★' : '-' }}</td>
                    <td>{{ SE::opcao('parentesco_opcoes', $m->mor_parentesco) }}</td>
                    <td>{{ SE::opcao('sexo_opcoes', $m->mor_sexo) }}</td>
                    <td>{{ $m->mor_data_nascimento?->format('d/m/Y') ?? '-' }}</td>
                    <td>{{ SE::opcao('estado_civil_opcoes', $m->mor_estado_civil) }}</td>
                    <td>{{ SE::municipioEscolaridade($m->mor_escolaridade) }}</td>
                    <td>{{ $m->mor_profissao ?? '-' }}</td>
                    <td>{{ SE::municipioRenda($m->mor_renda_faixa) }}</td>
                    <td>{{ SE::municipioTrabalho($m->mor_situacao_trabalho) }}</td>
                </tr>
            @empty
                <tr><td colspan="10">{{ __('Nenhum morador cadastrado.') }}</td></tr>
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
