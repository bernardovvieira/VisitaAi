<?php

namespace App\Services\Municipio;

use App\Models\Local;
use App\Models\Morador;
use Illuminate\Support\Collection;

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
}
