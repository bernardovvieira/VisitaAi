<?php

namespace App\Services\Municipio;

use App\Models\Local;
use App\Models\Morador;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Painel de indicadores municipais agregados a partir dos ocupantes cadastrados no Visita Aí.
 * Separa lógica de agregação do controller; não expõe dados individuais.
 */
class IndicadoresOcupantesMunicipioService
{
    public function __construct(
        private ResumoOcupantesMunicipioService $resumoOcupantes
    ) {}

    /**
     * @return array{
     *     resumo: array{total_ocupantes: int, total_imoveis_com_ocupante: int, faixas_etarias: array<string, int>},
     *     por_bairro: array<int, array{bairro: string, total: int|null, faixas: array<string, int>|null, suprimido: bool}>,
     *     escolaridade: array<string, int>,
     *     renda: array<string, int>,
     *     minimo_aplicado: int
     * }
     */
    public function painelCompleto(): array
    {
        $minimo = max(1, (int) config('visitaai_municipio.indicadores.minimo_registros_bairro', 5));

        $moradores = Morador::query()
            ->select(['mor_id', 'fk_local_id', 'mor_data_nascimento', 'mor_escolaridade', 'mor_renda_faixa'])
            ->with(['local' => fn ($q) => $q->select('loc_id', 'loc_bairro')])
            ->get();

        $semBairro = (string) config('visitaai_municipio.indicadores.sem_bairro_label');

        $porBairro = $moradores
            ->groupBy(fn (Morador $m) => trim((string) ($m->local?->loc_bairro ?? '')) ?: $semBairro)
            ->map(function (Collection $grupo, string $bairro) use ($minimo) {
                $total = $grupo->count();
                $suprimido = $total > 0 && $total < $minimo;

                return [
                    'bairro' => $bairro,
                    'total' => $suprimido ? null : $total,
                    'faixas' => $suprimido ? null : $this->resumoOcupantes->contagemFaixasEtarias($grupo),
                    'suprimido' => $suprimido,
                ];
            })
            ->values()
            ->sortByDesc(fn (array $row) => $row['suprimido'] ? -1 : ($row['total'] ?? 0))
            ->values()
            ->all();

        return [
            'resumo' => [
                'total_ocupantes' => $moradores->count(),
                'total_imoveis_com_ocupante' => Local::query()->whereHas('moradores')->count(),
                'faixas_etarias' => $this->resumoOcupantes->contagemFaixasEtarias($moradores),
            ],
            'por_bairro' => $porBairro,
            'escolaridade' => $this->contagemPorChave($moradores, 'mor_escolaridade'),
            'renda' => $this->contagemPorChave($moradores, 'mor_renda_faixa'),
            'minimo_aplicado' => $minimo,
        ];
    }

    /**
     * @param  Collection<int, Morador>  $moradores
     * @return array<string, int>
     */
    private function contagemPorChave(Collection $moradores, string $atributo): array
    {
        $out = [];
        foreach ($moradores as $m) {
            $k = (string) ($m->{$atributo} ?? '');
            $k = $k !== '' ? $k : 'nao_informado';
            $out[$k] = ($out[$k] ?? 0) + 1;
        }
        arsort($out);

        return $out;
    }

    /**
     * CSV com separador ';' e BOM UTF-8 para planilhas (Excel em PT-BR).
     * Apenas agregados; linhas suprimidas exportadas como marca textual, sem totais.
     */
    public function respostaDownloadCsv(): StreamedResponse
    {
        $painel = $this->painelCompleto();
        $cfg = config('visitaai_municipio.indicadores', []);
        $labelsFaixa = $cfg['colunas_faixas'] ?? [];
        $keysFaixa = ['0-11', '12-17', '18-59', '60+', 'sem_info'];
        $escLabels = config('visitaai_municipio.escolaridade_opcoes', []);
        $rendaLabels = config('visitaai_municipio.renda_faixa_opcoes', []);
        $supLabel = (string) ($cfg['csv_suprimido_label'] ?? 'suprimido');
        $sep = ';';
        $filename = 'visita-ai-indicadores-ocupantes-'.now()->format('Y-m-d-His').'.csv';

        return new StreamedResponse(function () use ($painel, $cfg, $labelsFaixa, $keysFaixa, $escLabels, $rendaLabels, $supLabel, $sep) {
            $out = fopen('php://output', 'w');
            if ($out === false) {
                return;
            }
            fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));

            $row = fn (array $cols) => fputcsv($out, $cols, $sep);

            $row([(string) ($cfg['csv_titulo'] ?? 'Indicadores agregados')]);
            $row(['Gerado em', now()->toIso8601String()]);
            $row(['Mínimo por bairro (supressão)', (string) $painel['minimo_aplicado']]);
            $row([]);

            $R = $painel['resumo'];
            $row(['Resumo global']);
            $row(['Total de ocupantes', (string) $R['total_ocupantes']]);
            $row(['Imóveis com ocupante', (string) $R['total_imoveis_com_ocupante']]);
            foreach ($keysFaixa as $k) {
                $label = $labelsFaixa[$k] ?? $k;
                $row([$label, (string) ($R['faixas_etarias'][$k] ?? 0)]);
            }
            $row([]);

            $row(array_merge(['Bairro (imóvel)', 'Total'], array_map(fn ($k) => $labelsFaixa[$k] ?? $k, $keysFaixa)));
            foreach ($painel['por_bairro'] as $linha) {
                if ($linha['suprimido']) {
                    $row(array_merge([$linha['bairro'], $supLabel], array_fill(0, count($keysFaixa), $supLabel)));
                } else {
                    $f = $linha['faixas'] ?? [];
                    $row(array_merge(
                        [$linha['bairro'], (string) ($linha['total'] ?? 0)],
                        array_map(fn ($k) => (string) ($f[$k] ?? 0), $keysFaixa)
                    ));
                }
            }
            $row([]);

            $row(['Escolaridade informada (código)', 'Quantidade', 'Rótulo']);
            foreach ($painel['escolaridade'] as $codigo => $qtd) {
                $row([$codigo, (string) $qtd, (string) ($escLabels[$codigo] ?? $codigo)]);
            }
            $row([]);

            $row(['Renda informada (código)', 'Quantidade', 'Rótulo']);
            foreach ($painel['renda'] as $codigo => $qtd) {
                $row([$codigo, (string) $qtd, (string) ($rendaLabels[$codigo] ?? $codigo)]);
            }

            fclose($out);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Cache-Control' => 'no-store, private',
        ]);
    }
}
