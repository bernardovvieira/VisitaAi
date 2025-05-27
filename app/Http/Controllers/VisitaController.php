<?php

namespace App\Http\Controllers;

use App\Models\{Visita, Local, Doenca};
use App\Http\Requests\VisitaRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\LogHelper;

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
                  ->orWhere('vis_tipo', 'like', "%$busca%");
            });
        }

        if ($user->isAgenteSaude()) {
            $query->where('fk_usuario_id', $user->use_id)->where('vis_tipo', 'LIRAa');
            $view = 'saude.visitas.index';
        } elseif ($user->isAgenteEndemias()) {
            $query->where('fk_usuario_id', $user->use_id);
            $view = 'agente.visitas.index';
        } else {
            $view = 'gestor.visitas.index';
        }

        $visitas = $query->orderByDesc('vis_data')->paginate(10)->appends(['busca' => $busca]);

        return view($view, compact('visitas', 'busca'));
    }

    public function show(Visita $visita)
    {
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

        $tiposPermitidos = $user->isAgenteSaude()
            ? ['LIRAa']
            : ['LI+T', 'LIRAa'];

        $view = $user->isAgenteSaude()
            ? 'saude.visitas.create'
            : ($user->isAgenteEndemias() ? 'agente.visitas.create' : 'gestor.visitas.create');

        return view($view, compact('locais', 'doencas', 'tiposPermitidos'));
    }

    public function store(VisitaRequest $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $validated = $request->validated();
        $doencas = $validated['doencas'] ?? [];
        unset($validated['doencas']);

        $validated['fk_usuario_id'] = $user->use_id;

        if ($user->isAgenteSaude()) {
            $validated['vis_tipo'] = 'LIRAa';
        }

        $visita = Visita::create($validated);
        $visita->doencas()->sync($doencas);

        LogHelper::registrar(
            'Registro de visita',
            'Visita',
            'create',
            'Visita realizada no local: ' . $visita->local->loc_endereco . ', ' . $visita->local->loc_numero
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

        $visita->update($validated);
        $visita->doencas()->sync($doencas);

        LogHelper::registrar(
            'Edição de visita',
            'Visita',
            'update',
            'Visita atualizada no local: ' . $visita->local->loc_endereco . ', ' . $visita->local->loc_numero
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