<?php

namespace App\Http\Controllers;

use App\Models\Visita;
use App\Models\Local;
use App\Models\Doenca;
use Illuminate\Http\Request;

class ConsultaPublicaController extends Controller
{
    public function index()
    {
        // Carrega visitas com local e doenças (sem dados sensíveis)
        $visitas = Visita::with(['local', 'doencas'])->get();

        // Total de visitas
        $totalVisitas = $visitas->count();

        // Doenças
        $doencas = Doenca::all();

        // Locais com pelo menos uma visita com doença marcada
        $locaisComFoco = $visitas->filter(fn($v) => $v->doencas->isNotEmpty())
                                ->pluck('local.loc_codigo_unico')
                                ->unique()
                                ->count();

        // Doença mais recorrente
        $todasDoencas = $visitas->flatMap(fn($v) => $v->doencas->pluck('doe_nome'));
        $doencaMaisRecorrente = $todasDoencas->countBy()->sortDesc()->keys()->first();

        // Bairro com mais ocorrências
        $bairros = $visitas->pluck('local.loc_bairro')->filter();
        $bairroMaisFrequente = $bairros->countBy()->sortDesc()->keys()->first();

        return view('consulta.index', compact(
            'visitas',
            'totalVisitas',
            'locaisComFoco',
            'doencaMaisRecorrente',
            'bairroMaisFrequente',
            'doencas'
        ));
    }

    public function consultaPorMatricula(Request $request)
    {
        $matricula = $request->input('matricula');

        $local = Local::where('loc_codigo_unico', $matricula)->first();

        if (!$local) {
            return redirect()->back()->with('erro', 'matrícula não encontrada.');
        }

        $visitas = $local->visitas()->with('doencas')->get();

        return view('consulta.matricula', compact('local', 'visitas'));
    }
}