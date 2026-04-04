<?php

namespace App\Http\Controllers\Municipio;

use App\Http\Controllers\Controller;
use App\Services\Municipio\IndicadoresOcupantesMunicipioService;

class IndicadoresOcupantesController extends Controller
{
    public function index(IndicadoresOcupantesMunicipioService $indicadores)
    {
        $this->authorize('isGestor');

        return view('municipio.indicadores.ocupantes', [
            'painel' => $indicadores->painelCompleto(),
        ]);
    }
}
