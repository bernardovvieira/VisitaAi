<?php

namespace App\Http\Controllers\Vigilancia;

use App\Helpers\LogHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\LocalRequest;
use App\Models\Local;
use App\Models\LocalSocioeconomico;
use App\Models\Morador;
use App\Models\User;
use App\Services\Municipio\ResumoOcupantesMunicipioService;
use App\Support\SmartSearch;
use Barryvdh\DomPDF\Facade\Pdf;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\SvgWriter;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class LocalController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Local::class);

        /** @var User $user */
        $user = Auth::user();

        if ($request->has('guardada')) {
            session()->flash('warning', __('Local cadastrado no dispositivo com sucesso. Sincronize quando se reconectar.'));

            return redirect()->to($request->url());
        }

        $search = trim($request->input('search'));
        $searchLower = mb_strtolower($search);
        $terms = SmartSearch::terms($search);

        $query = Local::query();

        if ($search !== '') {
            // Busca inteligente: prefixos de residencial, comercial, terreno, urbano, rural (sem regras fixas).
            $tipo = null;
            $zona = null;
            $minPrefix = 2;
            if (strlen($searchLower) >= $minPrefix) {
                if (str_starts_with('residencial', $searchLower)) {
                    $tipo = 'R';
                } elseif (str_starts_with('comercial', $searchLower)) {
                    $tipo = 'C';
                } elseif (str_starts_with('terreno', $searchLower)) {
                    $tipo = 'T';
                }
                if (str_starts_with('urbano', $searchLower) || str_starts_with('urbana', $searchLower)
                    || str_starts_with($searchLower, 'urbano') || str_starts_with($searchLower, 'urbana')) {
                    $zona = 'U';
                } elseif (str_starts_with('rural', $searchLower) || str_starts_with($searchLower, 'rural')) {
                    $zona = 'R';
                }
            }
            if ($searchLower === 'r' || $searchLower === 'c' || $searchLower === 't') {
                $tipo = strtoupper($searchLower);
            }
            if ($searchLower === 'u') {
                $zona = 'U';
            } elseif ($searchLower === 'r' && $zona === null) {
                $zona = 'R';
            }

            $query->where(function ($q) use ($terms, $tipo, $zona) {
                foreach ($terms as $term) {
                    $like = '%'.$term.'%';
                    $q->orWhereRaw('LOWER(COALESCE(loc_endereco, "")) LIKE ?', [$like])
                        ->orWhereRaw('LOWER(COALESCE(loc_numero, "")) LIKE ?', [$like])
                        ->orWhereRaw('LOWER(COALESCE(loc_complemento, "")) LIKE ?', [$like])
                        ->orWhereRaw('LOWER(COALESCE(loc_bairro, "")) LIKE ?', [$like])
                        ->orWhereRaw('LOWER(COALESCE(loc_cidade, "")) LIKE ?', [$like])
                        ->orWhereRaw('LOWER(COALESCE(loc_estado, "")) LIKE ?', [$like])
                        ->orWhereRaw('LOWER(COALESCE(loc_pais, "")) LIKE ?', [$like])
                        ->orWhereRaw('LOWER(COALESCE(loc_categoria, "")) LIKE ?', [$like])
                        ->orWhereRaw('LOWER(COALESCE(loc_lado, "")) LIKE ?', [$like])
                        ->orWhereRaw('LOWER(COALESCE(loc_responsavel_nome, "")) LIKE ?', [$like])
                        ->orWhereRaw(SmartSearch::foldExpr('loc_responsavel_nome').' LIKE ?', [$like])
                        ->orWhereRaw('CAST(loc_cep AS CHAR) LIKE ?', [$like])
                        ->orWhereRaw('CAST(loc_codigo_unico AS CHAR) LIKE ?', [$like])
                        ->orWhereRaw('CAST(loc_codigo AS CHAR) LIKE ?', [$like])
                        ->orWhereRaw('CAST(loc_quarteirao AS CHAR) LIKE ?', [$like])
                        ->orWhereRaw('CAST(loc_sequencia AS CHAR) LIKE ?', [$like])
                        ->orWhereHas('moradores', function ($moradoresQuery) use ($like) {
                            $moradoresQuery->whereRaw('LOWER(COALESCE(mor_nome, "")) LIKE ?', [$like])
                                ->orWhereRaw(SmartSearch::foldExpr('mor_nome').' LIKE ?', [$like])
                                ->orWhereRaw('LOWER(COALESCE(mor_profissao, "")) LIKE ?', [$like])
                                ->orWhereRaw('LOWER(COALESCE(mor_observacao, "")) LIKE ?', [$like])
                                ->orWhereRaw('CAST(mor_id AS CHAR) LIKE ?', [$like]);
                        })
                        ->orWhereHas('socioeconomico', function ($socioQuery) use ($like) {
                            $socioQuery->whereRaw('LOWER(COALESCE(lse_proprietario_nome, "")) LIKE ?', [$like])
                                ->orWhereRaw(SmartSearch::foldExpr('lse_proprietario_nome').' LIKE ?', [$like])
                                ->orWhereRaw('LOWER(COALESCE(lse_proprietario_endereco, "")) LIKE ?', [$like])
                                ->orWhereRaw('LOWER(COALESCE(lse_proprietario_telefone, "")) LIKE ?', [$like])
                                ->orWhereRaw('LOWER(COALESCE(lse_telefone_contato, "")) LIKE ?', [$like])
                                ->orWhereRaw('LOWER(COALESCE(lse_observacoes_imovel, "")) LIKE ?', [$like]);
                        });
                }

                if ($tipo) {
                    $q->orWhere('loc_tipo', $tipo);
                }

                if ($zona) {
                    $q->orWhere('loc_zona', $zona);
                }
            });
        }

        $locais = $query->orderByDesc('loc_id')->paginate(10)->appends(['search' => $search]);

        $coordenadasDuplicadas = Local::whereNotNull('loc_latitude')
            ->whereNotNull('loc_longitude')
            ->selectRaw('loc_latitude, loc_longitude, COUNT(*) as qtd')
            ->groupBy('loc_latitude', 'loc_longitude')
            ->having('qtd', '>', 1)
            ->exists();

        $view = $user->isAgente()
            ? 'agente.locais.index'
            : 'gestor.locais.index';

        return view($view, compact('locais', 'search', 'coordenadasDuplicadas'));
    }

    public function show(Local $local)
    {
        $this->authorize('view', $local);

        /** @var User $user */
        $user = Auth::user();

        $qrCode = new QrCode(
            data: route('consulta.codigo', ['codigo' => $local->loc_codigo_unico]),
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::Low,
            size: 250,
            margin: 10,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
            foregroundColor: new Color(0, 0, 0),
            backgroundColor: new Color(255, 255, 255)
        );

        try {
            $writer = new PngWriter;
            $result = $writer->write($qrCode);
            $qrCodeBase64 = base64_encode($result->getString());
            $qrCodeMime = 'image/png';
        } catch (\Throwable $e) {
            $writer = new SvgWriter;
            $result = $writer->write($qrCode);
            $qrCodeBase64 = base64_encode($result->getString());
            $qrCodeMime = 'image/svg+xml';
        }

        $view = $user->isAgente()
            ? 'agente.locais.show'
            : 'gestor.locais.show';

        if ($user->isGestor()) {
            $local->load([
                'moradores',
                'socioeconomico',
                'visitas' => fn ($q) => $q->select([
                    'vis_id', 'vis_data', 'vis_ocupantes_observacoes', 'fk_local_id',
                ])->orderByDesc('vis_data')->orderByDesc('vis_id'),
            ]);
        } else {
            $local->load(['moradores', 'socioeconomico']);
        }
        $moradorResumo = app(ResumoOcupantesMunicipioService::class)->resumoParaLocal($local);

        return view($view, compact('local', 'qrCodeBase64', 'qrCodeMime', 'moradorResumo'));
    }

    public function create()
    {
        $this->authorize('create', Local::class);

        $primario = Local::orderBy('loc_id')->first();
        $isPrimario = $primario === null;
        $cepPermitido = null;
        $cidadeEstado = null;
        $cepsCadastrados = [];
        if ($primario) {
            $cidadeEstado = ['cidade' => $primario->loc_cidade, 'estado' => $primario->loc_estado];
            $cepsCadastrados = $this->buildCepsCadastrados($primario->loc_cidade, $primario->loc_estado);
        }
        $storeRoute = Auth::user()->isGestor() ? 'gestor.locais.store' : 'agente.locais.store';
        $indexRoute = Auth::user()->isGestor() ? 'gestor.locais.index' : 'agente.locais.index';

        return view('agente.locais.create', compact('cepPermitido', 'cidadeEstado', 'cepsCadastrados', 'isPrimario', 'storeRoute', 'indexRoute'));
    }

    public function store(LocalRequest $request)
    {
        $data = $request->validated();
        $ocupantes = $data['ocupantes'] ?? [];
        $ocupantesFiles = $request->file('ocupantes', []);
        unset($data['ocupantes']);
        $socio = array_key_exists('socio', $data) ? ($data['socio'] ?? []) : null;
        unset($data['socio']);

        do {
            $codigo = mt_rand(10000000, 99999999);
        } while (Local::where('loc_codigo_unico', $codigo)->exists());

        $data['loc_codigo_unico'] = $codigo;
        $data['loc_numero'] = $this->normalizeLocNumero($data['loc_numero'] ?? null);

        $local = Local::create($data);
        $this->persistOcupantesNoLocal($local, is_array($ocupantes) ? $ocupantes : [], is_array($ocupantesFiles) ? $ocupantesFiles : []);
        if (is_array($socio)) {
            $this->persistSocioeconomicoNoLocal($local, $socio);
        }

        LogHelper::registrar(
            'Cadastro de local',
            'Local',
            'create',
            'Local cadastrado: '.$local->loc_endereco.', '.($local->loc_numero ?? 'S/N')
        );

        $indexRoute = Auth::user()->isGestor() ? 'gestor.locais.index' : 'agente.locais.index';
        $redirectRoute = (Auth::user()->isGestor() && Local::count() === 1)
            ? 'gestor.dashboard'
            : $indexRoute;

        return redirect()
            ->route($redirectRoute)
            ->with('success', __('Local cadastrado com sucesso.'))
            ->with('created_local_id', $local->loc_id);
    }

    public function edit(Local $local)
    {
        $this->authorize('update', $local);

        if ($local->isPrimary()) {
            return redirect()
                ->route(Auth::user()->isGestor() ? 'gestor.locais.index' : 'agente.locais.index')
                ->with('error', __('O local primário (primeiro cadastrado) não pode ser editado pela interface. Para alterações, entre em contato com o suporte técnico.'));
        }
        $primario = Local::orderBy('loc_id')->first();
        $cepPermitido = null;
        $cidadeEstado = null;
        $cepsCadastrados = [];
        if ($primario) {
            $cidadeEstado = ['cidade' => $primario->loc_cidade, 'estado' => $primario->loc_estado];
            $cepsCadastrados = $this->buildCepsCadastrados($primario->loc_cidade, $primario->loc_estado);
        }

        $local->load(['moradores', 'socioeconomico']);

        return view('agente.locais.edit', compact('local', 'cepPermitido', 'cidadeEstado', 'cepsCadastrados'));
    }

    /**
     * Retorna lista de endereços por CEP já cadastrados no sistema (mesmo município), no formato ViaCEP.
     */
    private function buildCepsCadastrados(?string $cidade, ?string $estado): array
    {
        if (! $cidade || ! $estado) {
            return [];
        }
        $locais = Local::where('loc_cidade', $cidade)
            ->where('loc_estado', $estado)
            ->get();
        $out = [];
        foreach ($locais as $loc) {
            $cepNorm = preg_replace('/\D/', '', $loc->loc_cep ?? '');
            if (strlen($cepNorm) >= 8) {
                $cepNorm = substr($cepNorm, 0, 8);
                $lat = $loc->loc_latitude !== null && $loc->loc_latitude !== '' ? (float) $loc->loc_latitude : null;
                $lng = $loc->loc_longitude !== null && $loc->loc_longitude !== '' ? (float) $loc->loc_longitude : null;
                $out[] = [
                    'cep' => $cepNorm,
                    'logradouro' => $loc->loc_endereco ?? '',
                    'bairro' => $loc->loc_bairro ?? '',
                    'localidade' => $loc->loc_cidade ?? '',
                    'uf' => $loc->loc_estado ?? '',
                    'latitude' => $lat,
                    'longitude' => $lng,
                ];
            }
        }

        return $out;
    }

    public function update(LocalRequest $request, Local $local)
    {
        $this->authorize('update', $local);

        if ($local->isPrimary()) {
            return redirect()
                ->route(Auth::user()->isGestor() ? 'gestor.locais.index' : 'agente.locais.index')
                ->with('error', __('O local primário não pode ser editado pela interface. Para alterações, entre em contato com o suporte técnico.'));
        }
        $data = $request->validated();
        $ocupantes = $data['ocupantes'] ?? [];
        $ocupantesFiles = $request->file('ocupantes', []);
        unset($data['ocupantes']);
        $socio = array_key_exists('socio', $data) ? ($data['socio'] ?? []) : null;
        unset($data['socio']);
        $data['loc_numero'] = $this->normalizeLocNumero($data['loc_numero'] ?? null);
        $local->update($data);
        $this->persistOcupantesNoLocal($local, is_array($ocupantes) ? $ocupantes : [], is_array($ocupantesFiles) ? $ocupantesFiles : []);
        if (is_array($socio)) {
            $this->persistSocioeconomicoNoLocal($local, $socio);
        }

        LogHelper::registrar(
            'Atualização de local',
            'Local',
            'update',
            'Local atualizado: '.$local->loc_endereco.', nº '.($local->loc_numero ?? 'S/N')
        );

        return redirect()
            ->route('agente.locais.index')
            ->with('success', __('Local atualizado com sucesso.'));
    }

    public function destroy(Local $local)
    {
        $this->authorize('delete', $local);

        if ($local->isPrimary()) {
            return redirect()
                ->route(Auth::user()->isGestor() ? 'gestor.locais.index' : 'agente.locais.index')
                ->with('error', __('O local primário (primeiro cadastrado) não pode ser excluído pela interface. Para exclusão ou alterações, entre em contato com o suporte técnico.'));
        }
        if ($local->moradores()->exists()) {
            return redirect()
                ->route(Auth::user()->isGestor() ? 'gestor.locais.index' : 'agente.locais.index')
                ->with('error', __('Erro: este local possui ocupantes registrados no Visita Aí e não pode ser excluído.'));
        }

        if ($local->visitas()->exists()) {
            return redirect()
                ->route(Auth::user()->isGestor() ? 'gestor.locais.index' : 'agente.locais.index')
                ->with('error', __('Erro: este local possui visitas cadastradas e não pode ser excluído.'));
        }

        $descricao = $local->loc_endereco.', '.($local->loc_numero ?? 'S/N');

        $local->delete();

        LogHelper::registrar(
            'Exclusão de local',
            'Local',
            'delete',
            'Local excluído: '.$descricao
        );

        return redirect()
            ->route(Auth::user()->isGestor() ? 'gestor.locais.index' : 'agente.locais.index')
            ->with('success', __('Local excluído com sucesso.'));
    }

    /**
     * Recebe locais salvos offline e persiste no servidor.
     * Retorna JSON: { "criados": int, "erros": [ { "index", "message" } ], "ids": [ loc_id, ... ] }
     */
    public function syncStore(Request $request)
    {
        $user = Auth::user();
        if (! $user->isAgenteEndemias() && ! $user->isGestor()) {
            return response()->json(['message' => __('Acesso negado.')], 403);
        }
        $this->authorize('create', Local::class);

        $payload = $request->validate([
            'locais' => ['required', 'array', 'max:50'],
            'locais.*' => ['array'],
        ]);

        $locais = $payload['locais'];
        $ids = array_fill(0, count($locais), null);
        $erros = [];
        $rules = $this->localSyncRules();

        foreach ($locais as $index => $item) {
            $validator = Validator::make($item, $rules);
            if ($validator->fails()) {
                $erros[] = ['index' => $index, 'message' => implode(' ', $validator->errors()->all())];

                continue;
            }
            $data = $validator->validated();
            if (Local::where('loc_endereco', $data['loc_endereco'])->exists()) {
                $erros[] = ['index' => $index, 'message' => __('Já existe um local com este endereço.')];

                continue;
            }
            do {
                $codigo = mt_rand(10000000, 99999999);
            } while (Local::where('loc_codigo_unico', $codigo)->exists());
            $data['loc_codigo_unico'] = $codigo;
            $data['loc_numero'] = $this->normalizeLocNumero($data['loc_numero'] ?? null);
            try {
                $local = Local::create($data);
                $ids[$index] = $local->loc_id;
                LogHelper::registrar('Sincronização offline', 'Local', 'create', 'Local sincronizado: '.$local->loc_endereco);
            } catch (\Throwable $e) {
                Log::warning('Local syncStore: '.$e->getMessage());
                $erros[] = ['index' => $index, 'message' => $e->getMessage()];
            }
        }

        return response()->json([
            'criados' => count(array_filter($ids)),
            'erros' => $erros,
            'ids' => $ids,
        ]);
    }

    public function fichaSocioeconomicaPdf(Local $local)
    {
        $this->authorize('view', $local);

        $local->load(['moradores', 'socioeconomico']);

        $pdf = Pdf::loadView('pdf.ficha_socioeconomica', [
            'local' => $local,
            'socio' => $local->socioeconomico,
            'titulos' => config('visitaai_socioeconomico.secao_titulos', []),
        ])->setPaper('a4', 'portrait');

        // Render first so we can draw footer and page numbers server-side
        $pdf->render();

        // Try to obtain dompdf canvas and font metrics (handle different method namings)
        $dom = $pdf->getDomPDF();
        $canvas = null;
        if (method_exists($dom, 'getCanvas')) {
            $canvas = $dom->getCanvas();
        } elseif (method_exists($dom, 'get_canvas')) {
            $canvas = $dom->get_canvas();
        }
        $fontMetrics = null;
        if (method_exists($dom, 'getFontMetrics')) {
            $fontMetrics = $dom->getFontMetrics();
        } elseif (method_exists($dom, 'get_font_metrics')) {
            $fontMetrics = $dom->get_font_metrics();
        }

        if ($canvas && $fontMetrics) {
            try {
                $font = $fontMetrics->getFont('dejavu sans', 'normal');
            } catch (\Throwable $e) {
                $font = $fontMetrics->getFont(null, 'normal');
            }

            // determine canvas dimensions with fallbacks
            if (method_exists($canvas, 'get_width')) {
                $width = $canvas->get_width();
            } elseif (method_exists($canvas, 'getWidth')) {
                $width = $canvas->getWidth();
            } else {
                $width = 595; // A4 approx fallback
            }
            if (method_exists($canvas, 'get_height')) {
                $height = $canvas->get_height();
            } elseif (method_exists($canvas, 'getHeight')) {
                $height = $canvas->getHeight();
            } else {
                $height = 842; // A4 approx fallback
            }

            // Align footers to page margins used in CSS: @page { margin: 100px 20px 70px 20px }
            // CSS uses px; dompdf canvas uses points. Convert px -> pt (1px = 72/96 pt)
            $pxToPt = 72.0 / 96.0;
            $cssSideMarginPx = 20; // as used in the Blade @page rule
            $leftMargin = $cssSideMarginPx * $pxToPt;
            $rightMargin = $cssSideMarginPx * $pxToPt;

            // Vertical position for footer baseline (adjust as needed)
            $y = $height - 28;
            $leftText = 'Bitwise Technologies - Soluções digitais para eficiência e inovação';
            $pageText = 'Página {PAGE_NUM} / {PAGE_COUNT}';

            // Compute a realistic width for the page text by replacing placeholders
            $pageCount = null;
            if (method_exists($canvas, 'get_page_count')) {
                $pageCount = $canvas->get_page_count();
            } elseif (method_exists($dom, 'get_page_count')) {
                $pageCount = $dom->get_page_count();
            }
            $samplePageNum = $pageCount ?: 1;
            $sampleText = str_replace(['{PAGE_NUM}', '{PAGE_COUNT}'], [(string) $samplePageNum, (string) ($pageCount ?: $samplePageNum)], $pageText);
            $w = $fontMetrics->getTextWidth($sampleText, $font, 8);

            // place page number aligned to the content box right edge (respecting CSS margins)
            // small inner gutter in points from the content edge; allow override via .env
            $innerGutter = (float) env('DOMPDF_FOOTER_INNER_GUTTER', 2);
            $x = $width - $rightMargin - $innerGutter - $w;

            // Draw footer texts aligned to page margins
            try {
                $canvas->page_text($leftMargin, $y, $leftText, $font, 8, [0,0,0]);
                $canvas->page_text($x, $y, $pageText, $font, 8, [0,0,0]);

                // Optional visual debug markers: set DOMPDF_FOOTER_DEBUG=true in .env
                if (env('DOMPDF_FOOTER_DEBUG', true)) {
                    // mark left content edge (L), right content edge (R) and computed page number X (P)
                    $canvas->page_text($leftMargin, $y - 8, 'L', $font, 6, [1,0,0]);
                    $canvas->page_text($width - $rightMargin, $y - 8, 'R', $font, 6, [1,0,0]);
                    $canvas->page_text($x, $y - 8, 'P', $font, 6, [0,0,1]);
                }
            } catch (\Throwable $e) {
                // swallow — footer drawing should not break PDF generation
            }
        }

        $safeCode = preg_replace('/\D/', '', (string) $local->loc_codigo_unico) ?: 'imovel';

        return $pdf->download('ficha-socioeconomica-'.$safeCode.'.pdf');
    }

    /**
     * @param  array<string, mixed>  $socio
     */
    private function persistSocioeconomicoNoLocal(Local $local, array $socio): void
    {
        $attrs = LocalSocioeconomico::attributesFromForm($socio);
        $attrs['fk_local_id'] = $local->loc_id;
        LocalSocioeconomico::updateOrCreate(
            ['fk_local_id' => $local->loc_id],
            $attrs
        );
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     */
    private function persistOcupantesNoLocal(Local $local, array $rows, array $files = []): void
    {
        foreach ($rows as $index => $row) {
            if (! is_array($row)) {
                continue;
            }
            $morIdRaw = $row['mor_id'] ?? null;
            $morId = is_numeric($morIdRaw) && (int) $morIdRaw > 0 ? (int) $morIdRaw : null;

            $nome = isset($row['mor_nome']) ? trim((string) $row['mor_nome']) : '';
            $dn = $row['mor_data_nascimento'] ?? null;
            $dn = ($dn === '' || $dn === null) ? null : $dn;
            $esc = $row['mor_escolaridade'] ?? null;
            $esc = ($esc === '' || $esc === null) ? null : $esc;
            $renda = $row['mor_renda_faixa'] ?? null;
            $renda = ($renda === '' || $renda === null) ? null : $renda;
            $cor = $row['mor_cor_raca'] ?? null;
            $cor = ($cor === '' || $cor === null) ? null : $cor;
            $trab = $row['mor_situacao_trabalho'] ?? null;
            $trab = ($trab === '' || $trab === null) ? null : $trab;
            $obs = isset($row['mor_observacao']) ? trim((string) $row['mor_observacao']) : '';
            $obs = $obs === '' ? null : $obs;
            $sexo = $row['mor_sexo'] ?? null;
            $sexo = ($sexo === '' || $sexo === null) ? null : $sexo;
            $ec = $row['mor_estado_civil'] ?? null;
            $ec = ($ec === '' || $ec === null) ? null : $ec;
            $nat = isset($row['mor_naturalidade']) ? trim((string) $row['mor_naturalidade']) : '';
            $nat = $nat === '' ? null : $nat;
            $prof = isset($row['mor_profissao']) ? trim((string) $row['mor_profissao']) : '';
            $prof = $prof === '' ? null : $prof;
            $par = $row['mor_parentesco'] ?? null;
            $par = ($par === '' || $par === null) ? null : $par;
            $refRaw = $row['mor_referencia_familiar'] ?? false;
            $ref = in_array($refRaw, [true, 1, '1', 'true', 'on', 'yes'], true);
            $tel = isset($row['mor_telefone']) ? trim((string) $row['mor_telefone']) : '';
            $tel = $tel === '' ? null : $tel;
            $rgN = isset($row['mor_rg_numero']) ? trim((string) $row['mor_rg_numero']) : '';
            $rgN = $rgN === '' ? null : $rgN;
            $rgO = isset($row['mor_rg_orgao']) ? trim((string) $row['mor_rg_orgao']) : '';
            $rgO = $rgO === '' ? null : $rgO;
            $rgExp = $row['mor_rg_expedicao'] ?? null;
            $rgExp = ($rgExp === '' || $rgExp === null) ? null : $rgExp;
            $cpf = isset($row['mor_cpf']) ? trim((string) $row['mor_cpf']) : '';
            $cpf = $cpf === '' ? null : $cpf;
            $tu = isset($row['mor_tempo_uniao_conjuge']) ? trim((string) $row['mor_tempo_uniao_conjuge']) : '';
            $tu = $tu === '' ? null : $tu;
            $aj = isset($row['mor_ajuda_compra_imovel']) ? strtolower(trim((string) $row['mor_ajuda_compra_imovel'])) : '';
            $aj = in_array($aj, ['sim', 'nao'], true) ? $aj : null;
            $rfi = $row['mor_renda_formal_informal'] ?? null;
            $rfi = ($rfi === '' || $rfi === null) ? null : $rfi;
            $documentoPessoal = data_get($files, $index.'.mor_documento_pessoal');
            if (! $documentoPessoal instanceof UploadedFile) {
                $documentoPessoal = null;
            }

            $payload = [
                'mor_nome' => $nome !== '' ? $nome : null,
                'mor_data_nascimento' => $dn,
                'mor_escolaridade' => $esc,
                'mor_renda_faixa' => $renda,
                'mor_cor_raca' => $cor,
                'mor_situacao_trabalho' => $trab,
                'mor_sexo' => $sexo,
                'mor_estado_civil' => $ec,
                'mor_naturalidade' => $nat,
                'mor_profissao' => $prof,
                'mor_parentesco' => $par,
                'mor_referencia_familiar' => $ref,
                'mor_telefone' => $tel,
                'mor_rg_numero' => $rgN,
                'mor_rg_orgao' => $rgO,
                'mor_rg_expedicao' => $rgExp,
                'mor_cpf' => $cpf,
                'mor_tempo_uniao_conjuge' => $tu,
                'mor_ajuda_compra_imovel' => $aj,
                'mor_renda_formal_informal' => $rfi,
                'mor_observacao' => $obs,
            ];

            if ($morId) {
                $m = Morador::query()
                    ->where('fk_local_id', $local->loc_id)
                    ->where('mor_id', $morId)
                    ->first();
                if (! $m) {
                    continue;
                }
                if ($documentoPessoal) {
                    $this->deleteDocumentoPessoal($m);
                    $payload = array_merge($payload, $this->uploadDocumentoPessoal($documentoPessoal));
                }
                $m->update($payload);

                continue;
            }

            $vacuous = $nome === '' && $dn === null && $esc === null && $renda === null && $cor === null && $trab === null && $obs === null
                && $sexo === null && $ec === null && $nat === null && $prof === null && $par === null && ! $ref && $tel === null
                && $rgN === null && $rgO === null && $rgExp === null && $cpf === null && $tu === null && $aj === null && $rfi === null;
            if ($vacuous) {
                continue;
            }

            if ($documentoPessoal) {
                $payload = array_merge($payload, $this->uploadDocumentoPessoal($documentoPessoal));
            }

            Morador::create(array_merge(['fk_local_id' => $local->loc_id], $payload));
        }
    }

    private function uploadDocumentoPessoal(UploadedFile $file): array
    {
        $path = $file->store('moradores/documentos', 'local');

        return [
            'mor_documento_pessoal_path' => $path,
            'mor_documento_pessoal_nome' => $file->getClientOriginalName(),
            'mor_documento_pessoal_mime' => $file->getClientMimeType(),
            'mor_documento_pessoal_tamanho' => $file->getSize(),
        ];
    }

    private function deleteDocumentoPessoal(Morador $morador): void
    {
        $path = (string) ($morador->mor_documento_pessoal_path ?? '');
        if ($path !== '' && Storage::disk('local')->exists($path)) {
            Storage::disk('local')->delete($path);
        }
    }

    /** Converte valor de número do local para integer ou null (coluna é integer). */
    private function normalizeLocNumero(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }
        $s = is_string($value) ? trim($value) : (string) $value;
        if ($s === '' || strtoupper($s) === 'N/A' || strtoupper($s) === 'S/N') {
            return null;
        }

        return is_numeric($s) ? (int) $s : null;
    }

    /** Regras de validação para sync de locais (sem ViaCEP). */
    private function localSyncRules(): array
    {
        return [
            'loc_cep' => ['required', 'string', 'max:20'],
            'loc_tipo' => ['required', 'string', 'in:R,C,T'],
            'loc_zona' => ['required', 'string', 'max:10'],
            'loc_endereco' => ['required', 'string', 'max:255'],
            'loc_numero' => ['nullable', 'string', 'max:20'],
            'loc_bairro' => ['required', 'string', 'max:100'],
            'loc_cidade' => ['required', 'string', 'max:100'],
            'loc_estado' => ['required', 'string', 'max:2'],
            'loc_pais' => ['required', 'string', 'max:100'],
            'loc_latitude' => ['required', 'string', 'max:20'],
            'loc_longitude' => ['required', 'string', 'max:20'],
            'loc_codigo' => ['required', 'string', 'max:50'],
            'loc_quarteirao' => ['nullable', 'string', 'max:50'],
            'loc_complemento' => ['nullable', 'string', 'max:255'],
            'loc_categoria' => ['nullable', 'string', 'max:100'],
            'loc_sequencia' => ['nullable', 'integer'],
            'loc_lado' => ['nullable', 'integer'],
            'loc_responsavel_nome' => ['nullable', 'string', 'max:255'],
        ];
    }
}
