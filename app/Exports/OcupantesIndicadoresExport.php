<?php

namespace App\Exports;

use App\Models\Local;
use App\Models\Morador;
use App\Support\SocioeconomicoEtiquetas as SE;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class OcupantesIndicadoresExport implements WithMultipleSheets
{
    use Exportable;

    private const HEADER_FILL = 'F3F4F6';
    private const SUBHEADER_FILL = 'E5E7EB';
    private const TEXT_COLOR = '1F2937';

    public function sheets(): array
    {
        return [
            'Resumo' => new OcupantesResumoSheet(),
            'Ocupantes' => new OcupantesCadastroSheet(),
            'Distribuições' => new OcupantesDistribuicaoSheet(),
        ];
    }
}

class OcupantesResumoSheet implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithStyles, \Maatwebsite\Excel\Concerns\WithColumnWidths, \Maatwebsite\Excel\Concerns\WithTitle
{
    use \Maatwebsite\Excel\Concerns\Exportable;

    public function title(): string
    {
        return 'Resumo';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 35,
            'B' => 18,
            'C' => 18,
        ];
    }

    public function styles($sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '1F2937']]],
            2 => ['fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F3F4F6']]],
            3 => ['fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E5E7EB']], 'font' => ['bold' => true]],
        ];
    }

    public function array(): array
    {
        $moradores = $this->baseQuery()->get();
        $total = $moradores->count();

        return [
            ['INDICADORES DE OCUPANTES - RESUMO'],
            [],
            ['Métrica', 'Quantidade', 'Percentual'],
            ['Total de Ocupantes', $total, '100%'],
            ['Imóveis com Ocupante', Local::query()->whereHas('moradores')->count(), ''],
            ['Ocupantes - Referência Familiar', (int) $moradores->filter(fn (Morador $m) => (bool) $m->mor_referencia_familiar)->count(), ''],
            [],
            ['FAIXAS ETÁRIAS', '', ''],
        ];
    }

    private function baseQuery()
    {
        return Morador::query()->select([
            'mor_id', 'fk_local_id', 'mor_nome', 'mor_referencia_familiar', 'mor_data_nascimento',
            'mor_escolaridade', 'mor_renda_faixa', 'mor_cor_raca', 'mor_estado_civil', 'mor_parentesco',
        ]);
    }
}

class OcupantesCadastroSheet implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithStyles, \Maatwebsite\Excel\Concerns\WithColumnWidths, \Maatwebsite\Excel\Concerns\WithTitle
{
    use \Maatwebsite\Excel\Concerns\Exportable;

    public function title(): string
    {
        return 'Ocupantes';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 14, 'B' => 35, 'C' => 8,  'D' => 12, 'E' => 12, 'F' => 12,
            'G' => 18, 'H' => 15, 'I' => 15, 'J' => 15, 'K' => 18, 'L' => 20,
        ];
    }

    public function styles($sheet)
    {
        $styles = [];

        // Header row (row 1)
        $styles[1] = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1F2937']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
        ];

        // Data rows
        foreach (range(2, $sheet->getHighestRow()) as $row) {
            $styles[$row] = [
                'alignment' => ['vertical' => Alignment::VERTICAL_TOP, 'wrapText' => true],
                'borders' => [
                    'bottom' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E5E7EB']],
                ],
            ];
        }

        return $styles;
    }

    public function array(): array
    {
        $moradores = Morador::query()
            ->select([
                'mor_id', 'fk_local_id', 'mor_nome', 'mor_referencia_familiar', 'mor_parentesco', 'mor_sexo',
                'mor_data_nascimento', 'mor_estado_civil', 'mor_escolaridade', 'mor_profissao', 'mor_renda_faixa',
                'mor_renda_formal_informal', 'mor_situacao_trabalho', 'mor_cor_raca', 'mor_telefone', 'mor_observacao',
            ])
            ->with(['local' => fn ($q) => $q->select([
                'loc_id', 'loc_codigo_unico', 'loc_endereco', 'loc_numero', 'loc_bairro', 'loc_cidade', 'loc_estado',
            ])])
            ->orderBy('fk_local_id')
            ->orderBy('mor_nome')
            ->get();

        $data = [
            [
                'Código Imóvel', 'Endereço Completo', 'Nº', 'Nome', 'Ref. Familiar', 'Parentesco',
                'Sexo', 'Data Nascimento', 'Idade', 'Estado Civil', 'Escolaridade', 'Profissão',
                'Renda (Faixa)', 'Formal/Informal', 'Situação Trabalho', 'Cor/Raça', 'Telefone', 'Observação',
            ],
        ];

        foreach ($moradores as $m) {
            $local = $m->local;
            $endereco = trim(implode(' ', array_filter([
                $local?->loc_endereco,
                $local?->loc_bairro ? '- ' . $local->loc_bairro : '',
                $local?->loc_cidade,
                $local?->loc_estado ? '(' . $local->loc_estado . ')' : '',
            ])));

            $data[] = [
                $local?->loc_codigo_unico ?? '',
                $endereco,
                $local?->loc_numero ?? '',
                $m->mor_nome ?? '',
                $m->mor_referencia_familiar ? 'Sim' : 'Não',
                SE::opcao('parentesco_opcoes', $m->mor_parentesco),
                SE::opcao('sexo_opcoes', $m->mor_sexo),
                $m->mor_data_nascimento?->format('d/m/Y') ?? '',
                $m->idadeAnos() ?? '',
                SE::opcao('estado_civil_opcoes', $m->mor_estado_civil),
                SE::municipioEscolaridade($m->mor_escolaridade),
                $m->mor_profissao ?? '',
                SE::municipioRenda($m->mor_renda_faixa),
                SE::opcao('renda_formal_informal_opcoes', $m->mor_renda_formal_informal),
                SE::municipioTrabalho($m->mor_situacao_trabalho),
                SE::municipioCor($m->mor_cor_raca),
                $m->mor_telefone ?? '',
                $m->mor_observacao ?? '',
            ];
        }

        return $data;
    }
}

