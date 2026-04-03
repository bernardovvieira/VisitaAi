<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Visita;
use App\Models\Local;
use App\Models\Doenca;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use App\Helpers\LogHelper;

class RelatorioController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     */
    public function index(Request $request)
    {
        $request->validate([
            'tipo_relatorio' => ['nullable', 'string', 'in:completo,individual,diario,semanal'],
            'local_id'       => ['nullable'],
        'local_id.*'     => ['integer', 'exists:locais,loc_id'],
            'data_unica'     => ['nullable', 'date'],
            'data_inicio'    => ['nullable', 'date'],
            'data_fim'       => ['nullable', 'date', 'after_or_equal:data_inicio'],
            'bairro'         => ['nullable'],
        ]);
        $bairroInput = $request->input('bairro');
        if (is_string($bairroInput)) {
            $bairroInput = $bairroInput !== '' ? [$bairroInput] : [];
        }
        $bairroInput = is_array($bairroInput) ? array_filter($bairroInput) : [];

        $tipo = $request->input('tipo_relatorio', 'completo');

        $query = Visita::with(['local', 'doencas', 'usuario', 'tratamentos']);

        $localIds = array_filter(array_map('intval', (array) $request->input('local_id', [])));
        if ($tipo === 'individual' && !empty($localIds)) {
            $query->whereIn('fk_local_id', $localIds);
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

        if (!empty($bairroInput)) {
            $query->whereHas('local', function ($q) use ($bairroInput) {
                $q->whereIn('loc_bairro', $bairroInput);
            });
        }

        $visitasPaginated = (clone $query)->orderBy('vis_data', 'desc')->orderBy('vis_id', 'desc')->paginate(15)->withQueryString();
        $visitas = $query->get();
        $filtrosAplicados = $request->hasAny(['data_inicio', 'data_fim', 'data_unica']) || !empty($bairroInput) || !empty($localIds);

        if ($visitas->isEmpty() && $filtrosAplicados) {
            return redirect()->route('gestor.relatorios.index')->with([
                'error' => 'Nenhuma visita encontrada para os filtros aplicados.',
                'limpar_filtros' => true,
            ]);
        }

        $sem_visitas = $visitas->isEmpty();

        $visitasParaGraficos = $visitas->map(function ($v) {
            return [
                'vis_data' => $v->vis_data,
                'local' => $v->local ? [
                    'loc_bairro' => $v->local->loc_bairro,
                    'loc_latitude' => $v->local->loc_latitude,
                    'loc_longitude' => $v->local->loc_longitude,
                ] : null,
                'doencas' => $v->doencas->map(fn ($d) => ['doe_nome' => $d->doe_nome])->values()->toArray(),
                'tratamentos' => $v->tratamentos->map(fn ($t) => ['trat_forma' => $t->trat_forma])->values()->toArray(),
            ];
        })->values()->toArray();

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

        $bairros = Local::select('loc_bairro')->distinct()->whereNotNull('loc_bairro')->orderBy('loc_bairro')->pluck('loc_bairro')->map(fn ($b) => trim((string) $b))->filter(fn ($b) => $b !== '')->values()->toArray();
        $locaisParaSelect = Local::has('visitas')->withCount('visitas')->orderBy('loc_bairro')->orderBy('loc_endereco')->orderBy('loc_numero')->get();
        $locaisParaSelectArray = $locaisParaSelect->map(function ($loc) {
            $endereco = trim(($loc->loc_endereco ?? '') . ($loc->loc_numero ? ', ' . $loc->loc_numero : ''));
            $codigo = $loc->loc_codigo_unico ?? '-';
            $bairro = $loc->loc_bairro ?? '-';
            $qtd = $loc->visitas_count ?? 0;
            $label = ($endereco ?: '-') . ', ' . $bairro . ', Cód. ' . $codigo . ($qtd > 0 ? ' (' . $qtd . ' visita' . ($qtd !== 1 ? 's' : '') . ')' : '');
            return ['id' => $loc->loc_id, 'label' => $label];
        })->values()->toArray();

        return view('gestor.relatorios.index', compact(
            'visitas',
            'visitasPaginated',
            'locaisParaSelect',
            'locaisParaSelectArray',
            'bairros',
            'visitasParaGraficos',
            'sem_visitas',
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

    /**
     * @param \Illuminate\Http\Request $request
     */
    public function gerarPdf(Request $request)
    {
        $tipo = $request->input('tipo_relatorio', 'completo');

        $request->validate([
            'tipo_relatorio' => ['nullable', 'string', 'in:completo,individual,diario,semanal'],
            'local_id'       => ['nullable'],
            'local_id.*'   => ['integer', 'exists:locais,loc_id'],
            'data_unica'     => ['nullable', 'date', Rule::requiredIf($tipo === 'diario')],
            'data_inicio'    => ['nullable', 'date', Rule::requiredIf($tipo === 'semanal')],
            'data_fim'       => ['nullable', 'date', 'after_or_equal:data_inicio', Rule::requiredIf($tipo === 'semanal')],
            'bairro'         => ['nullable'],
        ], [
            'data_unica.required_if' => 'Informe a data para o relatório diário.',
            'data_inicio.required_if' => 'Informe a data de início para o relatório por período.',
            'data_fim.required_if' => 'Informe a data de fim para o relatório por período.',
        ]);

        if (Visita::count() === 0) {
            return redirect()->route('gestor.relatorios.index')->with('error', 'Não há visitas cadastradas no sistema. Cadastre visitas para gerar relatórios.');
        }
        $bairroPdf = $request->input('bairro');
        $bairrosPdf = is_array($bairroPdf) ? array_filter($bairroPdf) : ($bairroPdf !== null && $bairroPdf !== '' ? [$bairroPdf] : []);

        $query = Visita::with(['local', 'doencas', 'usuario', 'tratamentos']);

        $localIdsPdf = array_filter(array_map('intval', (array) $request->input('local_id', [])));
        if ($tipo === 'individual' && empty($localIdsPdf)) {
            return redirect()->route('gestor.relatorios.index')->with('error', 'Selecione ao menos um local para o relatório individual.');
        }
        if ($tipo === 'individual' && !empty($localIdsPdf)) {
            $query->whereIn('fk_local_id', $localIdsPdf)->orderBy('vis_data', 'desc');
            $visitas = $query->get();
            $data_inicio = $visitas->min('vis_data') ?? now()->toDateString();
            $data_fim = $visitas->max('vis_data') ?? now()->toDateString();
        } else {
            if ($tipo === 'diario' && $request->filled('data_unica')) {
                $data_inicio = $data_fim = $request->data_unica;
                $query->whereDate('vis_data', $data_inicio);
            } else {
                $data_inicio = $request->data_inicio ?? Visita::min('vis_data');
                $data_fim = $request->data_fim ?? now()->toDateString();
                $query->whereBetween('vis_data', [$data_inicio, $data_fim]);
            }

            if (!empty($bairrosPdf)) {
                $query->whereHas('local', function ($q) use ($bairrosPdf) {
                    $q->whereIn('loc_bairro', $bairrosPdf);
                });
            }

            $visitas = $query->orderBy('vis_data', 'desc')->get();
        }

        if ($visitas->isEmpty()) {
            return redirect()->route('gestor.relatorios.index')->with('error', 'Nenhuma visita encontrada para os critérios selecionados. Não foi possível gerar o PDF.');
        }

        $base64Keys = [
            'graficoBairrosBase64', 'graficoDoencasBase64', 'mapaCalorBase64',
            'graficoZonasBase64', 'graficoDiasBase64', 'graficoInspBase64', 'graficoTratamentosBase64',
        ];
        $maxBase64Len = 2800000; // ~2MB em base64
        $sanitizedBase64 = [];
        foreach ($base64Keys as $key) {
            $val = $request->input($key);
            if ($val === null || $val === '') {
                $sanitizedBase64[$key] = null;
                continue;
            }
            $val = (string) $val;
            if (strlen($val) > $maxBase64Len || !preg_match('/^[A-Za-z0-9+\/=]+$/', $val)) {
                $sanitizedBase64[$key] = null;
                continue;
            }
            $sanitizedBase64[$key] = $val;
        }

        $graficoBairrosBase64 = $sanitizedBase64['graficoBairrosBase64'];
        $graficoDoencasBase64 = $sanitizedBase64['graficoDoencasBase64'];
        $mapaCalorBase64 = $sanitizedBase64['mapaCalorBase64'];
        $graficoZonasBase64 = $sanitizedBase64['graficoZonasBase64'];
        $graficoDiasBase64 = $sanitizedBase64['graficoDiasBase64'];
        $graficoInspBase64 = $sanitizedBase64['graficoInspBase64'];
        $graficoTratamentosBase64 = $sanitizedBase64['graficoTratamentosBase64'];

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
            'individual' => 'Relatório Individual',
            'diario'     => 'Relatório Diário',
            'semanal'    => 'Relatório Semanal',
            default      => 'Relatório Completo'
        };

        if ($tipo === 'individual') {
            $locaisSel = !empty($localIdsPdf) ? Local::whereIn('loc_id', $localIdsPdf)->get() : collect();
            $periodo = $locaisSel->isEmpty() ? 'Local(is)' : 'Local(is): ' . $locaisSel->map(fn ($l) => trim(($l->loc_endereco ?? '') . ($l->loc_numero ? ', ' . $l->loc_numero : '') . ', ' . ($l->loc_bairro ?? '')))->join(' | ');
        } elseif ($tipo === 'diario') {
            $periodo = 'Data: ' . $data_inicio;
        } elseif ($tipo === 'semanal') {
            $periodo = 'Período: ' . $data_inicio . ' até ' . $data_fim;
        } else {
            $periodo = 'Período: ' . $data_inicio . ' até ' . $data_fim;
        }

        $descricao = $titulo . ' - ' . $periodo;
        if (!empty($bairrosPdf) && $tipo !== 'individual') {
            $descricao .= ' - Bairros: ' . implode(', ', $bairrosPdf);
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
            'bairrosPdf',
            'titulo',
            'tipo'
        ))->setPaper('a4', 'landscape')->stream('relatorio-visitas.pdf');
    } 
}