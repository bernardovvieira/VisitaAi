<?php

namespace App\Http\Controllers\Municipio;

use App\Http\Controllers\Controller;
use App\Services\Municipio\IndicadoresOcupantesMunicipioService;
use Symfony\Component\HttpFoundation\StreamedResponse;

class IndicadoresOcupantesController extends Controller
{
    public function index(IndicadoresOcupantesMunicipioService $indicadores)
    {
        $this->authorize('isGestor');

        return view('municipio.indicadores.ocupantes', [
            'painel' => $indicadores->painelCompleto(),
        ]);
    }

    public function exportCsv(IndicadoresOcupantesMunicipioService $indicadores): StreamedResponse
    {
        $this->authorize('isGestor');

        $csv = $indicadores->exportCsvGestor();
        $filename = 'indicadores_ocupantes_'.now()->format('Y-m-d_His').'.csv';

        return response()->streamDownload(function () use ($csv) {
            echo $csv;
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
