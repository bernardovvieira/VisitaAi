<?php

namespace App\Services\Municipio;

use App\Models\Local;
use App\Models\Morador;
use Illuminate\Support\Collection;

/**
 * Indicadores locais a partir dos registros do próprio Visita Aí (imóvel / ocupantes).
 * Separado de qualquer modelo e-SUS, PEC ou e-SUS Território.
 */
class ResumoOcupantesMunicipioService
{
    /**
     * @return array{total: int, faixas: array<string, int>}
     */
    public function resumoParaLocal(Local $local): array
    {
        $moradores = $local->relationLoaded('moradores')
            ? $local->moradores
            : $local->moradores()->get();

        return [
            'total' => $moradores->count(),
            'faixas' => $this->contagemFaixasEtarias($moradores),
        ];
    }

    /**
     * @param  Collection<int, Morador>  $moradores
     * @return array<string, int>
     */
    public function contagemFaixasEtarias(Collection $moradores): array
    {
        $faixas = [
            '0-11' => 0,
            '12-17' => 0,
            '18-59' => 0,
            '60+' => 0,
            'sem_info' => 0,
        ];

        foreach ($moradores as $m) {
            $idade = $m->idadeAnos();
            if ($idade === null) {
                $faixas['sem_info']++;

                continue;
            }
            if ($idade <= 11) {
                $faixas['0-11']++;
            } elseif ($idade <= 17) {
                $faixas['12-17']++;
            } elseif ($idade <= 59) {
                $faixas['18-59']++;
            } else {
                $faixas['60+']++;
            }
        }

        return $faixas;
    }

    public function totalOcupantesRegistrados(): int
    {
        return Morador::query()->count();
    }

    /**
     * Agregado por bairro (campo loc_bairro do imóvel cadastrado no Visita Aí).
     *
     * @return Collection<int, object{bairro: string, total_moradores: int}>
     */
    public function totaisPorBairro(): Collection
    {
        return Morador::query()
            ->join('locais', 'locais.loc_id', '=', 'moradores.fk_local_id')
            ->selectRaw('TRIM(locais.loc_bairro) as bairro, COUNT(*) as total_moradores')
            ->groupBy('locais.loc_bairro')
            ->orderByDesc('total_moradores')
            ->get();
    }
}
