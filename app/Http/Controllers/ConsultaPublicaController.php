<?php

namespace App\Http\Controllers;

use App\Models\Local;
use App\Models\Doenca;
use Illuminate\Http\Request;

class ConsultaPublicaController extends Controller
{
    public function index()
    {
        $doencas = Doenca::all();

        return view('consulta.index', compact('doencas'));
    }

    public function consultaPorCodigo(Request $request)
    {
        $validated = $request->validate([
            'codigo' => ['required', 'digits:8'],
        ], [
            'codigo.required' => 'Informe o código do imóvel.',
            'codigo.digits'   => 'O código deve ter exatamente 8 dígitos numéricos.',
        ]);

        $codigo = $validated['codigo'];

        $local = Local::where('loc_codigo_unico', $codigo)->first();

        if (! $local) {
            return redirect()
                ->back()
                ->withInput()
                ->with('erro', 'Código não encontrado. Verifique o número informado (8 dígitos) e tente novamente. Se o problema persistir, entre em contato com o agente que realizou a visita.');
        }

        $visitas = $local->visitas()
            ->with('doencas')
            ->orderByDesc('vis_data')
            ->get();

        return view('consulta.codigo', compact('local', 'visitas'));
    }
}