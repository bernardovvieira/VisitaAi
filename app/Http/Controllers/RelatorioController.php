<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visita;
use App\Models\Local;
use App\Models\Doenca;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use App\Helpers\LogHelper;

class RelatorioController extends Controller
{
    public function index(Request $request)
    {
        $tipo = $request->input('tipo_relatorio', 'completo');

        $query = Visita::with(['local', 'doencas', 'usuario', 'tratamentos']);

        if ($tipo === 'individual' && $request->filled('visita_id')) {
            $query->where('vis_id', $request->visita_id);
        } elseif ($tipo === 'diario' && $request->filled('data_unica')) {
            $query->whereDate('vis_data', $request->data_unica);
        } elseif ($tipo === 'semanal') {
            if ($request->filled('data_inicio')) {
                $query->whereDate('vis_data', '>=', $request->data_inicio);
            }
            if ($request->filled('data_fim')) {
                $query->whereDate('vis_data', '<=', $request->data_fim);
            }
        } elseif ($tipo === 'completo') {
            if ($request->filled('data_inicio')) {
                $query->whereDate('vis_data', '>=', $request->data_inicio);
            }
            if ($request->filled('data_fim')) {
                $query->whereDate('vis_data', '<=', $request->data_fim);
            }
        }

        if ($request->filled('bairro')) {
            $query->whereHas('local', function ($q) use ($request) {
                $q->where('loc_bairro', 'like', '%' . $request->bairro . '%');
            });
        }

        $visitas = $query->get();

        if ($request->hasAny(['data_inicio', 'data_fim', 'bairro', 'visita_id', 'data_unica']) && $visitas->isEmpty()) {
            return redirect()->route('gestor.relatorios.index')->with([
                'error' => 'Nenhuma visita encontrada para os filtros aplicados.',
                'limpar_filtros' => true,
            ]);
        }

        $totalVisitas = $visitas->count();
        $locaisComFoco = $visitas->filter(function ($visita) {
            return (
                ($visita->insp_a1 > 0 || $visita->insp_a2 > 0 || $visita->insp_b > 0 ||
                 $visita->insp_c > 0 || $visita->insp_d1 > 0 || $visita->insp_d2 > 0 ||
                 $visita->insp_e > 0) &&
                ($visita->vis_depositos_eliminados < ($visita->insp_a1 + $visita->insp_a2 +
                    $visita->insp_b + $visita->insp_c + $visita->insp_d1 + $visita->insp_d2 +
                    $visita->insp_e))
            );
        })->count();

        $totalComPendencia = $visitas->where('vis_pendencias', true)->count();
        $percentualPendencias = $totalVisitas > 0 ? round(($totalComPendencia / $totalVisitas) * 100, 1) : 0;

        $totalComColeta = $visitas->where('vis_coleta_amostra', true)->count();
        $mediaTubitos = $visitas->avg('vis_qtd_tubitos') ?? 0;

        $doencaMaisRecorrente = $visitas->flatMap->doencas
            ->groupBy('doe_nome')->sortDesc()->map->count()->keys()->first();

        $bairroMaisFrequente = $visitas->groupBy(fn($v) => $v->local->loc_bairro ?? '')
            ->sortDesc()->map->count()->keys()->first();

        $totalLocaisCadastrados = Local::count();
        $visitasComTratamento = $visitas->filter(fn($v) => $v->tratamentos->isNotEmpty())->count();
        $totalDepEliminados = $visitas->sum('vis_depositos_eliminados');

        $totalTratamentos = $visitas->flatMap->tratamentos->count();
        $mediaTratamentosPorVisita = $totalVisitas > 0 ? $totalTratamentos / $totalVisitas : 0;

        return view('gestor.relatorios.index', compact(
            'visitas',
            'totalVisitas',
            'locaisComFoco',
            'doencaMaisRecorrente',
            'bairroMaisFrequente',
            'totalComPendencia',
            'percentualPendencias',
            'totalComColeta',
            'mediaTubitos',
            'totalLocaisCadastrados',
            'visitasComTratamento',
            'totalDepEliminados',
            'mediaTratamentosPorVisita'
        ));
    }

    public function gerarPdf(Request $request)
    {
        $tipo = $request->input('tipo_relatorio', 'completo');
        $bairro = $request->input('bairro');

        $query = Visita::with(['local', 'doencas', 'usuario', 'tratamentos']);

        if ($tipo === 'individual' && $request->filled('visita_id')) {
            $query->where('vis_id', $request->visita_id);
            $visitas = $query->get();
            $data_inicio = $data_fim = optional($visitas->first())->vis_data ?? now()->toDateString();
        } else {
            if ($tipo === 'diario' && $request->filled('data_unica')) {
                $data_inicio = $data_fim = $request->data_unica;
                $query->whereDate('vis_data', $data_inicio);
            } else {
                $data_inicio = $request->data_inicio ?? Visita::min('vis_data');
                $data_fim = $request->data_fim ?? now()->toDateString();
                $query->whereBetween('vis_data', [$data_inicio, $data_fim]);
            }

            if ($bairro) {
                $query->whereHas('local', function ($q) use ($bairro) {
                    $q->where('loc_bairro', 'like', '%' . $bairro . '%');
                });
            }

            $visitas = $query->orderBy('vis_data', 'desc')->get();
        }

        $graficoBairrosBase64 = $request->input('graficoBairrosBase64');
        $graficoDoencasBase64 = $request->input('graficoDoencasBase64');
        $mapaCalorBase64 = $request->input('mapaCalorBase64');
        $graficoZonasBase64 = $request->input('graficoZonasBase64');
        $graficoDiasBase64 = $request->input('graficoDiasBase64');
        $graficoInspBase64 = $request->input('graficoInspBase64');
        $graficoTratamentosBase64 = $request->input('graficoTratamentosBase64');

        $doencaMaisFrequente = $visitas->flatMap->doencas
            ->countBy('doe_nome')
            ->sortDesc()
            ->map(fn($qtd, $nome) => ['nome' => $nome, 'quantidade' => $qtd])
            ->values()
            ->first();

        $totalLocaisVisitados = $visitas->pluck('local.loc_codigo_unico')->unique()->count();
        $doencasDetectadas = Doenca::all();
        $gestorNome = Auth::user()->use_nome ?? 'Gestor';

        $titulo = match ($tipo) {
            'individual' => 'Relatório de Visita Individual',
            'diario'     => 'Relatório Diário',
            'semanal'    => 'Relatório Semanal',
            default      => 'Relatório Completo'
        };

        $descricao = $titulo . ' - Período: ' . $data_inicio . ' até ' . $data_fim;
        if ($bairro && $tipo !== 'individual') {
            $descricao .= ' - Bairro: ' . $bairro;
        }

        LogHelper::registrar(
            'Geração de relatório',
            'Relatório',
            'export',
            $descricao
        );

        return Pdf::loadView('gestor.relatorios.pdf', compact(
            'visitas',
            'graficoBairrosBase64',
            'graficoDoencasBase64',
            'mapaCalorBase64',
            'graficoZonasBase64',
            'graficoDiasBase64',
            'graficoInspBase64',
            'graficoTratamentosBase64',
            'doencaMaisFrequente',
            'totalLocaisVisitados',
            'doencasDetectadas',
            'gestorNome',
            'data_inicio',
            'data_fim',
            'bairro',
            'titulo'
        ))->stream('relatorio-visitas.pdf');
    }
}