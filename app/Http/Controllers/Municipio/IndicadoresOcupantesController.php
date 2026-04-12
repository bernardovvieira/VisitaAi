<?php

namespace App\Http\Controllers\Municipio;

use App\Http\Controllers\Controller;
use App\Services\Municipio\IndicadoresOcupantesMunicipioService;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\StreamedResponse;

class IndicadoresOcupantesController extends Controller
{
    public function index(IndicadoresOcupantesMunicipioService $indicadores)
    {
        $this->authorize('isGestor');

        $locaisMapa = $indicadores->locaisComCoordenadasParaMapa()
            ->map(function (array $local): array {
                $local['url'] = route('gestor.locais.show', ['local' => $local['loc_id']]);

                return $local;
            })
            ->all();

        return view('municipio.indicadores.ocupantes', [
            'painel' => $indicadores->painelCompleto(),
            'locaisMapa' => $locaisMapa,
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

    public function exportCadastroOcupantesCsv(IndicadoresOcupantesMunicipioService $indicadores): StreamedResponse
    {
        $this->authorize('isGestor');

        $csv = $indicadores->exportCadastroOcupantesCsvGestor();
        $filename = 'cadastro_socioeconomico_ocupantes_'.now()->format('Y-m-d_His').'.csv';

        return response()->streamDownload(function () use ($csv) {
            echo $csv;
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function exportCadastroOcupantesPdf(IndicadoresOcupantesMunicipioService $indicadores)
    {
        $this->authorize('isGestor');

        $dados = $indicadores->dadosCadastroOcupantesGestor();
        $pdf = Pdf::loadView('pdf.cadastro_socioeconomico_ocupantes', [
            'locais' => $dados,
            'titulos' => config('visitaai_socioeconomico.secao_titulos', []),
        ])->setPaper('a4', 'portrait');

        $filename = 'cadastro_socioeconomico_ocupantes_'.now()->format('Y-m-d_His').'.pdf';

        return $pdf->download($filename);
    }
}
