<?php

namespace App\Services\Municipio;

use App\Models\Local;
use App\Models\Morador;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Painel de indicadores municipais agregados a partir dos ocupantes cadastrados no Visita Aí.
 * Separa lógica de agregação do controller; não expõe dados individuais no painel web.
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
     *     cor_raca: array<string, int>,
     *     situacao_trabalho: array<string, int>,
     *     completude: array{
     *         total: int,
     *         com_data_nascimento: int,
     *         com_escolaridade_informada: int,
     *         com_renda_informada: int,
     *         com_cor_raca_informada: int,
     *         com_situacao_trabalho_informada: int,
     *         pct_data_nascimento: int,
     *         pct_escolaridade_informada: int,
     *         pct_renda_informada: int,
     *         pct_cor_raca_informada: int,
     *         pct_situacao_trabalho_informada: int
     *     },
     *     cruzamento_escolaridade_renda: array{
     *         linhas: array<string, string>,
     *         colunas: array<string, string>,
     *         celulas: array<string, array<string, array{count: int|null, suprimido: bool, pct_total: int|null}>>,
     *         total_cruzamento: int,
     *         minimo_celula_aplicado: int
     *     },
     *     minimo_aplicado: int
     * }
     */
    public function painelCompleto(): array
    {
        $minimo = max(1, (int) config('visitaai_municipio.indicadores.minimo_registros_bairro', 5));
        $minCelula = max(1, (int) config('visitaai_municipio.indicadores.minimo_celula_cruzamento', 5));

        $moradores = $this->baseQueryMoradores()->get();

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

        $totalOcupantes = $moradores->count();

        return [
            'resumo' => [
                'total_ocupantes' => $totalOcupantes,
                'total_imoveis_com_ocupante' => Local::query()->whereHas('moradores')->count(),
                'faixas_etarias' => $this->resumoOcupantes->contagemFaixasEtarias($moradores),
            ],
            'por_bairro' => $porBairro,
            'escolaridade' => $this->contagemPorChave($moradores, 'mor_escolaridade'),
            'renda' => $this->contagemPorChave($moradores, 'mor_renda_faixa'),
            'cor_raca' => $this->contagemPorChave($moradores, 'mor_cor_raca'),
            'situacao_trabalho' => $this->contagemPorChave($moradores, 'mor_situacao_trabalho'),
            'completude' => $this->completudePainel($moradores, $totalOcupantes),
            'cruzamento_escolaridade_renda' => $this->cruzamentoEscolaridadeRenda($moradores, $totalOcupantes, $minCelula, true),
            'minimo_aplicado' => $minimo,
        ];
    }

    /**
     * CSV para gestor: inclui cruzamento escolaridade × renda com contagens completas (sem supressão por célula).
     */
    public function exportCsvGestor(): string
    {
        $moradores = $this->baseQueryMoradores()->get();
        $total = $moradores->count();
        $painel = [
            'resumo' => [
                'total_ocupantes' => $total,
                'total_imoveis_com_ocupante' => Local::query()->whereHas('moradores')->count(),
                'faixas_etarias' => $this->resumoOcupantes->contagemFaixasEtarias($moradores),
            ],
            'escolaridade' => $this->contagemPorChave($moradores, 'mor_escolaridade'),
            'renda' => $this->contagemPorChave($moradores, 'mor_renda_faixa'),
            'cor_raca' => $this->contagemPorChave($moradores, 'mor_cor_raca'),
            'situacao_trabalho' => $this->contagemPorChave($moradores, 'mor_situacao_trabalho'),
            'completude' => $this->completudePainel($moradores, $total),
        ];
        $cruz = $this->cruzamentoEscolaridadeRenda($moradores, $total, 1, false);
        $matriz = $cruz['celulas'];

        $fp = fopen('php://temp', 'r+');
        fwrite($fp, "\xEF\xBB\xBF");

        $csvLgpd = config('visitaai_municipio.lgpd', []);
        fputcsv($fp, [$csvLgpd['csv_secao_titulo'] ?? 'AVISO_LGPD_EXPORTACAO']);
        foreach ($csvLgpd['csv_aviso_linhas'] ?? [] as $line) {
            fputcsv($fp, [$line]);
        }
        fputcsv($fp, []);

        $escLabels = config('visitaai_municipio.escolaridade_opcoes', []);
        $rendaLabels = config('visitaai_municipio.renda_faixa_opcoes', []);
        $corLabels = config('visitaai_municipio.cor_raca_opcoes', []);
        $trabLabels = config('visitaai_municipio.situacao_trabalho_opcoes', []);
        $faixaLabels = config('visitaai_municipio.indicadores.colunas_faixas', []);

        fputcsv($fp, ['Visita Aí — indicadores ocupantes (exportação gestor)']);
        fputcsv($fp, ['gerado_em', now()->toIso8601String()]);
        fputcsv($fp, []);

        fputcsv($fp, ['RESUMO']);
        fputcsv($fp, ['total_ocupantes', $painel['resumo']['total_ocupantes']]);
        fputcsv($fp, ['imoveis_com_ocupante', $painel['resumo']['total_imoveis_com_ocupante']]);
        fputcsv($fp, []);

        fputcsv($fp, ['FAIXA_ETARIA']);
        fputcsv($fp, ['codigo', 'rotulo', 'quantidade']);
        foreach ($painel['resumo']['faixas_etarias'] as $k => $q) {
            fputcsv($fp, [$k, $faixaLabels[$k] ?? $k, $q]);
        }
        fputcsv($fp, []);

        $Q = $painel['completude'];
        fputcsv($fp, ['COMPLETUDE']);
        fputcsv($fp, ['metrica', 'quantidade', 'percentual']);
        fputcsv($fp, ['data_nascimento', $Q['com_data_nascimento'], $Q['pct_data_nascimento']]);
        fputcsv($fp, ['escolaridade_informada', $Q['com_escolaridade_informada'], $Q['pct_escolaridade_informada']]);
        fputcsv($fp, ['renda_informada', $Q['com_renda_informada'], $Q['pct_renda_informada']]);
        fputcsv($fp, ['cor_raca_informada', $Q['com_cor_raca_informada'], $Q['pct_cor_raca_informada']]);
        fputcsv($fp, ['situacao_trabalho_informada', $Q['com_situacao_trabalho_informada'], $Q['pct_situacao_trabalho_informada']]);
        fputcsv($fp, []);

        $this->fputcsvDistribuicao($fp, 'ESCOLARIDADE', $painel['escolaridade'], $escLabels);
        $this->fputcsvDistribuicao($fp, 'RENDA_FAIXA', $painel['renda'], $rendaLabels);
        $this->fputcsvDistribuicao($fp, 'COR_RACA', $painel['cor_raca'], $corLabels);
        $this->fputcsvDistribuicao($fp, 'SITUACAO_TRABALHO', $painel['situacao_trabalho'], $trabLabels);

        fputcsv($fp, ['CRUZAMENTO_ESCOLARIDADE_RENDA_COMPLETO']);
        fputcsv($fp, ['sem_supressao_celula', '1']);
        $keysEsc = array_keys($escLabels);
        $keysRenda = array_keys($rendaLabels);
        $header = array_merge(['escolaridade_codigo', 'escolaridade_rotulo'], $keysRenda);
        fputcsv($fp, $header);
        foreach ($keysEsc as $ke) {
            $row = [$ke, $escLabels[$ke] ?? $ke];
            foreach ($keysRenda as $kr) {
                $cell = $matriz[$ke][$kr] ?? null;
                $row[] = (int) ($cell['count'] ?? 0);
            }
            fputcsv($fp, $row);
        }

        rewind($fp);

        return stream_get_contents($fp) ?: '';
    }

    /**
     * @param  array<string, int>  $contagens
     * @param  array<string, string>  $labels
     */
    private function fputcsvDistribuicao($fp, string $secao, array $contagens, array $labels): void
    {
        fputcsv($fp, [$secao]);
        fputcsv($fp, ['codigo', 'rotulo', 'quantidade']);
        foreach ($contagens as $codigo => $qtd) {
            fputcsv($fp, [$codigo, $labels[$codigo] ?? $codigo, $qtd]);
        }
        fputcsv($fp, []);
    }

    private function baseQueryMoradores(): Builder
    {
        return Morador::query()
            ->select([
                'mor_id', 'fk_local_id', 'mor_data_nascimento', 'mor_escolaridade', 'mor_renda_faixa',
                'mor_cor_raca', 'mor_situacao_trabalho',
            ])
            ->with(['local' => fn ($q) => $q->select('loc_id', 'loc_bairro')]);
    }

    /**
     * @param  Collection<int, Morador>  $moradores
     * @return array{
     *     total: int,
     *     com_data_nascimento: int,
     *     com_escolaridade_informada: int,
     *     com_renda_informada: int,
     *     com_cor_raca_informada: int,
     *     com_situacao_trabalho_informada: int,
     *     pct_data_nascimento: int,
     *     pct_escolaridade_informada: int,
     *     pct_renda_informada: int,
     *     pct_cor_raca_informada: int,
     *     pct_situacao_trabalho_informada: int
     * }
     */
    private function completudePainel(Collection $moradores, int $total): array
    {
        $t = max(0, $total);
        if ($t === 0) {
            return [
                'total' => 0,
                'com_data_nascimento' => 0,
                'com_escolaridade_informada' => 0,
                'com_renda_informada' => 0,
                'com_cor_raca_informada' => 0,
                'com_situacao_trabalho_informada' => 0,
                'pct_data_nascimento' => 0,
                'pct_escolaridade_informada' => 0,
                'pct_renda_informada' => 0,
                'pct_cor_raca_informada' => 0,
                'pct_situacao_trabalho_informada' => 0,
            ];
        }

        $comDn = 0;
        $comEsc = 0;
        $comRenda = 0;
        $comCor = 0;
        $comTrab = 0;
        foreach ($moradores as $m) {
            if ($m->mor_data_nascimento !== null) {
                $comDn++;
            }
            $esc = (string) ($m->mor_escolaridade ?? '');
            if ($esc !== '' && $esc !== 'nao_informado') {
                $comEsc++;
            }
            $r = (string) ($m->mor_renda_faixa ?? '');
            if ($r !== '' && $r !== 'nao_informado') {
                $comRenda++;
            }
            $c = (string) ($m->mor_cor_raca ?? '');
            if ($c !== '' && $c !== 'nao_informado') {
                $comCor++;
            }
            $tr = (string) ($m->mor_situacao_trabalho ?? '');
            if ($tr !== '' && $tr !== 'nao_informado') {
                $comTrab++;
            }
        }

        return [
            'total' => $t,
            'com_data_nascimento' => $comDn,
            'com_escolaridade_informada' => $comEsc,
            'com_renda_informada' => $comRenda,
            'com_cor_raca_informada' => $comCor,
            'com_situacao_trabalho_informada' => $comTrab,
            'pct_data_nascimento' => (int) round(100 * $comDn / $t),
            'pct_escolaridade_informada' => (int) round(100 * $comEsc / $t),
            'pct_renda_informada' => (int) round(100 * $comRenda / $t),
            'pct_cor_raca_informada' => (int) round(100 * $comCor / $t),
            'pct_situacao_trabalho_informada' => (int) round(100 * $comTrab / $t),
        ];
    }

    /**
     * @param  Collection<int, Morador>  $moradores
     * @return array{
     *     linhas: array<string, string>,
     *     colunas: array<string, string>,
     *     celulas: array<string, array<string, array{count: int|null, suprimido: bool, pct_total: int|null}>>,
     *     total_cruzamento: int,
     *     minimo_celula_aplicado: int
     * }
     */
    private function cruzamentoEscolaridadeRenda(Collection $moradores, int $totalOcupantes, int $minCelula, bool $aplicarSupressao): array
    {
        $escLabels = config('visitaai_municipio.escolaridade_opcoes', []);
        $rendaLabels = config('visitaai_municipio.renda_faixa_opcoes', []);
        $keysEsc = array_keys($escLabels);
        $keysRenda = array_keys($rendaLabels);

        $raw = [];
        foreach ($keysEsc as $ke) {
            foreach ($keysRenda as $kr) {
                $raw[$ke][$kr] = 0;
            }
        }

        foreach ($moradores as $m) {
            $ke = (string) ($m->mor_escolaridade ?? '');
            $ke = $ke !== '' ? $ke : 'nao_informado';
            $kr = (string) ($m->mor_renda_faixa ?? '');
            $kr = $kr !== '' ? $kr : 'nao_informado';
            if (! isset($raw[$ke][$kr])) {
                $ke = 'nao_informado';
                $kr = 'nao_informado';
            }
            $raw[$ke][$kr]++;
        }

        $denom = max(1, $totalOcupantes);
        $celulas = [];
        foreach ($keysEsc as $ke) {
            foreach ($keysRenda as $kr) {
                $c = (int) ($raw[$ke][$kr] ?? 0);
                $sup = $aplicarSupressao && $c > 0 && $c < $minCelula;
                $celulas[$ke][$kr] = [
                    'count' => $sup ? null : $c,
                    'suprimido' => $sup,
                    'pct_total' => (! $sup && $c > 0) ? (int) round(100 * $c / $denom) : ($sup ? null : 0),
                ];
            }
        }

        return [
            'linhas' => $escLabels,
            'colunas' => $rendaLabels,
            'celulas' => $celulas,
            'total_cruzamento' => $totalOcupantes,
            'minimo_celula_aplicado' => $aplicarSupressao ? $minCelula : 0,
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
}
