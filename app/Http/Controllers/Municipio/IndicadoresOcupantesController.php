<?php

namespace App\Http\Controllers\Municipio;

use App\Helpers\LogHelper;
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

        LogHelper::registrar(
            'Exportação CSV dos indicadores municipais (ocupantes)',
            'IndicadoresMunicipio',
            'export',
            'Download de arquivo CSV com agregados por bairro e perfis informados',
            request()
        );

        return $indicadores->respostaDownloadCsv();
    }
}
