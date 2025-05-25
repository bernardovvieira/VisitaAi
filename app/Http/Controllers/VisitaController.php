<?php

namespace App\Http\Controllers;

use App\Models\Visita;
use App\Models\Local;
use App\Models\Doenca;
use App\Http\Requests\VisitaRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VisitaController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $busca = $request->input('busca');

        $visitas = Visita::with(['local', 'doencas', 'usuario'])
            ->when($busca, function ($query) use ($busca) {
                $query->whereHas('local', function ($q) use ($busca) {
                    $q->where('loc_endereco', 'like', '%' . $busca . '%');
                })
                ->orWhereHas('local', function ($q) use ($busca) {
                    $q->where('loc_codigo_unico', '=', $busca);
                })
                ->orWhereHas('usuario', function ($q) use ($busca) {
                    $q->where('use_nome', 'like', '%' . $busca . '%');
                })
                ->orWhereHas('doencas', function ($q) use ($busca) {
                    $q->where('doe_nome', 'like', '%' . $busca . '%');
                });
            })
            ->orderByDesc('vis_data')
            ->paginate(10)
            ->appends(['busca' => $busca]);

        $view = $user->isAgente()
            ? 'agente.visitas.index'
            : 'gestor.visitas.index';

        return view($view, compact('visitas', 'busca'));
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

    public function store(VisitaRequest $request)
    {
        $validated = $request->validated();
        $doencas = $validated['doencas'] ?? [];
        unset($validated['doencas']);

        $validated['fk_usuario_id'] = Auth::id();

        $visita = Visita::create($validated);
        $visita->doencas()->sync($doencas);

        return redirect()->route('agente.visitas.index')->with('success', 'Visita registrada com sucesso.');
    }

    public function edit(Visita $visita)
    {
        $locais  = Local::all();
        $doencas = Doenca::all();

        return view('agente.visitas.edit', compact('visita', 'locais', 'doencas'));
    }

    public function update(VisitaRequest $request, Visita $visita)
    {
        $validated = $request->validated();
        $doencas = $validated['doencas'] ?? [];
        unset($validated['doencas']);

        $visita->update($validated);
        $visita->doencas()->sync($doencas);

        return redirect()->route('agente.visitas.index')->with('success', 'Visita atualizada com sucesso.');
    }

    public function destroy(Visita $visita)
    {
        $visita->delete();

        return redirect()
            ->route('agente.visitas.index')
            ->with('success', 'Visita excluída com sucesso.');
    }
}