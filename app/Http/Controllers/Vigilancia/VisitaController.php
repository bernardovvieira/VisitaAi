<?php

namespace App\Http\Controllers\Vigilancia;

use App\Helpers\LogHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\VisitaRequest;
use App\Models\Doenca;
use App\Models\Local;
use App\Models\User;
use App\Models\Visita;
use App\Services\Vigilancia\SugestaoDoencasService;
use App\Support\SmartSearch;
use App\Support\VisitaOcupantesObservacao;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class VisitaController extends Controller
{
    public function index(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        if ($request->has('enviadas')) {
            $n = (int) $request->input('enviadas');
            $erros = (int) $request->input('erros', 0);
            $msg = $n === 1
                ? __(':n visita enviada com sucesso.', ['n' => $n])
                : __(':n visitas enviadas com sucesso.', ['n' => $n]);
            if ($erros > 0) {
                $msg .= ' '.($erros === 1
                    ? __(':e visita não enviada. Verifique os dados.', ['e' => $erros])
                    : __(':e visitas não enviadas. Verifique os dados.', ['e' => $erros]));
            }
            session()->flash('success', $msg);

            return redirect()->to($request->url());
        }

        // Visita cadastrada offline: mensagem de sucesso (amarelo) na listagem
        if ($request->has('guardada')) {
            session()->flash('warning', __('Visita cadastrada no dispositivo com sucesso. Será sincronizada quando a conexão for estabelecida.'));

            return redirect()->to($request->url());
        }

        $busca = trim((string) $request->input('busca'));
        $buscaLower = mb_strtolower($busca);
        $terms = SmartSearch::terms($busca);

        $query = Visita::with(['local', 'doencas', 'usuario']);

        if ($busca !== '') {
            $query->where(function ($q) use ($terms, $buscaLower, $busca) {
                foreach ($terms as $term) {
                    $like = '%'.$term.'%';
                    $q->orWhereHas('local', function ($localQuery) use ($like) {
                        $localQuery->whereRaw('LOWER(COALESCE(loc_endereco, "")) LIKE ?', [$like])
                            ->orWhereRaw('LOWER(COALESCE(loc_numero, "")) LIKE ?', [$like])
                            ->orWhereRaw('LOWER(COALESCE(loc_complemento, "")) LIKE ?', [$like])
                            ->orWhereRaw('LOWER(COALESCE(loc_bairro, "")) LIKE ?', [$like])
                            ->orWhereRaw('LOWER(COALESCE(loc_cidade, "")) LIKE ?', [$like])
                            ->orWhereRaw('LOWER(COALESCE(loc_estado, "")) LIKE ?', [$like])
                            ->orWhereRaw('LOWER(COALESCE(loc_pais, "")) LIKE ?', [$like])
                            ->orWhereRaw('LOWER(COALESCE(loc_responsavel_nome, "")) LIKE ?', [$like])
                            ->orWhereRaw(SmartSearch::foldExpr('loc_responsavel_nome').' LIKE ?', [$like])
                            ->orWhereRaw('CAST(loc_cep AS CHAR) LIKE ?', [$like])
                            ->orWhereRaw('CAST(loc_codigo_unico AS CHAR) LIKE ?', [$like]);
                    })
                        ->orWhereHas('local.moradores', function ($moradoresQuery) use ($like) {
                            $moradoresQuery->whereRaw('LOWER(COALESCE(mor_nome, "")) LIKE ?', [$like])
                                ->orWhereRaw(SmartSearch::foldExpr('mor_nome').' LIKE ?', [$like])
                                ->orWhereRaw('LOWER(COALESCE(mor_profissao, "")) LIKE ?', [$like])
                                ->orWhereRaw('LOWER(COALESCE(mor_observacao, "")) LIKE ?', [$like])
                                ->orWhereRaw('CAST(mor_id AS CHAR) LIKE ?', [$like]);
                        })
                        ->orWhereHas('local.socioeconomico', function ($socioQuery) use ($like) {
                            $socioQuery->whereRaw('LOWER(COALESCE(lse_proprietario_nome, "")) LIKE ?', [$like])
                                ->orWhereRaw('LOWER(COALESCE(lse_proprietario_endereco, "")) LIKE ?', [$like])
                                ->orWhereRaw('LOWER(COALESCE(lse_proprietario_telefone, "")) LIKE ?', [$like])
                                ->orWhereRaw('LOWER(COALESCE(lse_telefone_contato, "")) LIKE ?', [$like])
                                ->orWhereRaw('LOWER(COALESCE(lse_observacoes_imovel, "")) LIKE ?', [$like]);
                        })
                        ->orWhereHas('usuario', function ($usuarioQuery) use ($like) {
                            $usuarioQuery->whereRaw('LOWER(COALESCE(use_nome, "")) LIKE ?', [$like])
                                ->orWhereRaw(SmartSearch::foldExpr('use_nome').' LIKE ?', [$like])
                                ->orWhereRaw('LOWER(COALESCE(use_email, "")) LIKE ?', [$like])
                                ->orWhereRaw('CAST(use_id AS CHAR) LIKE ?', [$like]);
                        })
                        ->orWhereHas('doencas', function ($doencaQuery) use ($like) {
                            $doencaQuery->whereRaw('LOWER(COALESCE(doe_nome, "")) LIKE ?', [$like])
                                ->orWhereRaw(SmartSearch::foldExpr('doe_nome').' LIKE ?', [$like])
                                ->orWhereRaw('CAST(doe_id AS CHAR) LIKE ?', [$like]);
                        })
                        ->orWhereRaw('LOWER(COALESCE(vis_observacoes, "")) LIKE ?', [$like])
                        ->orWhereRaw('LOWER(COALESCE(CAST(vis_ocupantes_observacoes AS CHAR), "")) LIKE ?', [$like])
                        ->orWhereRaw('CAST(vis_data AS CHAR) LIKE ?', [$like])
                        ->orWhereRaw('CAST(vis_ciclo AS CHAR) LIKE ?', [$like])
                        ->orWhereRaw('CAST(vis_atividade AS CHAR) LIKE ?', [$like]);
                }

                if (str_starts_with($buscaLower, 'pendente')) {
                    $q->orWhere('vis_pendencias', true);
                }
                if (str_starts_with($buscaLower, 'concluida') || str_starts_with($buscaLower, 'concluída')) {
                    $q->orWhere('vis_concluida', true);
                }

                $dataParsed = $this->parseBuscaData($busca);
                if ($dataParsed !== null) {
                    if (isset($dataParsed['date'])) {
                        $q->orWhereDate('vis_data', $dataParsed['date']);
                    } elseif (isset($dataParsed['day'])) {
                        $q->orWhereDay('vis_data', $dataParsed['day']);
                    }
                }
            });
        }

        if ($user->isAgenteSaude()) {
            $query->where('fk_usuario_id', $user->use_id)
                ->where('vis_atividade', '7');
            $view = 'saude.visitas.index';
        } elseif ($user->isAgenteEndemias()) {
            $view = 'agente.visitas.index';
        } else {
            $view = 'gestor.visitas.index';
        }

        $visitas = $query->orderByDesc('vis_data')
            ->paginate(10)
            ->appends(['busca' => $busca]);

        // NOVO: busca locais com pendência não revisitada
        $locaisComPendenciasNaoRevisitadas = Local::whereHas('visitas', fn ($q) => $q->where('vis_pendencias', true))
            ->with(['visitas' => fn ($q) => $q->orderByDesc('vis_data')])
            ->get()
            ->filter(function ($local) {
                $ultimaPendencia = $local->visitas->firstWhere('vis_pendencias', true);

                $temRevisita = $local->visitas->firstWhere(fn ($v) => ! $v->vis_pendencias && $v->vis_data > $ultimaPendencia->vis_data
                );

                return $ultimaPendencia && ! $temRevisita;
            });

        return view($view, compact('visitas', 'busca', 'locaisComPendenciasNaoRevisitadas'));
    }

    /**
     * Interpreta o termo de busca como data para filtrar visitas.
     * Aceita: dia só (ex: 30), DD/MM (ex: 30/05), DD/MM/AA (ex: 30/05/25), DD/MM/AAAA (ex: 30/05/2025).
     * Retorna null se não parecer data; ['day' => int] para dia do mês; ['date' => 'Y-m-d'] para data completa.
     */
    private function parseBuscaData(string $busca): ?array
    {
        $busca = trim($busca);
        if ($busca === '') {
            return null;
        }
        if (preg_match('/^\d{1,2}$/', $busca)) {
            $d = (int) $busca;
            if ($d >= 1 && $d <= 31) {
                return ['day' => $d];
            }
        }
        if (preg_match('/^(\d{1,2})\/(\d{1,2})$/', $busca, $m)) {
            $dia = (int) $m[1];
            $mes = (int) $m[2];
            if ($dia >= 1 && $dia <= 31 && $mes >= 1 && $mes <= 12) {
                $ano = (int) now()->format('Y');
                $date = sprintf('%04d-%02d-%02d', $ano, $mes, $dia);
                if (Carbon::createFromFormat('Y-m-d', $date) !== false) {
                    return ['date' => $date];
                }
            }
        }
        if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{2,4})$/', $busca, $m)) {
            $dia = (int) $m[1];
            $mes = (int) $m[2];
            $ano = (int) $m[3];
            if (strlen($m[3]) === 2) {
                $ano = 2000 + $ano;
            }
            if ($dia >= 1 && $dia <= 31 && $mes >= 1 && $mes <= 12 && $ano >= 2000 && $ano <= 2100) {
                $date = sprintf('%04d-%02d-%02d', $ano, $mes, $dia);
                if (Carbon::createFromFormat('Y-m-d', $date) !== false) {
                    return ['date' => $date];
                }
            }
        }

        return null;
    }

    public function show(Visita $visita)
    {
        $this->authorize('view', $visita);

        /** @var User $user */
        $user = Auth::user();
        $visita->load(['local.moradores']);

        $view = $user->isAgenteSaude()
            ? 'saude.visitas.show'
            : ($user->isAgenteEndemias() ? 'agente.visitas.show' : 'gestor.visitas.show');

        return view($view, compact('visita'));
    }

    public function create()
    {
        /** @var User $user */
        $user = Auth::user();
        $locais = Local::query()
            ->with(['moradores' => fn ($q) => $q->orderBy('mor_id')])
            ->orderByDesc('loc_id')
            ->get();
        $doencas = Doenca::all();

        $sugestoesDisponiveis = false;
        if ($user->isAgenteEndemias() || $user->isAgenteSaude()) {
            $sugestoesDisponiveis = Visita::whereHas('doencas')
                ->where('vis_data', '>=', now()->subMonths(SugestaoDoencasService::MESES_FREQUENCIA))
                ->exists()
                || (Doenca::count() > 0 && Doenca::whereNotNull('doe_sintomas')->exists());
        }

        $view = $user->isAgenteSaude()
            ? 'saude.visitas.create'
            : ($user->isAgenteEndemias() ? 'agente.visitas.create' : 'gestor.visitas.create');

        return view($view, compact('locais', 'doencas', 'sugestoesDisponiveis'));
    }

    public function store(VisitaRequest $request)
    {
        /** @var User $user */
        $user = Auth::user();
        $validated = $request->validated();
        $this->mergeMoradorObservacoesNaVisita($validated, $request);

        $existe = Visita::where('fk_usuario_id', $user->use_id)
            ->where('fk_local_id', $validated['fk_local_id'])
            ->whereDate('vis_data', $validated['vis_data'])
            ->where('vis_atividade', $validated['vis_atividade'])
            ->where('vis_ciclo', $validated['vis_ciclo'] ?? null)
            ->exists();

        if ($existe) {
            return redirect()
                ->back()
                ->withErrors(['duplicado' => 'Já existe uma visita para este local nesta data (e atividade/ciclo). Não é permitido lançar duas visitas no mesmo dia para o mesmo imóvel.'])
                ->withInput();
        }

        $doencas = $validated['doencas'] ?? [];
        unset($validated['doencas']);

        $tratamentos = $validated['tratamentos'] ?? [];
        unset($validated['tratamentos']);

        $validated['fk_usuario_id'] = $user->use_id;
        $validated['vis_coleta_amostra'] = $request->boolean('vis_coleta_amostra');
        $validated['vis_pendencias'] = $request->boolean('vis_pendencias');
        // Sem pendência ⇒ visita concluída (regra PNCD)
        $validated['vis_concluida'] = $request->boolean('vis_pendencias') ? $request->boolean('vis_concluida') : true;

        $visita = Visita::create($validated);
        $visita->doencas()->sync($doencas);

        foreach ($tratamentos as $t) {
            if (
                ! empty($t['trat_tipo']) &&
                ! empty($t['trat_forma']) &&
                (
                    (strtolower($t['trat_forma']) === 'focal' && (! empty($t['qtd_gramas']) || ! empty($t['qtd_depositos_tratados']))) ||
                    (strtolower($t['trat_forma']) === 'perifocal' && ! empty($t['qtd_cargas']))
                )
            ) {
                $visita->tratamentos()->create([
                    'trat_tipo' => $t['trat_tipo'],
                    'trat_forma' => $t['trat_forma'],
                    'linha' => isset($t['linha']) && $t['linha'] !== '' ? $t['linha'] : null,
                    'qtd_gramas' => isset($t['qtd_gramas']) && $t['qtd_gramas'] !== '' ? $t['qtd_gramas'] : null,
                    'qtd_depositos_tratados' => isset($t['qtd_depositos_tratados']) && $t['qtd_depositos_tratados'] !== '' ? $t['qtd_depositos_tratados'] : null,
                    'qtd_cargas' => isset($t['qtd_cargas']) && $t['qtd_cargas'] !== '' ? $t['qtd_cargas'] : null,
                ]);
            }
        }

        LogHelper::registrar(
            'Registro de visita',
            'Visita',
            'create',
            'Visita realizada no local: '.$visita->local->loc_endereco.', '.($visita->local->loc_numero ?: 'S/N')
        );

        return redirect()
            ->route($user->isAgenteSaude() ? 'saude.visitas.index' : 'agente.visitas.index')
            ->with('success', __('Visita registrada com sucesso.'))
            ->with('created_visita_id', $visita->vis_id);
    }

    /**
     * Exibe a tela de sincronização de visitas salvas offline (rascunhos).
     * Disponível apenas para ACE e ACS.
     */
    public function syncPage()
    {
        /** @var User $user */
        $user = Auth::user();
        if (! $user->isAgenteEndemias() && ! $user->isAgenteSaude()) {
            abort(403, 'Acesso negado.');
        }

        $visitasIndexRoute = $user->isAgenteSaude() ? route('saude.visitas.index') : route('agente.visitas.index');
        $visitasCreateRoute = $user->isAgenteSaude() ? route('saude.visitas.create') : route('agente.visitas.create');
        $syncSubmitUrl = $user->isAgenteSaude() ? route('saude.visitas.sync.submit') : route('agente.visitas.sync.submit');
        $perfil = $user->isAgenteSaude() ? 'saude' : 'agente';

        $locaisSyncSubmitUrl = $user->isAgenteEndemias() ? route('agente.locais.sync.submit') : null;
        $locaisIndexRoute = $user->isAgenteEndemias() ? route('agente.locais.index') : null;

        return view('visitas.sync', compact('visitasIndexRoute', 'visitasCreateRoute', 'syncSubmitUrl', 'perfil', 'locaisSyncSubmitUrl', 'locaisIndexRoute'));
    }

    /**
     * Recebe visitas salvas offline (rascunhos) e persiste no servidor.
     * Espera JSON: { "visitas": [ { "vis_data", "fk_local_id", ... } ] }
     * Retorna JSON: { "sincronizados": int, "erros": [ { "index": int, "message": string } ] }
     */
    public function syncStore(Request $request)
    {
        try {
            /** @var User $user */
            $user = Auth::user();
            if (! $user->isAgenteEndemias() && ! $user->isAgenteSaude()) {
                return response()->json(['message' => 'Acesso negado.'], 403);
            }

            $this->authorize('create', Visita::class);

            $payload = $request->validate([
                'visitas' => ['required', 'array', 'max:50'],
                'visitas.*' => ['array'],
            ]);

            $visitas = $payload['visitas'];
            $sincronizados = 0;
            $erros = [];

            foreach ($visitas as $index => $item) {
                $item = $this->prepareVisitaPayloadForSync($item, $user);
                $rules = VisitaRequest::rulesForUser($user);

                $validator = Validator::make($item, $rules);
                if ($validator->fails()) {
                    $erros[] = [
                        'index' => $index,
                        'message' => implode(' ', $validator->errors()->all()),
                    ];

                    continue;
                }

                $validated = $validator->validated();
                $doencas = $validated['doencas'] ?? [];
                unset($validated['doencas']);
                $tratamentos = $validated['tratamentos'] ?? [];
                unset($validated['tratamentos']);

                $rawMoradorObs = $item['morador_obs'] ?? [];
                if (is_string($rawMoradorObs)) {
                    $rawMoradorObs = json_decode($rawMoradorObs, true) ?: [];
                }
                $validated['vis_ocupantes_observacoes'] = VisitaOcupantesObservacao::fromInputArray(
                    is_array($rawMoradorObs) ? $rawMoradorObs : [],
                    (int) $validated['fk_local_id']
                );
                unset($validated['morador_obs']);

                $validated['fk_usuario_id'] = $user->use_id;
                $validated['vis_coleta_amostra'] = (bool) ($item['vis_coleta_amostra'] ?? false);
                $validated['vis_pendencias'] = (bool) ($item['vis_pendencias'] ?? false);
                $validated['vis_concluida'] = ($validated['vis_pendencias'] ?? false)
                    ? (bool) ($item['vis_concluida'] ?? false)
                    : true;

                $existe = Visita::where('fk_usuario_id', $user->use_id)
                    ->where('fk_local_id', $validated['fk_local_id'])
                    ->whereDate('vis_data', $validated['vis_data'])
                    ->where('vis_atividade', $validated['vis_atividade'])
                    ->where('vis_ciclo', $validated['vis_ciclo'] ?? null)
                    ->exists();
                if ($existe) {
                    $erros[] = [
                        'index' => $index,
                        'message' => 'Visita já registrada com estes dados (local, data, atividade e ciclo).',
                    ];

                    continue;
                }

                try {
                    $visita = Visita::create($validated);
                    $visita->doencas()->sync($doencas);

                    foreach ($tratamentos as $t) {
                        if (
                            ! empty($t['trat_tipo']) && ! empty($t['trat_forma']) &&
                            (
                                (strtolower($t['trat_forma'] ?? '') === 'focal' && (! empty($t['qtd_gramas']) || ! empty($t['qtd_depositos_tratados']))) ||
                                (strtolower($t['trat_forma'] ?? '') === 'perifocal' && ! empty($t['qtd_cargas']))
                            )
                        ) {
                            $visita->tratamentos()->create([
                                'trat_tipo' => $t['trat_tipo'],
                                'trat_forma' => $t['trat_forma'],
                                'linha' => isset($t['linha']) && $t['linha'] !== '' ? $t['linha'] : null,
                                'qtd_gramas' => isset($t['qtd_gramas']) && $t['qtd_gramas'] !== '' ? $t['qtd_gramas'] : null,
                                'qtd_depositos_tratados' => isset($t['qtd_depositos_tratados']) && $t['qtd_depositos_tratados'] !== '' ? $t['qtd_depositos_tratados'] : null,
                                'qtd_cargas' => isset($t['qtd_cargas']) && $t['qtd_cargas'] !== '' ? $t['qtd_cargas'] : null,
                            ]);
                        }
                    }

                    LogHelper::registrar(
                        'Sincronização offline',
                        'Visita',
                        'create',
                        'Visita sincronizada no local ID '.$visita->fk_local_id.', data '.$visita->vis_data
                    );

                    $sincronizados++;
                } catch (\Throwable $e) {
                    $erros[] = [
                        'index' => $index,
                        'message' => 'Erro ao salvar: '.$e->getMessage(),
                    ];
                }
            }

            return response()->json([
                'sincronizados' => $sincronizados,
                'erros' => $erros,
            ]);
        } catch (ValidationException $e) {
            throw $e;
        } catch (AuthorizationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            Log::error('syncStore: '.$e->getMessage(), ['exception' => $e]);

            return response()->json([
                'sincronizados' => 0,
                'erros' => [['index' => 0, 'message' => 'Erro no servidor: '.$e->getMessage()]],
            ], 500);
        }
    }

    /**
     * Normaliza o payload de uma visita vinda do cliente (tratamentos JSON, vis_atividade ACS, etc.).
     */
    private function prepareVisitaPayloadForSync(array $item, User $user): array
    {
        if ($user->use_perfil === 'agente_saude') {
            $item['vis_atividade'] = '7';
        }

        if (isset($item['tratamentos'])) {
            $trat = $item['tratamentos'];
            if (is_string($trat)) {
                $trat = json_decode($trat, true);
            }
            if (is_array($trat)) {
                $item['tratamentos'] = collect($trat)->filter(function ($t) {
                    return ! empty($t['trat_forma']) || ! empty($t['trat_tipo'])
                        || ! empty($t['linha']) || ! empty($t['qtd_gramas'])
                        || ! empty($t['qtd_depositos_tratados']) || ! empty($t['qtd_cargas']);
                })->map(function ($t) {
                    $tipo = isset($t['trat_tipo']) ? trim(ucfirst(strtolower((string) $t['trat_tipo']))) : null;
                    $forma = isset($t['trat_forma']) ? trim(ucfirst(strtolower((string) $t['trat_forma']))) : null;
                    $t['trat_tipo'] = ($tipo !== '' && in_array($tipo, ['Larvicida', 'Adulticida'], true)) ? $tipo : null;
                    $t['trat_forma'] = ($forma !== '' && in_array($forma, ['Focal', 'Perifocal'], true)) ? $forma : null;
                    if (array_key_exists('linha', $t) && $t['linha'] !== null && $t['linha'] !== '') {
                        $t['linha'] = (int) $t['linha'];
                    }

                    return $t;
                })->values()->toArray();
            }
        }

        if (isset($item['fk_local_id']) && ! is_int($item['fk_local_id'])) {
            $item['fk_local_id'] = (int) $item['fk_local_id'];
        }

        $vvt = $item['vis_visita_tipo'] ?? null;
        if ($vvt === '' || ($vvt !== null && ! in_array((string) $vvt, ['N', 'R'], true))) {
            $item['vis_visita_tipo'] = null;
        }
        if (isset($item['vis_atividade']) && $item['vis_atividade'] === '') {
            $item['vis_atividade'] = null;
        }
        if (array_key_exists('vis_ciclo', $item) && $item['vis_ciclo'] === '') {
            $item['vis_ciclo'] = null;
        }

        if (isset($item['morador_obs']) && is_string($item['morador_obs'])) {
            $decoded = json_decode($item['morador_obs'], true);
            $item['morador_obs'] = is_array($decoded) ? $decoded : [];
        }

        return $item;
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    private function mergeMoradorObservacoesNaVisita(array &$validated, Request $request): void
    {
        $raw = $request->input('morador_obs', []);
        if (! is_array($raw)) {
            $raw = [];
        }
        $validated['vis_ocupantes_observacoes'] = VisitaOcupantesObservacao::fromInputArray(
            $raw,
            (int) $validated['fk_local_id']
        );
        unset($validated['morador_obs']);
    }

    public function edit(Visita $visita)
    {
        /** @var User $user */
        $user = Auth::user();
        $visita->load(['local.moradores']);
        $locais = Local::query()
            ->with(['moradores' => fn ($q) => $q->orderBy('mor_id')])
            ->orderByDesc('loc_id')
            ->get();
        $doencas = Doenca::all();

        $sugestoesDisponiveis = false;
        if ($user->isAgenteEndemias() || $user->isAgenteSaude()) {
            $sugestoesDisponiveis = Visita::whereHas('doencas')
                ->where('vis_data', '>=', now()->subMonths(SugestaoDoencasService::MESES_FREQUENCIA))
                ->exists()
                || (Doenca::count() > 0 && Doenca::whereNotNull('doe_sintomas')->exists());
        }

        $view = $user->isAgenteSaude()
            ? 'saude.visitas.edit'
            : ($user->isAgenteEndemias() ? 'agente.visitas.edit' : 'gestor.visitas.edit');

        return view($view, compact('visita', 'locais', 'doencas', 'sugestoesDisponiveis'));
    }

    public function update(VisitaRequest $request, Visita $visita)
    {
        /** @var User $user */
        $user = Auth::user();
        $validated = $request->validated();
        $this->mergeMoradorObservacoesNaVisita($validated, $request);

        $doencas = $validated['doencas'] ?? [];
        unset($validated['doencas']);

        $tratamentos = $validated['tratamentos'] ?? [];
        unset($validated['tratamentos']);

        $validated['vis_coleta_amostra'] = $request->boolean('vis_coleta_amostra');
        $validated['vis_pendencias'] = $request->boolean('vis_pendencias');
        // Sem pendência ⇒ visita concluída (regra PNCD)
        $validated['vis_concluida'] = $request->boolean('vis_pendencias') ? $request->boolean('vis_concluida') : true;

        $visita->update($validated);
        $visita->doencas()->sync($doencas);

        if (! empty($tratamentos)) {
            $visita->tratamentos()->delete();

            foreach ($tratamentos as $t) {
                if (
                    ! empty($t['trat_tipo']) &&
                    ! empty($t['trat_forma']) &&
                    (
                        (strtolower($t['trat_forma']) === 'focal' && (! empty($t['qtd_gramas']) || ! empty($t['qtd_depositos_tratados']))) ||
                        (strtolower($t['trat_forma']) === 'perifocal' && ! empty($t['qtd_cargas']))
                    )
                ) {
                    $visita->tratamentos()->create([
                        'trat_tipo' => $t['trat_tipo'],
                        'trat_forma' => $t['trat_forma'],
                        'linha' => isset($t['linha']) && $t['linha'] !== '' ? $t['linha'] : null,
                        'qtd_gramas' => isset($t['qtd_gramas']) && $t['qtd_gramas'] !== '' ? $t['qtd_gramas'] : null,
                        'qtd_depositos_tratados' => isset($t['qtd_depositos_tratados']) && $t['qtd_depositos_tratados'] !== '' ? $t['qtd_depositos_tratados'] : null,
                        'qtd_cargas' => isset($t['qtd_cargas']) && $t['qtd_cargas'] !== '' ? $t['qtd_cargas'] : null,
                    ]);
                }
            }
        }

        LogHelper::registrar(
            'Edição de visita',
            'Visita',
            'update',
            'Visita atualizada no local: '.$visita->local->loc_endereco.', '.($visita->local->loc_numero ?: 'S/N')
        );

        if ($user->isAgenteSaude()) {
            return redirect()
                ->route('saude.visitas.index')
                ->with('success', __('Visita atualizada com sucesso.'));
        }

        return redirect()
            ->route('agente.visitas.edit', $visita)
            ->with('success', __('Visita atualizada com sucesso.'));
    }

    public function destroy(Visita $visita)
    {
        /** @var User $user */
        $user = Auth::user();
        $descricao = 'Visita removida do local: '.$visita->local->loc_endereco.', código: '.$visita->local->loc_codigo_unico;

        $visita->delete();

        LogHelper::registrar(
            'Exclusão de visita',
            'Visita',
            'delete',
            $descricao
        );

        return redirect()
            ->route($user->isAgenteSaude() ? 'saude.visitas.index' : 'agente.visitas.index')
            ->with('success', __('Visita excluída com sucesso.'));
    }
}
