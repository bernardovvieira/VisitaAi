<?php

namespace App\Http\Controllers;

use App\Models\{Visita, Local, Doenca};
use App\Http\Requests\VisitaRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\LogHelper;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class VisitaController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $busca = $request->input('busca');

        $query = Visita::with(['local', 'doencas', 'usuario']);

        if ($busca) {
            $query->where(function ($q) use ($busca) {
                $q->whereHas('local', fn($q) => $q->where('loc_endereco', 'like', "%$busca%"))
                ->orWhereHas('local', fn($q) => $q->where('loc_codigo_unico', '=', $busca))
                ->orWhereHas('usuario', fn($q) => $q->where('use_nome', 'like', "%$busca%"))
                ->orWhereHas('doencas', fn($q) => $q->where('doe_nome', 'like', "%$busca%"))
                ->orWhere('vis_atividade', 'like', "%$busca%");
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

        $view = $user->isAgenteSaude()
            ? 'saude.visitas.create'
            : ($user->isAgenteEndemias() ? 'agente.visitas.create' : 'gestor.visitas.create');

        return view($view, compact('locais', 'doencas'));
    }

    public function store(VisitaRequest $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $validated = $request->validated();

        $doencas = $validated['doencas'] ?? [];
        unset($validated['doencas']);

        $tratamentos = $validated['tratamentos'] ?? [];
        unset($validated['tratamentos']);

        $validated['fk_usuario_id'] = $user->use_id;
        $validated['vis_coleta_amostra'] = $request->boolean('vis_coleta_amostra');
        $validated['vis_concluida'] = $request->boolean('vis_concluida');
        $validated['vis_pendencias'] = $request->boolean('vis_pendencias');

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
            ->with('success', 'Visita registrada com sucesso.');
    }

    public function edit(Visita $visita)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $locais = Local::all();
        $doencas = Doenca::all();

        $view = $user->isAgenteSaude()
            ? 'saude.visitas.edit'
            : ($user->isAgenteEndemias() ? 'agente.visitas.edit' : 'gestor.visitas.edit');

        return view($view, compact('visita', 'locais', 'doencas'));
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
        $validated['vis_concluida'] = $request->boolean('vis_concluida');
        $validated['vis_pendencias'] = $request->boolean('vis_pendencias');

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