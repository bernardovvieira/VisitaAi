<?php

namespace App\Http\Controllers;

use App\Models\{Visita, Local, Doenca};
use App\Http\Requests\VisitaRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Helpers\LogHelper;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Carbon\Carbon;

class VisitaController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($request->has('enviadas')) {
            $n = (int) $request->input('enviadas');
            $erros = (int) $request->input('erros', 0);
            $msg = $n . ' visita(s) enviada(s) com sucesso.';
            if ($erros > 0) {
                $msg .= ' ' . $erros . ' não enviada(s) (verifique os dados).';
            }
            session()->flash('success', $msg);
            return redirect()->to($request->url());
        }

        if ($request->has('guardada')) {
            session()->flash('warning', 'Visita guardada no dispositivo. Será sincronizada quando a conexão for estabelecida.');
            return redirect()->to($request->url());
        }

        $busca = trim((string) $request->input('busca'));

        $query = Visita::with(['local', 'doencas', 'usuario']);

        if ($busca !== '') {
            $query->where(function ($q) use ($busca) {
                $q->whereHas('local', fn($q) => $q->where('loc_endereco', 'like', "%$busca%"))
                ->orWhereHas('local', fn($q) => $q->where('loc_codigo_unico', '=', $busca))
                ->orWhereHas('local', fn($q) => $q->where('loc_responsavel_nome', 'like', "%$busca%"))
                ->orWhereHas('usuario', fn($q) => $q->where('use_nome', 'like', "%$busca%"))
                ->orWhereHas('doencas', fn($q) => $q->where('doe_nome', 'like', "%$busca%"))
                ->orWhere('vis_atividade', 'like', "%$busca%");

                $buscaLower = mb_strtolower($busca);
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
        $locaisComPendenciasNaoRevisitadas = \App\Models\Local::whereHas('visitas', fn($q) => $q->where('vis_pendencias', true))
            ->with(['visitas' => fn($q) => $q->orderByDesc('vis_data')])
            ->get()
            ->filter(function ($local) {
                $ultimaPendencia = $local->visitas->firstWhere('vis_pendencias', true);

                $temRevisita = $local->visitas->firstWhere(fn($v) =>
                    !$v->vis_pendencias && $v->vis_data > $ultimaPendencia->vis_data
                );

                return $ultimaPendencia && !$temRevisita;
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

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $view = $user->isAgenteSaude()
            ? 'saude.visitas.show'
            : ($user->isAgenteEndemias() ? 'agente.visitas.show' : 'gestor.visitas.show');

        return view($view, compact('visita'));
    }

    public function create()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $locais = Local::all();
        $doencas = Doenca::all();

        $sugestoesDisponiveis = false;
        if ($user->isAgenteEndemias() || $user->isAgenteSaude()) {
            $sugestoesDisponiveis = Visita::whereHas('doencas')
                ->where('vis_data', '>=', now()->subMonths(\App\Services\SugestaoDoencasService::MESES_FREQUENCIA))
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
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $validated = $request->validated();

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
                !empty($t['trat_tipo']) &&
                !empty($t['trat_forma']) &&
                (
                    (strtolower($t['trat_forma']) === 'focal' && (!empty($t['qtd_gramas']) || !empty($t['qtd_depositos_tratados']))) ||
                    (strtolower($t['trat_forma']) === 'perifocal' && !empty($t['qtd_cargas']))
                )
            ) {
                $visita->tratamentos()->create([
                    'trat_tipo'               => $t['trat_tipo'],
                    'trat_forma'              => $t['trat_forma'],
                    'linha'                   => isset($t['linha']) && $t['linha'] !== '' ? $t['linha'] : null,
                    'qtd_gramas'              => isset($t['qtd_gramas']) && $t['qtd_gramas'] !== '' ? $t['qtd_gramas'] : null,
                    'qtd_depositos_tratados'  => isset($t['qtd_depositos_tratados']) && $t['qtd_depositos_tratados'] !== '' ? $t['qtd_depositos_tratados'] : null,
                    'qtd_cargas'              => isset($t['qtd_cargas']) && $t['qtd_cargas'] !== '' ? $t['qtd_cargas'] : null,
                ]);
            }
        }

        LogHelper::registrar(
            'Registro de visita',
            'Visita',
            'create',
            'Visita realizada no local: ' . $visita->local->loc_endereco . ', ' . ($visita->local->loc_numero ?: 'S/N')
        );

        return redirect()
            ->route($user->isAgenteSaude() ? 'saude.visitas.index' : 'agente.visitas.index')
            ->with('success', 'Visita registrada com sucesso.')
            ->with('created_visita_id', $visita->vis_id);
    }

    /**
     * Exibe a tela de sincronização de visitas salvas offline (rascunhos).
     * Disponível apenas para ACE e ACS.
     */
    public function syncPage()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user->isAgenteEndemias() && !$user->isAgenteSaude()) {
            abort(403, 'Acesso negado.');
        }

        $visitasIndexRoute = $user->isAgenteSaude() ? route('saude.visitas.index') : route('agente.visitas.index');
        $visitasCreateRoute = $user->isAgenteSaude() ? route('saude.visitas.create') : route('agente.visitas.create');
        $syncSubmitUrl = $user->isAgenteSaude() ? route('saude.visitas.sync.submit') : route('agente.visitas.sync.submit');
        $perfil = $user->isAgenteSaude() ? 'saude' : 'agente';

        return view('visitas.sync', compact('visitasIndexRoute', 'visitasCreateRoute', 'syncSubmitUrl', 'perfil'));
    }

    /**
     * Recebe visitas salvas offline (rascunhos) e persiste no servidor.
     * Espera JSON: { "visitas": [ { "vis_data", "fk_local_id", ... } ] }
     * Retorna JSON: { "sincronizados": int, "erros": [ { "index": int, "message": string } ] }
     */
    public function syncStore(Request $request)
    {
        try {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            if (!$user->isAgenteEndemias() && !$user->isAgenteSaude()) {
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
                            !empty($t['trat_tipo']) && !empty($t['trat_forma']) &&
                            (
                                (strtolower($t['trat_forma'] ?? '') === 'focal' && (!empty($t['qtd_gramas']) || !empty($t['qtd_depositos_tratados']))) ||
                                (strtolower($t['trat_forma'] ?? '') === 'perifocal' && !empty($t['qtd_cargas']))
                            )
                        ) {
                            $visita->tratamentos()->create([
                                'trat_tipo'               => $t['trat_tipo'],
                                'trat_forma'              => $t['trat_forma'],
                                'linha'                   => isset($t['linha']) && $t['linha'] !== '' ? $t['linha'] : null,
                                'qtd_gramas'              => isset($t['qtd_gramas']) && $t['qtd_gramas'] !== '' ? $t['qtd_gramas'] : null,
                                'qtd_depositos_tratados'   => isset($t['qtd_depositos_tratados']) && $t['qtd_depositos_tratados'] !== '' ? $t['qtd_depositos_tratados'] : null,
                                'qtd_cargas'              => isset($t['qtd_cargas']) && $t['qtd_cargas'] !== '' ? $t['qtd_cargas'] : null,
                            ]);
                        }
                    }

                    LogHelper::registrar(
                        'Sincronização offline',
                        'Visita',
                        'create',
                        'Visita sincronizada no local ID ' . $visita->fk_local_id . ', data ' . $visita->vis_data
                    );

                    $sincronizados++;
                } catch (\Throwable $e) {
                    $erros[] = [
                        'index' => $index,
                        'message' => 'Erro ao salvar: ' . $e->getMessage(),
                    ];
                }
            }

            return response()->json([
                'sincronizados' => $sincronizados,
                'erros' => $erros,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            \Log::error('syncStore: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'sincronizados' => 0,
                'erros' => [['index' => 0, 'message' => 'Erro no servidor: ' . $e->getMessage()]],
            ], 500);
        }
    }

    /**
     * Normaliza o payload de uma visita vinda do cliente (tratamentos JSON, vis_atividade ACS, etc.).
     */
    private function prepareVisitaPayloadForSync(array $item, \App\Models\User $user): array
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
                    return !empty($t['trat_forma']) || !empty($t['trat_tipo'])
                        || !empty($t['linha']) || !empty($t['qtd_gramas'])
                        || !empty($t['qtd_depositos_tratados']) || !empty($t['qtd_cargas']);
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

        if (isset($item['fk_local_id']) && !is_int($item['fk_local_id'])) {
            $item['fk_local_id'] = (int) $item['fk_local_id'];
        }

        $vvt = $item['vis_visita_tipo'] ?? null;
        if ($vvt === '' || ($vvt !== null && !in_array((string) $vvt, ['N', 'R'], true))) {
            $item['vis_visita_tipo'] = null;
        }
        if (isset($item['vis_atividade']) && $item['vis_atividade'] === '') {
            $item['vis_atividade'] = null;
        }
        if (array_key_exists('vis_ciclo', $item) && $item['vis_ciclo'] === '') {
            $item['vis_ciclo'] = null;
        }

        return $item;
    }

    public function edit(Visita $visita)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $locais = Local::all();
        $doencas = Doenca::all();

        $sugestoesDisponiveis = false;
        if ($user->isAgenteEndemias() || $user->isAgenteSaude()) {
            $sugestoesDisponiveis = Visita::whereHas('doencas')
                ->where('vis_data', '>=', now()->subMonths(\App\Services\SugestaoDoencasService::MESES_FREQUENCIA))
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
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $validated = $request->validated();

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

        if (!empty($tratamentos)) {
            $visita->tratamentos()->delete();

            foreach ($tratamentos as $t) {
                if (
                    !empty($t['trat_tipo']) &&
                    !empty($t['trat_forma']) &&
                    (
                        (strtolower($t['trat_forma']) === 'focal' && (!empty($t['qtd_gramas']) || !empty($t['qtd_depositos_tratados']))) ||
                        (strtolower($t['trat_forma']) === 'perifocal' && !empty($t['qtd_cargas']))
                    )
                ) {
                    $visita->tratamentos()->create([
                        'trat_tipo'               => $t['trat_tipo'],
                        'trat_forma'              => $t['trat_forma'],
                        'linha'                   => isset($t['linha']) && $t['linha'] !== '' ? $t['linha'] : null,
                        'qtd_gramas'              => isset($t['qtd_gramas']) && $t['qtd_gramas'] !== '' ? $t['qtd_gramas'] : null,
                        'qtd_depositos_tratados'  => isset($t['qtd_depositos_tratados']) && $t['qtd_depositos_tratados'] !== '' ? $t['qtd_depositos_tratados'] : null,
                        'qtd_cargas'              => isset($t['qtd_cargas']) && $t['qtd_cargas'] !== '' ? $t['qtd_cargas'] : null,
                    ]);
                }
            }
        }

        LogHelper::registrar(
            'Edição de visita',
            'Visita',
            'update',
            'Visita atualizada no local: ' . $visita->local->loc_endereco . ', ' . ($visita->local->loc_numero ?: 'S/N')
        );

        return redirect()
            ->route($user->isAgenteSaude() ? 'saude.visitas.index' : 'agente.visitas.index')
            ->with('success', 'Visita atualizada com sucesso.');
    }

    public function destroy(Visita $visita)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $descricao = 'Visita removida do local: ' . $visita->local->loc_endereco . ', código: ' . $visita->local->loc_codigo_unico;

        $visita->delete();

        LogHelper::registrar(
            'Exclusão de visita',
            'Visita',
            'delete',
            $descricao
        );

        return redirect()
            ->route($user->isAgenteSaude() ? 'saude.visitas.index' : 'agente.visitas.index')
            ->with('success', 'Visita excluída com sucesso.');
    }
}