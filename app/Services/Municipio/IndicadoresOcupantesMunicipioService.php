<?php

namespace App\Services\Municipio;

use App\Models\Local;
use App\Models\Morador;
use App\Support\SocioeconomicoEtiquetas as SE;
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
     *     resumo: array{
     *         total_ocupantes: int,
     *         total_imoveis_com_ocupante: int,
     *         ocupantes_referencia_familiar: int,
     *         faixas_etarias: array<string, int>
     *     },
     *     por_bairro: array<int, array{bairro: string, total: int|null, faixas: array<string, int>|null, suprimido: bool}>,
     *     escolaridade: array<string, int>,
     *     renda: array<string, int>,
     *     cor_raca: array<string, int>,
     *     situacao_trabalho: array<string, int>,
     *     sexo: array<string, int>,
     *     estado_civil: array<string, int>,
     *     parentesco: array<string, int>,
     *     renda_formal_informal: array<string, int>,
     *     completude: array{
     *         total: int,
     *         com_data_nascimento: int,
     *         com_escolaridade_informada: int,
     *         com_renda_informada: int,
     *         com_cor_raca_informada: int,
     *         com_situacao_trabalho_informada: int,
     *         com_sexo_informado: int,
     *         com_estado_civil_informado: int,
     *         com_parentesco_informado: int,
     *         com_renda_formal_informal_informada: int,
     *         pct_data_nascimento: int,
     *         pct_escolaridade_informada: int,
     *         pct_renda_informada: int,
     *         pct_cor_raca_informada: int,
     *         pct_situacao_trabalho_informada: int,
     *         pct_sexo_informado: int,
     *         pct_estado_civil_informado: int,
     *         pct_parentesco_informado: int,
     *         pct_renda_formal_informal_informada: int
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

        $refFam = (int) $moradores->filter(fn (Morador $m) => (bool) $m->mor_referencia_familiar)->count();

        return [
            'resumo' => [
                'total_ocupantes' => $totalOcupantes,
                'total_imoveis_com_ocupante' => Local::query()->whereHas('moradores')->count(),
                'ocupantes_referencia_familiar' => $refFam,
                'faixas_etarias' => $this->resumoOcupantes->contagemFaixasEtarias($moradores),
            ],
            'por_bairro' => $porBairro,
            'escolaridade' => $this->contagemPorChave($moradores, 'mor_escolaridade'),
            'renda' => $this->contagemPorChave($moradores, 'mor_renda_faixa'),
            'cor_raca' => $this->contagemPorChave($moradores, 'mor_cor_raca'),
            'situacao_trabalho' => $this->contagemPorChave($moradores, 'mor_situacao_trabalho'),
            'sexo' => $this->contagemPorChave($moradores, 'mor_sexo'),
            'estado_civil' => $this->contagemPorChave($moradores, 'mor_estado_civil'),
            'parentesco' => $this->contagemPorChave($moradores, 'mor_parentesco'),
            'renda_formal_informal' => $this->contagemPorChave($moradores, 'mor_renda_formal_informal'),
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
        $refFamCsv = (int) $moradores->filter(fn (Morador $m) => (bool) $m->mor_referencia_familiar)->count();

        $painel = [
            'resumo' => [
                'total_ocupantes' => $total,
                'total_imoveis_com_ocupante' => Local::query()->whereHas('moradores')->count(),
                'ocupantes_referencia_familiar' => $refFamCsv,
                'faixas_etarias' => $this->resumoOcupantes->contagemFaixasEtarias($moradores),
            ],
            'escolaridade' => $this->contagemPorChave($moradores, 'mor_escolaridade'),
            'renda' => $this->contagemPorChave($moradores, 'mor_renda_faixa'),
            'cor_raca' => $this->contagemPorChave($moradores, 'mor_cor_raca'),
            'situacao_trabalho' => $this->contagemPorChave($moradores, 'mor_situacao_trabalho'),
            'sexo' => $this->contagemPorChave($moradores, 'mor_sexo'),
            'estado_civil' => $this->contagemPorChave($moradores, 'mor_estado_civil'),
            'parentesco' => $this->contagemPorChave($moradores, 'mor_parentesco'),
            'renda_formal_informal' => $this->contagemPorChave($moradores, 'mor_renda_formal_informal'),
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
        $sexoLabels = config('visitaai_socioeconomico.sexo_opcoes', []);
        $ecLabels = config('visitaai_socioeconomico.estado_civil_opcoes', []);
        $parLabels = config('visitaai_socioeconomico.parentesco_opcoes', []);
        $rfiLabels = config('visitaai_socioeconomico.renda_formal_informal_opcoes', []);
        $faixaLabels = config('visitaai_municipio.indicadores.colunas_faixas', []);

        fputcsv($fp, ['Visita Aí: indicadores ocupantes']);
        fputcsv($fp, ['gerado_em', now()->toIso8601String()]);
        fputcsv($fp, []);

        fputcsv($fp, ['RESUMO']);
        fputcsv($fp, ['total_ocupantes', $painel['resumo']['total_ocupantes']]);
        fputcsv($fp, ['imoveis_com_ocupante', $painel['resumo']['total_imoveis_com_ocupante']]);
        fputcsv($fp, ['ocupantes_referencia_familiar', $painel['resumo']['ocupantes_referencia_familiar']]);
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
        fputcsv($fp, ['sexo_informado', $Q['com_sexo_informado'], $Q['pct_sexo_informado']]);
        fputcsv($fp, ['estado_civil_informado', $Q['com_estado_civil_informado'], $Q['pct_estado_civil_informado']]);
        fputcsv($fp, ['parentesco_informado', $Q['com_parentesco_informado'], $Q['pct_parentesco_informado']]);
        fputcsv($fp, ['renda_formal_informal_informada', $Q['com_renda_formal_informal_informada'], $Q['pct_renda_formal_informal_informada']]);
        fputcsv($fp, []);

        $this->fputcsvDistribuicao($fp, 'ESCOLARIDADE', $painel['escolaridade'], $escLabels);
        $this->fputcsvDistribuicao($fp, 'RENDA_FAIXA', $painel['renda'], $rendaLabels);
        $this->fputcsvDistribuicao($fp, 'COR_RACA', $painel['cor_raca'], $corLabels);
        $this->fputcsvDistribuicao($fp, 'SITUACAO_TRABALHO', $painel['situacao_trabalho'], $trabLabels);
        $this->fputcsvDistribuicao($fp, 'SEXO', $painel['sexo'], $sexoLabels);
        $this->fputcsvDistribuicao($fp, 'ESTADO_CIVIL', $painel['estado_civil'], $ecLabels);
        $this->fputcsvDistribuicao($fp, 'PARENTESCO_TITULAR', $painel['parentesco'], $parLabels);
        $this->fputcsvDistribuicao($fp, 'RENDA_FORMAL_INFORMAL', $painel['renda_formal_informal'], $rfiLabels);

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
     * CSV detalhado de ocupantes, no formato de cadastro socioeconômico (uso interno do gestor).
     * Não inclui RG/CPF por padrão.
     */
    public function exportCadastroOcupantesCsvGestor(): string
    {
        $moradores = Morador::query()
            ->select([
                'mor_id', 'fk_local_id', 'mor_nome', 'mor_referencia_familiar', 'mor_parentesco', 'mor_sexo',
                'mor_data_nascimento', 'mor_estado_civil', 'mor_escolaridade', 'mor_profissao', 'mor_renda_faixa',
                'mor_renda_formal_informal', 'mor_situacao_trabalho', 'mor_cor_raca', 'mor_naturalidade',
                'mor_telefone', 'mor_observacao',
            ])
            ->with(['local' => fn ($q) => $q->select([
                'loc_id', 'loc_codigo_unico', 'loc_endereco', 'loc_numero', 'loc_bairro', 'loc_cidade', 'loc_estado',
            ])])
            ->orderBy('fk_local_id')
            ->orderBy('mor_nome')
            ->get();

        $fp = fopen('php://temp', 'r+');
        fwrite($fp, "\xEF\xBB\xBF");

        $csvLgpd = config('visitaai_municipio.lgpd', []);
        fputcsv($fp, [$csvLgpd['csv_secao_titulo'] ?? 'AVISO_LEGISLACAO_FEDERAL_EXPORTACAO']);
        foreach ($csvLgpd['csv_aviso_linhas'] ?? [] as $line) {
            fputcsv($fp, [$line]);
        }
        fputcsv($fp, ['Exportação detalhada de ocupantes: uso interno da gestão municipal.']);
        fputcsv($fp, ['Documentos pessoais (RG/CPF) não são exportados por padrão.']);
        fputcsv($fp, []);

        fputcsv($fp, ['Visita Aí: cadastro socioeconômico - ocupantes']);
        fputcsv($fp, ['gerado_em', now()->toIso8601String()]);
        fputcsv($fp, ['total_ocupantes', $moradores->count()]);
        fputcsv($fp, []);

        fputcsv($fp, [
            'codigo_imovel',
            'endereco',
            'numero',
            'bairro',
            'cidade',
            'estado',
            'nome_ocupante',
            'referencia_familiar',
            'parentesco',
            'sexo',
            'data_nascimento',
            'idade_anos',
            'estado_civil',
            'escolaridade',
            'profissao',
            'renda_faixa',
            'renda_formal_informal',
            'situacao_trabalho',
            'cor_raca',
            'naturalidade',
            'telefone',
            'observacao',
        ]);

        foreach ($moradores as $m) {
            $local = $m->local;
            fputcsv($fp, [
                (string) ($local?->loc_codigo_unico ?? ''),
                (string) ($local?->loc_endereco ?? ''),
                (string) ($local?->loc_numero ?? ''),
                (string) ($local?->loc_bairro ?? ''),
                (string) ($local?->loc_cidade ?? ''),
                (string) ($local?->loc_estado ?? ''),
                (string) ($m->mor_nome ?? ''),
                $m->mor_referencia_familiar ? 'Sim' : 'Nao',
                SE::opcao('parentesco_opcoes', $m->mor_parentesco),
                SE::opcao('sexo_opcoes', $m->mor_sexo),
                $m->mor_data_nascimento?->format('Y-m-d') ?? '',
                $m->idadeAnos() ?? '',
                SE::opcao('estado_civil_opcoes', $m->mor_estado_civil),
                SE::municipioEscolaridade($m->mor_escolaridade),
                (string) ($m->mor_profissao ?? ''),
                SE::municipioRenda($m->mor_renda_faixa),
                SE::opcao('renda_formal_informal_opcoes', $m->mor_renda_formal_informal),
                SE::municipioTrabalho($m->mor_situacao_trabalho),
                SE::municipioCor($m->mor_cor_raca),
                (string) ($m->mor_naturalidade ?? ''),
                (string) ($m->mor_telefone ?? ''),
                (string) ($m->mor_observacao ?? ''),
            ]);
        }

        rewind($fp);

        return stream_get_contents($fp) ?: '';
    }

    /**
     * Dados detalhados para renderização de PDF de cadastro socioeconômico por imóvel.
     *
     * @return Collection<int, Local>
     */
    public function dadosCadastroOcupantesGestor(): Collection
    {
        return Local::query()
            ->whereHas('moradores')
            ->with([
                'socioeconomico',
                'moradores' => fn ($q) => $q->orderBy('mor_nome')->orderBy('mor_id'),
            ])
            ->orderBy('loc_bairro')
            ->orderBy('loc_endereco')
            ->orderBy('loc_numero')
            ->get();
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
                'mor_cor_raca', 'mor_situacao_trabalho', 'mor_sexo', 'mor_estado_civil', 'mor_parentesco',
                'mor_renda_formal_informal', 'mor_referencia_familiar',
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
     *     com_sexo_informado: int,
     *     com_estado_civil_informado: int,
     *     com_parentesco_informado: int,
     *     com_renda_formal_informal_informada: int,
     *     pct_data_nascimento: int,
     *     pct_escolaridade_informada: int,
     *     pct_renda_informada: int,
     *     pct_cor_raca_informada: int,
     *     pct_situacao_trabalho_informada: int,
     *     pct_sexo_informado: int,
     *     pct_estado_civil_informado: int,
     *     pct_parentesco_informado: int,
     *     pct_renda_formal_informal_informada: int
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
                'com_sexo_informado' => 0,
                'com_estado_civil_informado' => 0,
                'com_parentesco_informado' => 0,
                'com_renda_formal_informal_informada' => 0,
                'pct_data_nascimento' => 0,
                'pct_escolaridade_informada' => 0,
                'pct_renda_informada' => 0,
                'pct_cor_raca_informada' => 0,
                'pct_situacao_trabalho_informada' => 0,
                'pct_sexo_informado' => 0,
                'pct_estado_civil_informado' => 0,
                'pct_parentesco_informado' => 0,
                'pct_renda_formal_informal_informada' => 0,
            ];
        }

        $comDn = 0;
        $comEsc = 0;
        $comRenda = 0;
        $comCor = 0;
        $comTrab = 0;
        $comSexo = 0;
        $comEc = 0;
        $comPar = 0;
        $comRfi = 0;
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
            $sx = (string) ($m->mor_sexo ?? '');
            if ($sx !== '' && $sx !== 'nao_informado') {
                $comSexo++;
            }
            $ec = (string) ($m->mor_estado_civil ?? '');
            if ($ec !== '' && $ec !== 'nao_informado') {
                $comEc++;
            }
            $par = (string) ($m->mor_parentesco ?? '');
            if ($par !== '' && $par !== 'nao_informado') {
                $comPar++;
            }
            $rfi = (string) ($m->mor_renda_formal_informal ?? '');
            if ($rfi !== '' && $rfi !== 'nao_informado') {
                $comRfi++;
            }
        }

        return [
            'total' => $t,
            'com_data_nascimento' => $comDn,
            'com_escolaridade_informada' => $comEsc,
            'com_renda_informada' => $comRenda,
            'com_cor_raca_informada' => $comCor,
            'com_situacao_trabalho_informada' => $comTrab,
            'com_sexo_informado' => $comSexo,
            'com_estado_civil_informado' => $comEc,
            'com_parentesco_informado' => $comPar,
            'com_renda_formal_informal_informada' => $comRfi,
            'pct_data_nascimento' => (int) round(100 * $comDn / $t),
            'pct_escolaridade_informada' => (int) round(100 * $comEsc / $t),
            'pct_renda_informada' => (int) round(100 * $comRenda / $t),
            'pct_cor_raca_informada' => (int) round(100 * $comCor / $t),
            'pct_situacao_trabalho_informada' => (int) round(100 * $comTrab / $t),
            'pct_sexo_informado' => (int) round(100 * $comSexo / $t),
            'pct_estado_civil_informado' => (int) round(100 * $comEc / $t),
            'pct_parentesco_informado' => (int) round(100 * $comPar / $t),
            'pct_renda_formal_informal_informada' => (int) round(100 * $comRfi / $t),
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
