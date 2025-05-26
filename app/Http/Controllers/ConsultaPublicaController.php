<?php

namespace App\Http\Controllers;

use App\Models\Local;
use App\Models\Doenca;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ConsultaPublicaController extends Controller
{
    public function index()
    {
        $doencas = Doenca::all();

        return view('consulta.index', compact('doencas'));
    }

    public function consultaPorCodigo(Request $request)
    {
        $codigo = $request->input('codigo');

        $local = Local::where('loc_codigo_unico', $codigo)->first();

        if (!$local) {
            return redirect()->back()->with('erro', 'código não encontrado.');
        }

        $visitas = $local->visitas()->with('doencas')->get();

        return view('consulta.codigo', compact('local', 'visitas'));
    }
}