<?php

namespace App\Http\Controllers\Municipio;

use App\Helpers\LogHelper;
use App\Http\Controllers\Controller;
use App\Models\Morador;
use App\Services\Municipio\IndicadoresOcupantesMunicipioService;
use Illuminate\Http\RedirectResponse;
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

    public function exportCsv(IndicadoresOcupantesMunicipioService $indicadores): RedirectResponse|StreamedResponse
    {
        $this->authorize('isGestor');

        if (! Morador::query()->exists()) {
            return redirect()
                ->route('gestor.indicadores.ocupantes')
                ->with(
                    'warning',
                    (string) config('visitaai_municipio.indicadores.export_csv_flash_sem_dados')
                );
        }

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
