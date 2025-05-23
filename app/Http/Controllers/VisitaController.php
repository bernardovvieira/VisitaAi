<?php

namespace App\Http\Controllers;

use App\Models\Visita;
use App\Models\Local;
use App\Models\Doenca;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VisitaController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $visitas = Visita::with(['local', 'doenca'])
                         ->orderBy('vis_data', 'desc')
                         ->paginate(10);

        $view = $user->isAgente()
            ? 'agente.visitas.index'
            : 'gestor.visitas.index';

        return view($view, compact('visitas'));
    }

    public function show(Visita $visita)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $view = $user->isAgente()
            ? 'agente.visitas.show'
            : 'gestor.visitas.show';

        return view($view, compact('visita'));
    }

    public function create()
    {
        $locais  = Local::all();
        $doencas = Doenca::all();

        return view('agente.visitas.create', compact('locais', 'doencas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vis_data'        => 'required|date',
            'vis_observacoes' => 'nullable|string',
            'fk_local_id'     => 'required|exists:locais,loc_id',
            'fk_usuario_id'   => 'required|exists:users,use_id',
            'fk_doenca_id'    => 'required|exists:doencas,doe_id',
        ]);

        Visita::create($validated);

        return redirect()
            ->route('agente.visitas.index')
            ->with('success', 'Visita registrada com sucesso.');
    }

    public function edit(Visita $visita)
    {
        $locais  = Local::all();
        $doencas = Doenca::all();

        return view('agente.visitas.edit', compact('visita', 'locais', 'doencas'));
    }

    public function update(Request $request, Visita $visita)
    {
        $validated = $request->validate([
            'vis_data'        => 'required|date',
            'vis_observacoes' => 'nullable|string',
            'fk_local_id'     => 'required|exists:locais,loc_id',
            'fk_usuario_id'   => 'required|exists:users,use_id',
            'fk_doenca_id'    => 'required|exists:doencas,doe_id',
        ]);

        $visita->update($validated);

        return redirect()
            ->route('agente.visitas.index')
            ->with('success', 'Visita atualizada com sucesso.');
    }

    public function destroy(Visita $visita)
    {
        $visita->delete();

        return redirect()
            ->route('agente.visitas.index')
            ->with('success', 'Visita excluída com sucesso.');
    }
}