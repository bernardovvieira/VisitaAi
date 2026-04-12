<?php

namespace App\Http\Controllers\Municipio;

use App\Exports\OcupantesIndicadoresExport;
use App\Exports\OcupantesCadastroExportOnly;
use App\Http\Controllers\Controller;
use App\Services\Municipio\IndicadoresOcupantesMunicipioService;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
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

    public function exportCsv(IndicadoresOcupantesMunicipioService $indicadores)
    {
        $this->authorize('isGestor');

        $filename = 'indicadores_ocupantes_'.now()->format('Y-m-d_His').'.xlsx';

        return Excel::download(new OcupantesIndicadoresExport(), $filename);
    }

    public function exportCadastroOcupantesCsv(IndicadoresOcupantesMunicipioService $indicadores)
    {
        $this->authorize('isGestor');

        $filename = 'cadastro_socioeconomico_ocupantes_'.now()->format('Y-m-d_His').'.xlsx';

        return Excel::download(new OcupantesCadastroExportOnly(), $filename);
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
