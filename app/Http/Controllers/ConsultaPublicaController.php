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

        // Pega o primeiro local com cidade preenchida
        $primeiroLocal = Local::whereNotNull('loc_cidade')->first();
        $cidade = $primeiroLocal?->loc_cidade;
        $estado = $primeiroLocal?->loc_estado;

        // Fallback
        $coordenadas = ['lat' => -28.655, 'lng' => -52.425];

        // Busca coordenadas da cidade via Nominatim
        if ($cidade && $estado) {
            $query = urlencode("$cidade, $estado, Brasil");
            $url = "https://nominatim.openstreetmap.org/search?q=$query&format=json&limit=1";

            try {
                $response = Http::withoutVerifying()->get($url);
                $dados = $response->json();
                if (!empty($dados[0])) {
                    $coordenadas = [
                        'lat' => floatval($dados[0]['lat']),
                        'lng' => floatval($dados[0]['lon']),
                    ];
                }
            } catch (\Exception $e) {
                // falha silenciosa, usa fallback
            }
        }

        return view('consulta.index', compact('doencas', 'coordenadas'));
    }

    public function consultaPorMatricula(Request $request)
    {
        $codigo = $request->input('matricula');

        $local = Local::where('loc_codigo_unico', $codigo)->first();

        if (!$local) {
            return redirect()->back()->with('erro', 'Código não encontrado.');
        }

        $visitas = $local->visitas()->with('doencas')->get();

        return view('consulta.matricula', compact('local', 'visitas'));
    }
}