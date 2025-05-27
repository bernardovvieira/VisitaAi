<?php

namespace App\Http\Controllers\Saude;

use App\Http\Controllers\Controller;
use App\Models\Visita;
use App\Models\Local;
use App\Models\Doenca;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\VisitaRequest;

class VisitaController extends Controller
{
    public function index()
    {
        $visitas = Visita::with(['local'])
            ->where('fk_usuario_id', Auth::id())
            ->where('vis_tipo', 'LIRAa')
            ->orderByDesc('vis_data')
            ->paginate(10);

        return view('saude.visitas.index', compact('visitas'));
    }

    public function create()
    {
        $locais  = Local::all();
        $doencas = Doenca::all();
        $tiposPermitidos = ['LIRAa']; // fixo

        return view('saude.visitas.create', compact('locais', 'doencas', 'tiposPermitidos'));
    }

    public function store(VisitaRequest $request)
    {
        $validated = $request->validated();
        $doencas = $validated['doencas'] ?? [];
        unset($validated['doencas']);

        $validated['fk_usuario_id'] = Auth::id();
        $validated['vis_tipo'] = 'LIRAa'; // força o tipo

        $visita = Visita::create($validated);
        $visita->doencas()->sync($doencas);

        return redirect()->route('saude.visitas.index')->with('success', 'Visita registrada com sucesso.');
    }
}