class OcupantesDistribuicaoSheet implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithStyles, \Maatwebsite\Excel\Concerns\WithColumnWidths, \Maatwebsite\Excel\Concerns\WithTitle
{
    use \Maatwebsite\Excel\Concerns\Exportable;

    public function title(): string
    {
        return 'Distribuições';
    }

    public function columnWidths(): array
    {
        return ['A' => 30, 'B' => 15, 'C' => 15];
    }

    public function styles($sheet)
    {
        $styles = [];

        // Section titles
        $sections = [1, 10, 18, 26, 34, 42, 50, 58, 66];
        foreach ($sections as $row) {
            $styles[$row] = [
                'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '374151']],
            ];
        }

        // Headers
        foreach (range(1, $sheet->getHighestRow()) as $row) {
            if (isset($styles[$row])) continue;

            if ($row % 2 === 0) {
                $styles[$row] = [
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F9FAFB']],
                ];
            }
        }

        return $styles;
    }

    public function array(): array
    {
        $escLabels = config('visitaai_municipio.escolaridade_opcoes', []);
        $rendaLabels = config('visitaai_municipio.renda_faixa_opcoes', []);
        $corLabels = config('visitaai_municipio.cor_raca_opcoes', []);
        $trabLabels = config('visitaai_municipio.situacao_trabalho_opcoes', []);
        $sexoLabels = config('visitaai_socioeconomico.sexo_opcoes', []);
        $ecLabels = config('visitaai_socioeconomico.estado_civil_opcoes', []);
        $parLabels = config('visitaai_socioeconomico.parentesco_opcoes', []);
        $rfiLabels = config('visitaai_socioeconomico.renda_formal_informal_opcoes', []);

        $moradores = Morador::query()->get();

        $data = [
            ['ESCOLARIDADE', 'Quantidade', 'Percentual'],
        ];

        foreach ($escLabels as $code => $label) {
            $count = $moradores->filter(fn (Morador $m) => $m->mor_escolaridade === $code)->count();
            $pct = $moradores->count() > 0 ? round(($count / $moradores->count()) * 100, 1) : 0;
            $data[] = [$label, $count, $pct . '%'];
        }

        $data[] = [];
        $data[] = ['RENDA (FAIXA)', 'Quantidade', 'Percentual'];

        foreach ($rendaLabels as $code => $label) {
            $count = $moradores->filter(fn (Morador $m) => $m->mor_renda_faixa === $code)->count();
            $pct = $moradores->count() > 0 ? round(($count / $moradores->count()) * 100, 1) : 0;
            $data[] = [$label, $count, $pct . '%'];
        }

        $data[] = [];
        $data[] = ['COR/RAÇA', 'Quantidade', 'Percentual'];

        foreach ($corLabels as $code => $label) {
            $count = $moradores->filter(fn (Morador $m) => $m->mor_cor_raca === $code)->count();
            $pct = $moradores->count() > 0 ? round(($count / $moradores->count()) * 100, 1) : 0;
            $data[] = [$label, $count, $pct . '%'];
        }

        $data[] = [];
        $data[] = ['SITUAÇÃO DE TRABALHO', 'Quantidade', 'Percentual'];

        foreach ($trabLabels as $code => $label) {
            $count = $moradores->filter(fn (Morador $m) => $m->mor_situacao_trabalho === $code)->count();
            $pct = $moradores->count() > 0 ? round(($count / $moradores->count()) * 100, 1) : 0;
            $data[] = [$label, $count, $pct . '%'];
        }

        $data[] = [];
        $data[] = ['SEXO', 'Quantidade', 'Percentual'];

        foreach ($sexoLabels as $code => $label) {
            $count = $moradores->filter(fn (Morador $m) => $m->mor_sexo === $code)->count();
            $pct = $moradores->count() > 0 ? round(($count / $moradores->count()) * 100, 1) : 0;
            $data[] = [$label, $count, $pct . '%'];
        }

        $data[] = [];
        $data[] = ['ESTADO CIVIL', 'Quantidade', 'Percentual'];

        foreach ($ecLabels as $code => $label) {
            $count = $moradores->filter(fn (Morador $m) => $m->mor_estado_civil === $code)->count();
            $pct = $moradores->count() > 0 ? round(($count / $moradores->count()) * 100, 1) : 0;
            $data[] = [$label, $count, $pct . '%'];
        }

        return $data;
    }
}
