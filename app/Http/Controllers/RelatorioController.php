<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use App\Models\Local;
use App\Models\Visita;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class RelatorioController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'tipo_relatorio' => ['nullable', 'string', 'in:completo,individual,diario,semanal'],
            'local_id' => ['nullable'],
            'local_id.*' => ['integer', 'exists:locais,loc_id'],
            'data_unica' => ['nullable', 'date'],
            'data_inicio' => ['nullable', 'date'],
            'data_fim' => ['nullable', 'date', 'after_or_equal:data_inicio'],
            'bairro' => ['nullable'],
        ]);
        $bairroInput = $request->input('bairro');
        if (is_string($bairroInput)) {
            $bairroInput = $bairroInput !== '' ? [$bairroInput] : [];
        }
        $bairroInput = is_array($bairroInput) ? array_filter($bairroInput) : [];

        $tipo = $request->input('tipo_relatorio', 'completo');

        $query = Visita::with([
            'local' => fn ($q) => $q->withCount('moradores')->with('socioeconomico'),
            'doencas',
            'usuario',
            'tratamentos',
        ]);

        $localIds = array_filter(array_map('intval', (array) $request->input('local_id', [])));
        if ($tipo === 'individual' && ! empty($localIds)) {
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

        if (! empty($bairroInput)) {
            $query->whereHas('local', function ($q) use ($bairroInput) {
                $q->whereIn('loc_bairro', $bairroInput);
            });
        }

        $visitasPaginated = (clone $query)->orderBy('vis_data', 'desc')->orderBy('vis_id', 'desc')->paginate(15)->withQueryString();
        $visitas = $query->get();
        $filtrosAplicados = $request->hasAny(['data_inicio', 'data_fim', 'data_unica']) || ! empty($bairroInput) || ! empty($localIds);

        if ($visitas->isEmpty() && $filtrosAplicados) {
            return redirect()->route('gestor.relatorios.index')->with([
                'error' => __('Nenhuma visita encontrada para os filtros aplicados.'),
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

        $totalComPendencia = $visitas->where('vis_pendencias', true)->count();
        $percentualPendencias = $totalVisitas > 0 ? round(($totalComPendencia / $totalVisitas) * 100, 1) : 0;

        $totalComColeta = $visitas->where('vis_coleta_amostra', true)->count();

        $bairroMaisFrequente = $visitas->groupBy(fn ($v) => $v->local?->loc_bairro ?? '')
            ->sortDesc()->map->count()->keys()->first();

        $visitasComTratamento = $visitas->filter(fn ($v) => $v->tratamentos->isNotEmpty())->count();
        $totalDepEliminados = $visitas->sum('vis_depositos_eliminados');

        $bairros = Local::select('loc_bairro')->distinct()->whereNotNull('loc_bairro')->orderBy('loc_bairro')->pluck('loc_bairro')->map(fn ($b) => trim((string) $b))->filter(fn ($b) => $b !== '')->values()->toArray();
        $locaisParaSelect = Local::has('visitas')->withCount('visitas')->orderBy('loc_bairro')->orderBy('loc_endereco')->orderBy('loc_numero')->get();
        $locaisParaSelectArray = $locaisParaSelect->map(function ($loc) {
            $endereco = trim(($loc->loc_endereco ?? '').($loc->loc_numero ? ', '.$loc->loc_numero : ''));
            $codigo = $loc->loc_codigo_unico ?? '-';
            $bairro = $loc->loc_bairro ?? '-';
            $qtd = $loc->visitas_count ?? 0;
            $visitPart = $qtd > 0
                ? ' - '.($qtd === 1 ? __(':n visita', ['n' => $qtd]) : __(':n visitas', ['n' => $qtd]))
                : '';
            $label = ($endereco ?: '-').', '.$bairro.', '.__('Cód.').' '.$codigo.$visitPart;

            return ['id' => $loc->loc_id, 'label' => $label];
        })->values()->toArray();

        $imoveisComplementoResumo = $this->complementoImoveisResumo($visitas);
        $statsComplemento = $this->statsComplemento($imoveisComplementoResumo);

        return view('gestor.relatorios.index', compact(
            'visitas',
            'visitasPaginated',
            'locaisParaSelect',
            'locaisParaSelectArray',
            'bairros',
            'visitasParaGraficos',
            'sem_visitas',
            'totalVisitas',
            'bairroMaisFrequente',
            'totalComPendencia',
            'percentualPendencias',
            'totalComColeta',
            'visitasComTratamento',
            'totalDepEliminados',
            'imoveisComplementoResumo',
            'statsComplemento',
        ));
    }

    public function gerarPdf(Request $request)
    {
        $tipo = $request->input('tipo_relatorio', 'completo');

        $request->validate([
            'tipo_relatorio' => ['nullable', 'string', 'in:completo,individual,diario,semanal'],
            'local_id' => ['nullable'],
            'local_id.*' => ['integer', 'exists:locais,loc_id'],
            'data_unica' => ['nullable', 'date', Rule::requiredIf($tipo === 'diario')],
            'data_inicio' => ['nullable', 'date', Rule::requiredIf($tipo === 'semanal')],
            'data_fim' => ['nullable', 'date', 'after_or_equal:data_inicio', Rule::requiredIf($tipo === 'semanal')],
            'bairro' => ['nullable'],
        ], [
            'data_unica.required_if' => __('Informe a data para o relatório diário.'),
            'data_inicio.required_if' => __('Informe a data de início para o relatório por período.'),
            'data_fim.required_if' => __('Informe a data de fim para o relatório por período.'),
        ]);

        if (Visita::count() === 0) {
            return redirect()->route('gestor.relatorios.index')->with('error', __('Não há visitas cadastradas no sistema. Cadastre visitas para gerar relatórios.'));
        }
        $bairroPdf = $request->input('bairro');
        $bairrosPdf = is_array($bairroPdf) ? array_filter($bairroPdf) : ($bairroPdf !== null && $bairroPdf !== '' ? [$bairroPdf] : []);

        $query = Visita::with([
            'local' => fn ($q) => $q->withCount('moradores')->with('socioeconomico'),
            'doencas',
            'usuario',
            'tratamentos',
        ]);

        $localIdsPdf = array_filter(array_map('intval', (array) $request->input('local_id', [])));
        if ($tipo === 'individual' && empty($localIdsPdf)) {
            return redirect()->route('gestor.relatorios.index')->with('error', __('Selecione ao menos um local para o relatório individual.'));
        }
        if ($tipo === 'individual' && ! empty($localIdsPdf)) {
            $query->whereIn('fk_local_id', $localIdsPdf);
            if (! empty($bairrosPdf)) {
                $query->whereHas('local', function ($q) use ($bairrosPdf) {
                    $q->whereIn('loc_bairro', $bairrosPdf);
                });
            }
            $visitas = $query->orderBy('vis_data', 'desc')->get();
            $data_inicio = $visitas->min('vis_data') ?? now()->toDateString();
            $data_fim = $visitas->max('vis_data') ?? now()->toDateString();
        } else {
            $data_inicio = null;
            $data_fim = null;

            if ($tipo === 'diario' && $request->filled('data_unica')) {
                $data_inicio = $data_fim = $request->data_unica;
                $query->whereDate('vis_data', $data_inicio);
            } elseif ($tipo === 'semanal') {
                $data_inicio = $request->data_inicio;
                $data_fim = $request->data_fim;
                $query->whereBetween('vis_data', [$data_inicio, $data_fim]);
            } else {
                if ($request->filled('data_inicio')) {
                    $query->whereDate('vis_data', '>=', $request->data_inicio);
                }
                if ($request->filled('data_fim')) {
                    $query->whereDate('vis_data', '<=', $request->data_fim);
                }
            }

            if (! empty($bairrosPdf)) {
                $query->whereHas('local', function ($q) use ($bairrosPdf) {
                    $q->whereIn('loc_bairro', $bairrosPdf);
                });
            }

            $visitas = $query->orderBy('vis_data', 'desc')->get();

            if ($data_inicio === null) {
                $data_inicio = $visitas->min('vis_data') ?? now()->toDateString();
            }
            if ($data_fim === null) {
                $data_fim = $visitas->max('vis_data') ?? now()->toDateString();
            }
        }

        if ($visitas->isEmpty()) {
            return redirect()->route('gestor.relatorios.index')->with('error', __('Nenhuma visita encontrada para os critérios selecionados. Não foi possível gerar o PDF.'));
        }

        $base64Keys = [
            'graficoBairrosBase64', 'graficoDoencasBase64', 'mapaCalorBase64',
            'graficoZonasBase64', 'graficoDiasBase64', 'graficoInspBase64', 'graficoTratamentosBase64',
        ];
        $maxBase64Len = 2800000; // ~2MB só do payload base64
        $sanitizedBase64 = [];
        foreach ($base64Keys as $key) {
            $val = $request->input($key);
            if ($val === null || $val === '') {
                $sanitizedBase64[$key] = null;

                continue;
            }
            $val = (string) $val;
            $payload = $val;
            if (str_starts_with($val, 'data:image/')) {
                $comma = strpos($val, ',');
                if ($comma === false) {
                    $sanitizedBase64[$key] = null;

                    continue;
                }
                $payload = substr($val, $comma + 1);
            }
            if (strlen($payload) > $maxBase64Len || ! preg_match('/^[A-Za-z0-9+\/=]+$/', $payload)) {
                $sanitizedBase64[$key] = null;

                continue;
            }
            $sanitizedBase64[$key] = str_starts_with($val, 'data:image/') ? $val : 'data:image/png;base64,'.$payload;
        }

        $graficoBairrosBase64 = $sanitizedBase64['graficoBairrosBase64'];
        $graficoDoencasBase64 = $sanitizedBase64['graficoDoencasBase64'];
        $mapaCalorBase64 = $sanitizedBase64['mapaCalorBase64'];
        $graficoZonasBase64 = $sanitizedBase64['graficoZonasBase64'];
        $graficoDiasBase64 = $sanitizedBase64['graficoDiasBase64'];
        $graficoInspBase64 = $sanitizedBase64['graficoInspBase64'];
        $graficoTratamentosBase64 = $sanitizedBase64['graficoTratamentosBase64'];

        $gestorNome = Auth::user()->use_nome ?? __('Gestor');

        $titulo = match ($tipo) {
            'individual' => __('Relatório Individual'),
            'diario' => __('Relatório Diário'),
            'semanal' => __('Relatório por período'),
            default => __('Relatório Completo'),
        };

        if ($tipo === 'individual') {
            $locaisSel = ! empty($localIdsPdf) ? Local::whereIn('loc_id', $localIdsPdf)->get() : collect();
            $periodo = $locaisSel->isEmpty()
                ? __('Locais')
                : __('Locais: :lista', [
                    'lista' => $locaisSel->map(fn ($l) => trim(($l->loc_endereco ?? '').($l->loc_numero ? ', '.$l->loc_numero : '').', '.($l->loc_bairro ?? '')))->join(' | '),
                ]);
        } elseif ($tipo === 'diario') {
            $periodo = __('Data: :d', ['d' => $data_inicio]);
        } elseif ($tipo === 'semanal') {
            $periodo = __('Período: :i até :f', ['i' => $data_inicio, 'f' => $data_fim]);
        } else {
            $periodo = __('Período: :i até :f', ['i' => $data_inicio, 'f' => $data_fim]);
        }

        $descricao = $titulo.' - '.$periodo;
        if (! empty($bairrosPdf)) {
            $descricao .= ' - '.__('Bairros:').' '.implode(', ', $bairrosPdf);
        }

        LogHelper::registrar(
            'Geração de relatório',
            'Relatório',
            'export',
            $descricao
        );

        $imoveisComplementoResumo = $this->complementoImoveisResumo($visitas);

        return Pdf::loadView('gestor.relatorios.pdf', compact(
            'visitas',
            'graficoBairrosBase64',
            'graficoDoencasBase64',
            'mapaCalorBase64',
            'graficoZonasBase64',
            'graficoDiasBase64',
            'graficoInspBase64',
            'graficoTratamentosBase64',
            'gestorNome',
            'data_inicio',
            'data_fim',
            'bairrosPdf',
            'titulo',
            'tipo',
            'imoveisComplementoResumo',
        ))->setPaper('a4', 'landscape')->stream('relatorio-visitas.pdf');
    }

    /**
     * @param  Collection<int, Visita>  $visitas
     * @return Collection<int, Local>
     */
    private function complementoImoveisResumo($visitas)
    {
        $ids = $visitas->pluck('fk_local_id')->unique()->filter()->values();
        if ($ids->isEmpty()) {
            return collect();
        }

        return Local::query()
            ->whereIn('loc_id', $ids)
            ->withCount('moradores')
            ->with('socioeconomico')
            ->orderBy('loc_bairro')
            ->orderBy('loc_endereco')
            ->orderBy('loc_numero')
            ->get();
    }

    /**
     * @param  Collection<int, Local>  $imoveis
     * @return array{imoveis_periodo: int, imoveis_com_ocupantes: int, total_ocupantes: int, imoveis_com_socioeconomico: int}
     */
    private function statsComplemento($imoveis): array
    {
        $imoveis_periodo = $imoveis->count();
        $imoveis_com_ocupantes = $imoveis->where('moradores_count', '>', 0)->count();
        $total_ocupantes = (int) $imoveis->sum('moradores_count');
        $imoveis_com_socioeconomico = $imoveis->filter(fn (Local $l) => $l->socioeconomico !== null)->count();

        return compact('imoveis_periodo', 'imoveis_com_ocupantes', 'total_ocupantes', 'imoveis_com_socioeconomico');
    }
}
