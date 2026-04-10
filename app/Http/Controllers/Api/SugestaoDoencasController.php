<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Doenca;
use App\Services\Vigilancia\SugestaoDoencasService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * API interna para sugestões de doenças com base nos dados do sistema.
 * Não utiliza serviços externos nem cotas de IA.
 */
class SugestaoDoencasController extends Controller
{
    public function __invoke(Request $request, SugestaoDoencasService $service)
    {
        try {
            $localId = $request->filled('local_id') ? (int) $request->input('local_id') : null;
            $observacoes = (string) $request->input('observacoes', '');

            $sugestoes = $service->sugerir($localId, $observacoes);

            if (empty($sugestoes)) {
                return response()->json(['sugestoes' => [], 'doencas' => []]);
            }

            $doencas = Doenca::whereIn('doe_id', array_keys($sugestoes))->get()->keyBy('doe_id');

            $itens = [];
            foreach ($sugestoes as $doeId => $motivo) {
                $d = $doencas->get($doeId);
                if ($d) {
                    $itens[] = [
                        'doe_id' => (int) $d->doe_id,
                        'nome' => $d->doe_nome,
                        'motivo' => $motivo,
                    ];
                }
            }

            return response()->json([
                'sugestoes' => $itens,
                'doencas' => $doencas->values()->toArray(),
            ]);
        } catch (\Throwable $e) {
            Log::warning('SugestaoDoencas: '.$e->getMessage());

            return response()->json([
                'sugestoes' => [],
                'doencas' => [],
                'message' => 'Não foi possível carregar sugestões. Tente novamente.',
            ], 500);
        }
    }
}
