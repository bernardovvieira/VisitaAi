<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visita;
use Barryvdh\DomPDF\Facade\Pdf;

class RelatorioController extends Controller
{
    /**
     * Exibe a lista de visitas para o gestor, com filtros e análises.
     */
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

        // Indicadores
        $totalVisitas = $visitas->count();

        $locaisComFoco = $visitas->pluck('local.loc_codigo_unico')->unique()->count();

        $doencaMaisRecorrente = $visitas->flatMap->doencas
            ->groupBy('doe_nome')->sortDesc()->map->count()->keys()->first();

        $bairroMaisFrequente = $visitas->groupBy(fn($v) => $v->local->loc_bairro ?? '')->sortDesc()->map->count()->keys()->first();

        return view('gestor.relatorios.index', compact(
            'visitas',
            'totalVisitas',
            'locaisComFoco',
            'doencaMaisRecorrente',
            'bairroMaisFrequente'
        ));
    }

    /**
     * Gera um PDF com os dados filtrados.
     */
    public function gerarPdf(Request $request)
    {
        $visitas = Visita::with(['local', 'doencas', 'usuario'])
            ->when($request->filled('data_inicio'), fn($q) => $q->whereDate('vis_data', '>=', $request->data_inicio))
            ->when($request->filled('data_fim'), fn($q) => $q->whereDate('vis_data', '<=', $request->data_fim))
            ->orderBy('vis_data', 'desc')
            ->get();

        $graficoBairrosBase64 = $request->input('graficoBairrosBase64');
        $graficoDoencasBase64 = $request->input('graficoDoencasBase64');
        $mapaCalorBase64 = $request->input('mapaCalorBase64');

        $pdf = Pdf::loadView('gestor.relatorios.pdf', compact(
            'visitas',
            'graficoBairrosBase64',
            'graficoDoencasBase64',
            'mapaCalorBase64'
        ));

        return $pdf->stream('relatorio-visitas.pdf');
    }
}