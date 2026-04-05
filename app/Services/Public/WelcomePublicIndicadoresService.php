<?php

namespace App\Services\Public;

use App\Models\Local;
use App\Models\Visita;
use Illuminate\Support\Carbon;

/**
 * Indicadores numéricos agregados para a página inicial pública.
 * Apenas totais operacionais — sem perfis de ocupantes, bairros nomeados ou CSV.
 */
class WelcomePublicIndicadoresService
{
    /**
     * @return array{
     *     visitas_total: int,
     *     imoveis_cadastrados: int,
     *     imoveis_com_visita: int,
     *     bairros_com_visita: int|null,
     *     depositos_eliminados_total: int,
     *     ultima_visita: Carbon|null,
     *     suprimir_detalhes: bool,
     *     indicadores_habilitados: bool,
     *     ocultar_valores_zero: bool,
     * }
     */
    public function resumo(): array
    {
        $cfg = config('visitaai_municipio.welcome_public', []);
        $indicadoresHabilitados = (bool) ($cfg['indicadores_habilitados'] ?? true);
        $minVisitasBairros = max(0, (int) ($cfg['min_visitas_para_exibir_bairros'] ?? 5));
        $ocultarZeros = (bool) ($cfg['ocultar_valores_zero'] ?? false);

        if (! $indicadoresHabilitados) {
            return [
                'visitas_total' => 0,
                'imoveis_cadastrados' => 0,
                'imoveis_com_visita' => 0,
                'bairros_com_visita' => null,
                'depositos_eliminados_total' => 0,
                'ultima_visita' => null,
                'suprimir_detalhes' => true,
                'indicadores_habilitados' => false,
                'ocultar_valores_zero' => $ocultarZeros,
            ];
        }

        $primarioId = Local::query()->orderBy('loc_id')->value('loc_id');

        $visitasTotal = (int) Visita::query()->count();

        $imoveisCadastrados = (int) Local::query()
            ->when($primarioId, fn ($q) => $q->where('loc_id', '!=', $primarioId))
            ->count();

        $imoveisComVisita = (int) Local::query()
            ->has('visitas')
            ->when($primarioId, fn ($q) => $q->where('loc_id', '!=', $primarioId))
            ->count();

        $depositos = (int) Visita::query()->sum('vis_depositos_eliminados');

        $ultima = Visita::query()->max('vis_data');
        $ultimaVisita = $ultima ? Carbon::parse($ultima) : null;

        $suprimir = $visitasTotal < $minVisitasBairros;

        $bairrosComVisita = null;
        if (! $suprimir) {
            $bairrosComVisita = (int) Local::query()
                ->has('visitas')
                ->when($primarioId, fn ($q) => $q->where('loc_id', '!=', $primarioId))
                ->whereNotNull('loc_bairro')
                ->whereRaw("TRIM(loc_bairro) <> ''")
                ->distinct()
                ->count('loc_bairro');
        }

        return [
            'visitas_total' => $visitasTotal,
            'imoveis_cadastrados' => $imoveisCadastrados,
            'imoveis_com_visita' => $imoveisComVisita,
            'bairros_com_visita' => $bairrosComVisita,
            'depositos_eliminados_total' => $depositos,
            'ultima_visita' => $ultimaVisita,
            'suprimir_detalhes' => $suprimir,
            'indicadores_habilitados' => true,
            'ocultar_valores_zero' => $ocultarZeros,
        ];
    }
}
