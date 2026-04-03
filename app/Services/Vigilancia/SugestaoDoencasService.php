<?php

namespace App\Services\Vigilancia;

use App\Models\Doenca;
use App\Models\Visita;
use Illuminate\Support\Facades\DB;

/**
 * Sugestões de doenças com base exclusivamente nos dados do sistema:
 * histórico do local, frequência no município e correspondência com as observações.
 * Não utiliza serviços externos nem cotas de IA.
 */
class SugestaoDoencasService
{
    /** Número máximo de sugestões retornadas */
    public const MAX_SUGESTOES = 5;

    /** Meses para considerar visitas recentes no município */
    public const MESES_FREQUENCIA = 3;

    /**
     * Retorna sugestões de doenças (doe_id) ordenadas por relevância.
     *
     * @param  int|null  $localId  ID do local (opcional)
     * @param  string  $observacoes  Texto das observações da visita
     * @return array<int, string> [doe_id => motivo legível]
     */
    public function sugerir(?int $localId, string $observacoes = ''): array
    {
        $scores = []; // doe_id => pontuação
        $motivos = []; // doe_id => motivo (para exibição)

        $observacoesNorm = $this->normalizarTexto($observacoes);

        // 1) Histórico do local: doenças já detectadas neste imóvel (mais recentes = mais peso)
        if ($localId) {
            $doencasNoLocal = DB::table('monitoradas')
                ->join('visitas', 'visitas.vis_id', '=', 'monitoradas.fk_visita_id')
                ->where('visitas.fk_local_id', $localId)
                ->select('monitoradas.fk_doenca_id', 'visitas.vis_data')
                ->orderByDesc('visitas.vis_data')
                ->limit(50)
                ->get();

            $pos = 0;
            foreach ($doencasNoLocal as $row) {
                $id = (int) $row->fk_doenca_id;
                $peso = max(1, 10 - (int) floor($pos / 5)); // primeiras visitas têm mais peso
                $scores[$id] = ($scores[$id] ?? 0) + $peso;
                $motivos[$id] = 'Já registrada em visitas anteriores neste imóvel.';
                $pos++;
            }
        }

        // 2) Frequência no município (últimos N meses)
        $doencasFrequentes = DB::table('monitoradas')
            ->join('visitas', 'visitas.vis_id', '=', 'monitoradas.fk_visita_id')
            ->where('visitas.vis_data', '>=', now()->subMonths(self::MESES_FREQUENCIA))
            ->select('monitoradas.fk_doenca_id', DB::raw('COUNT(*) as total'))
            ->groupBy('monitoradas.fk_doenca_id')
            ->orderByDesc('total')
            ->limit(20)
            ->pluck('total', 'fk_doenca_id');

        foreach ($doencasFrequentes as $id => $total) {
            $id = (int) $id;
            $scores[$id] = ($scores[$id] ?? 0) + (int) min($total, 5);
            if (! isset($motivos[$id])) {
                $motivos[$id] = 'Frequente nas visitas do município nos últimos '.self::MESES_FREQUENCIA.' meses.';
            }
        }

        // 3) Correspondência com observações: palavras em comum com nome/sintomas das doenças
        if ($observacoesNorm !== '') {
            $palavras = $this->extrairPalavras($observacoesNorm);
            $doencas = Doenca::all();

            foreach ($doencas as $doenca) {
                $textoDoenca = $this->normalizarTexto(
                    $doenca->doe_nome.' '.
                    implode(' ', (array) $doenca->doe_sintomas).' '.
                    implode(' ', (array) $doenca->doe_transmissao)
                );
                $match = 0;
                foreach ($palavras as $p) {
                    if (strlen($p) >= 3 && str_contains($textoDoenca, $p)) {
                        $match++;
                    }
                }
                if ($match > 0) {
                    $id = (int) $doenca->doe_id;
                    $scores[$id] = ($scores[$id] ?? 0) + ($match * 2);
                    if (! isset($motivos[$id])) {
                        $motivos[$id] = 'Relacionada às palavras das suas observações.';
                    }
                }
            }
        }

        arsort($scores, SORT_NUMERIC);
        $ordenados = array_slice(array_keys($scores), 0, self::MAX_SUGESTOES, true);

        $resultado = [];
        foreach ($ordenados as $doeId) {
            $resultado[$doeId] = $motivos[$doeId] ?? 'Sugerida com base nos dados do sistema.';
        }

        return $resultado;
    }

    private function normalizarTexto(string $texto): string
    {
        $t = mb_strtolower($texto, 'UTF-8');
        $t = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $t);

        return preg_replace('/\s+/', ' ', trim($t));
    }

    /** @return array<string> */
    private function extrairPalavras(string $texto): array
    {
        $palavras = explode(' ', $texto);

        return array_values(array_filter(array_unique($palavras), fn ($p) => strlen($p) >= 2));
    }
}
