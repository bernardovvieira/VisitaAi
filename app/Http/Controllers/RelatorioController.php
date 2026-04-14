<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use App\Models\Local;
use App\Models\User;
use App\Models\Visita;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Throwable;

class RelatorioController extends Controller
{
    private function idsUsuariosDoGestorAutenticado(): array
    {
        /** @var User|null $user */
        $user = Auth::user();
        if (! $user || ! $user->isGestor()) {
            abort(403);
        }

        return User::query()
            ->where('use_id', $user->use_id)
            ->orWhere('fk_gestor_id', $user->use_id)
            ->pluck('use_id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();
    }

    private function aplicarEscopoGestorEmVisitas(Builder $query): array
    {
        $userIds = $this->idsUsuariosDoGestorAutenticado();

        $query->whereIn('fk_usuario_id', $userIds);

        return Visita::query()
            ->whereIn('fk_usuario_id', $userIds)
            ->distinct()
            ->pluck('fk_local_id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();
    }

    public function index(Request $request)
    {
        $this->authorize('isGestor');

        $request->validate([
            'tipo_relatorio' => ['nullable', 'string', 'in:completo,individual,diario,semanal'],
            'limite_visitas' => ['nullable', 'integer', 'min:1', 'max:500'],
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
        $localIdsPermitidos = $this->aplicarEscopoGestorEmVisitas($query);

        $localIdsSolicitados = array_values(array_filter(array_map('intval', (array) $request->input('local_id', []))));
        $localIds = array_values(array_intersect($localIdsSolicitados, $localIdsPermitidos));

        if (! empty($localIdsSolicitados) && count(array_unique($localIdsSolicitados)) !== count($localIds)) {
            abort(403, __('Um ou mais locais selecionados não pertencem ao escopo permitido.'));
        }

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

        $bairrosQuery = Local::select('loc_bairro')
            ->distinct()
            ->whereNotNull('loc_bairro')
            ->orderBy('loc_bairro');
        if (! empty($localIdsPermitidos)) {
            $bairrosQuery->whereIn('loc_id', $localIdsPermitidos);
        } else {
            $bairrosQuery->whereRaw('1 = 0');
        }
        $bairros = $bairrosQuery->pluck('loc_bairro')
            ->map(fn ($b) => trim((string) $b))
            ->filter(fn ($b) => $b !== '')
            ->values()
            ->toArray();

        $locaisParaSelectQuery = Local::has('visitas')
            ->withCount('visitas')
            ->orderBy('loc_bairro')
            ->orderBy('loc_endereco')
            ->orderBy('loc_numero');
        if (! empty($localIdsPermitidos)) {
            $locaisParaSelectQuery->whereIn('loc_id', $localIdsPermitidos);
        } else {
            $locaisParaSelectQuery->whereRaw('1 = 0');
        }
        $locaisParaSelect = $locaisParaSelectQuery->get();
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
        $imoveisPage = LengthAwarePaginator::resolveCurrentPage('imoveis_page');
        $imoveisPerPage = 10;
        $imoveisComplementoResumoPaginated = new LengthAwarePaginator(
            $imoveisComplementoResumo->forPage($imoveisPage, $imoveisPerPage)->values(),
            $imoveisComplementoResumo->count(),
            $imoveisPerPage,
            $imoveisPage,
            [
                'path' => $request->url(),
                'pageName' => 'imoveis_page',
                'query' => $request->query(),
            ]
        );

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
            'imoveisComplementoResumoPaginated',
            'statsComplemento',
        ));
    }

    public function gerarPdf(Request $request)
    {
        $this->authorize('isGestor');

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

        // Carrega apenas relações essenciais para o PDF
        $query = Visita::with([
            'local' => function ($q) {
                $q->select('loc_id','loc_cidade','loc_estado','loc_bairro','loc_endereco','loc_numero','loc_complemento','loc_codigo_unico','loc_tipo','loc_zona','loc_quarteirao','loc_sequencia','loc_lado');
                $q->withCount('moradores')->with('socioeconomico');
            },
            'doencas:doencas.doe_id,doe_nome',
            'usuario:use_id,use_nome',
            'tratamentos:trat_id,fk_visita_id,trat_forma,trat_tipo,qtd_gramas,qtd_depositos_tratados,qtd_cargas',
        ]);
        $localIdsPermitidos = $this->aplicarEscopoGestorEmVisitas($query);

        // Forçar limite a 1 visita diretamente na query (modo de teste rápido)
        $query->limit(1);

        $localIdsSolicitados = array_values(array_filter(array_map('intval', (array) $request->input('local_id', []))));
        $localIdsPdf = array_values(array_intersect($localIdsSolicitados, $localIdsPermitidos));

        if (! empty($localIdsSolicitados) && count(array_unique($localIdsSolicitados)) !== count($localIdsPdf)) {
            abort(403, __('Um ou mais locais selecionados não pertencem ao escopo permitido.'));
        }

        if ($tipo === 'individual' && empty($localIdsPdf)) {
            return redirect()->route('gestor.relatorios.index')->with('error', __('Selecione ao menos um local para o relatório individual.'));
        }

        // Inicializa variáveis
        $data_inicio = null;
        $data_fim = null;

        if ($tipo === 'individual' && ! empty($localIds)) {
            $query->whereIn('fk_local_id', $localIds);
            $visitas = $query->orderBy('vis_data', 'desc')->get();
        } elseif ($tipo === 'diario' && $request->filled('data_unica')) {
            $query->whereDate('vis_data', $request->data_unica);
            $visitas = $query->orderBy('vis_data', 'desc')->get();
            $data_inicio = $data_fim = $request->data_unica;
        } elseif ($tipo === 'semanal') {
            if ($request->filled('data_inicio')) {
                $query->whereDate('vis_data', '>=', $request->data_inicio);
                $data_inicio = $request->data_inicio;
            }
            if ($request->filled('data_fim')) {
                $query->whereDate('vis_data', '<=', $request->data_fim);
                $data_fim = $request->data_fim;
            }
            $visitas = $query->orderBy('vis_data', 'desc')->get();
            // Se não veio do filtro, pega do resultado
            $data_inicio = $data_inicio ?? $visitas->min('vis_data');
            $data_fim = $data_fim ?? $visitas->max('vis_data');
        } elseif ($tipo === 'completo') {
            if ($request->filled('data_inicio')) {
                $query->whereDate('vis_data', '>=', $request->data_inicio);
                $data_inicio = $request->data_inicio;
            }
            if ($request->filled('data_fim')) {
                $query->whereDate('vis_data', '<=', $request->data_fim);
                $data_fim = $request->data_fim;
            }
            // Limit to last 50 visits for 'completo' report (can be overridden by request param `limite_visitas`)
            $limite = $request->input('limite_visitas');
            $limite = is_numeric($limite) ? max(1, (int) $limite) : 50;
            $limite = min($limite, 500);
            $visitas = $query->orderBy('vis_data', 'desc')->orderBy('vis_id', 'desc')->take($limite)->get();
            $data_inicio = $data_inicio ?? $visitas->min('vis_data');
            $data_fim = $data_fim ?? $visitas->max('vis_data');
        } else {
            // fallback
            $visitas = $query->orderBy('vis_data', 'desc')->get();
            $data_inicio = $visitas->min('vis_data');
            $data_fim = $visitas->max('vis_data');
        }

        if ($visitas->isEmpty()) {
            return redirect()->route('gestor.relatorios.index')->with('error', __('Nenhuma visita encontrada para os critérios selecionados. Não foi possível gerar o PDF.'));
        }

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

        try {
            // Prefer wkhtmltopdf (external binary) if available because DOMPDF can explode memory on large HTML.
            $wkPath = null;
            try {
                $wkPath = trim((string) shell_exec('command -v wkhtmltopdf 2>/dev/null')) ?: null;
            } catch (Throwable $_) {
                $wkPath = null;
            }

            $viewData = [
                'visitas' => $visitas,
                'gestorNome' => $gestorNome,
                'data_inicio' => $data_inicio,
                'data_fim' => $data_fim,
                'bairrosPdf' => $bairrosPdf,
                'titulo' => $titulo,
                'tipo' => $tipo,
                'imoveisComplementoResumo' => $imoveisComplementoResumo,
            ];

            if ($wkPath) {
                $html = view('gestor.relatorios.pdf', $viewData)->render();
                $tmpDir = sys_get_temp_dir();
                $htmlFile = $tmpDir.DIRECTORY_SEPARATOR.'relatorio_'.uniqid().'.html';
                $pdfFile = $tmpDir.DIRECTORY_SEPARATOR.'relatorio_'.uniqid().'.pdf';
                file_put_contents($htmlFile, $html);

                $cmd = escapeshellcmd($wkPath).' --page-size A4 --orientation Landscape '.escapeshellarg($htmlFile).' '.escapeshellarg($pdfFile).' 2>&1';
                exec($cmd, $out, $code);

                if ($code === 0 && file_exists($pdfFile)) {
                    $contents = file_get_contents($pdfFile);
                    // cleanup
                    @unlink($htmlFile);
                    @unlink($pdfFile);

                    return response($contents, 200, [
                        'Content-Type' => 'application/pdf',
                        'Content-Disposition' => 'inline; filename="relatorio-visitas.pdf"',
                    ]);
                }
                // If wkhtmltopdf failed, cleanup and fall back to DomPDF
                @unlink($htmlFile);
                @unlink($pdfFile);
            }

            // Fallback to DomPDF
            @ini_set('memory_limit', '512M');
            $pdf = Pdf::loadView('gestor.relatorios.pdf', $viewData);
            $pdf->setPaper('a4', 'landscape');
            return $pdf->stream('relatorio-visitas.pdf');
        } catch (Throwable $e) {
            $msg = (string) $e->getMessage();
            $memoryPatterns = ['allowed memory', 'out of memory', 'memory exhausted', 'cannot allocate memory'];
            $isMemoryError = false;
            foreach ($memoryPatterns as $pat) {
                if (stripos($msg, $pat) !== false) {
                    $isMemoryError = true;
                    break;
                }
            }

            $context = [
                'erro' => $msg,
                'tipo_relatorio' => $tipo,
                'data_inicio' => $data_inicio,
                'data_fim' => $data_fim,
                'bairros' => $bairrosPdf,
                'total_visitas' => $visitas->count(),
                'gestor_id' => Auth::id(),
            ];

            if ($isMemoryError) {
                Log::critical('Falha por falta de memória ao gerar PDF de relatorios', $context);
                report($e);

                return redirect()->route('gestor.relatorios.index')->with(
                    'error',
                    __('Não foi possível gerar o PDF devido à falta de memória. Tente reduzir o período ou aplicar filtros mais restritos (por exemplo relatório individual).')
                );
            }

            Log::error('Falha ao gerar PDF de relatorios', $context);
            report($e);

            return redirect()->route('gestor.relatorios.index')->with(
                'error',
                __('Não foi possível gerar o PDF no momento. Tente novamente em instantes.')
            );
        }
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
