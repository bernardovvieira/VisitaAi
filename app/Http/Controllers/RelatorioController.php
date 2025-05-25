<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visita;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class RelatorioController extends Controller
{
    public function index(Request $request)
    {
        $query = Visita::with(['local', 'doencas', 'usuario']);

        if ($request->filled('data_inicio')) {
            $query->whereDate('vis_data', '>=', $request->data_inicio);
        }

        if ($request->filled('data_fim')) {
            $query->whereDate('vis_data', '<=', $request->data_fim);
        }

        if ($request->filled('bairro')) {
            $query->whereHas('local', function ($q) use ($request) {
                $q->where('loc_bairro', 'like', '%' . $request->bairro . '%');
            });
        }

        $visitas = $query->get();

        if ($request->hasAny(['data_inicio', 'data_fim', 'bairro']) && $visitas->isEmpty()) {
            return redirect()
                ->route('gestor.relatorios.index')
                ->with([
                    'error' => 'nenhuma visita encontrada para os filtros aplicados.',
                    'limpar_filtros' => true,
                ]);
        }

        $totalVisitas = $visitas->count();
        $locaisComFoco = $visitas->pluck('local.loc_codigo_unico')->unique()->count();

        $doencaMaisRecorrente = $visitas->flatMap->doencas
            ->groupBy('doe_nome')->sortDesc()->map->count()->keys()->first();

        $bairroMaisFrequente = $visitas->groupBy(fn($v) => $v->local->loc_bairro ?? '')
            ->sortDesc()->map->count()->keys()->first();

        return view('gestor.relatorios.index', compact(
            'visitas',
            'totalVisitas',
            'locaisComFoco',
            'doencaMaisRecorrente',
            'bairroMaisFrequente'
        ));
    }

public function gerarPdf(Request $request)
{
    // Definir datas padrão
    $data_inicio = $request->data_inicio ?? Visita::min('vis_data');
    $data_fim = $request->data_fim ?? now()->format('Y-m-d');

    $visitas = Visita::with(['local', 'doencas', 'usuario'])
        ->whereDate('vis_data', '>=', $data_inicio)
        ->whereDate('vis_data', '<=', $data_fim)
        ->when($request->filled('bairro'), function ($q) use ($request) {
            $q->whereHas('local', function ($q2) use ($request) {
                $q2->where('loc_bairro', 'like', '%' . $request->bairro . '%');
            });
        })
        ->orderBy('vis_data', 'desc')
        ->get();

    $graficoBairrosBase64 = $request->input('graficoBairrosBase64');
    $graficoDoencasBase64 = $request->input('graficoDoencasBase64');
    $mapaCalorBase64 = $request->input('mapaCalorBase64');

    $doencaMaisFrequente = $visitas->flatMap->doencas
        ->countBy('doe_nome')
        ->sortDesc()
        ->map(fn($qtd, $nome) => ['nome' => $nome, 'quantidade' => $qtd])
        ->values()
        ->first();

    $totalLocaisVisitados = $visitas->pluck('local.loc_codigo_unico')->unique()->count();
    $doencasDetectadas = $visitas->flatMap->doencas->unique('doe_id');
    $gestorNome = Auth::user()->use_nome ?? 'Gestor';
    $bairro = $request->bairro;

    return Pdf::loadView('gestor.relatorios.pdf', compact(
        'visitas',
        'graficoBairrosBase64',
        'graficoDoencasBase64',
        'mapaCalorBase64',
        'doencaMaisFrequente',
        'totalLocaisVisitados',
        'doencasDetectadas',
        'gestorNome',
        'data_inicio',
        'data_fim',
        'bairro'
    ))->stream('relatorio-visitas.pdf');
}

}