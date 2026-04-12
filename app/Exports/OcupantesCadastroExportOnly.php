<?php

namespace App\Exports;

use App\Models\Morador;
use App\Support\SocioeconomicoEtiquetas as SE;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class OcupantesCadastroExportOnly implements FromArray, WithStyles, WithColumnWidths, WithTitle
{
    use Exportable;

    public function title(): string
    {
        return 'Ocupantes';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 14, 'B' => 35, 'C' => 8,  'D' => 12, 'E' => 12, 'F' => 12,
            'G' => 18, 'H' => 15, 'I' => 15, 'J' => 15, 'K' => 18, 'L' => 20,
            'M' => 15, 'N' => 16, 'O' => 18, 'P' => 12, 'Q' => 14, 'R' => 20,
        ];
    }

    public function styles($sheet)
    {
        $styles = [];

        // Header row
        $styles[1] = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1F2937']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
        ];

        // Data rows with alternating background
        foreach (range(2, $sheet->getHighestRow()) as $row) {
            $bgColor = ($row - 2) % 2 === 0 ? 'FFFFFF' : 'F9FAFB';
            $styles[$row] = [
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor]],
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
