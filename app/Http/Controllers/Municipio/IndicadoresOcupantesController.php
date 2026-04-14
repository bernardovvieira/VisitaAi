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

    public function exportCsv(IndicadoresOcupantesMunicipioService $indicadores)
    {
        $this->authorize('isGestor');

        $csv = $indicadores->exportCsvGestor();
        $filename = 'indicadores_ocupantes_'.now()->format('Y-m-d_His').'.csv';

        return new StreamedResponse(function () use ($csv): void {
            echo $csv;
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    public function exportCadastroOcupantesCsv(IndicadoresOcupantesMunicipioService $indicadores)
    {
        $this->authorize('isGestor');

        $csv = $indicadores->exportCadastroOcupantesCsvGestor();
        $filename = 'cadastro_socioeconomico_ocupantes_'.now()->format('Y-m-d_His').'.csv';

        return new StreamedResponse(function () use ($csv): void {
            echo $csv;
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
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
        // Render and draw footer & page numbers server-side (fallback when embedded PHP is disabled)
        $pdf->render();

        $dom = $pdf->getDomPDF();
        $canvas = null;
        if (method_exists($dom, 'getCanvas')) {
            $canvas = $dom->getCanvas();
        } elseif (method_exists($dom, 'get_canvas')) {
            $canvas = $dom->get_canvas();
        }
        $fontMetrics = null;
        if (method_exists($dom, 'getFontMetrics')) {
            $fontMetrics = $dom->getFontMetrics();
        } elseif (method_exists($dom, 'get_font_metrics')) {
            $fontMetrics = $dom->get_font_metrics();
        }

        if ($canvas && $fontMetrics) {
            try {
                $font = $fontMetrics->getFont('dejavu sans', 'normal');
            } catch (\Throwable $e) {
                $font = $fontMetrics->getFont(null, 'normal');
            }

            // dimensions
            if (method_exists($canvas, 'get_width')) {
                $width = $canvas->get_width();
            } elseif (method_exists($canvas, 'getWidth')) {
                $width = $canvas->getWidth();
            } else {
                $width = 595;
            }
            if (method_exists($canvas, 'get_height')) {
                $height = $canvas->get_height();
            } elseif (method_exists($canvas, 'getHeight')) {
                $height = $canvas->getHeight();
            } else {
                $height = 842;
            }

            // CSS side margin in px; convert to pt
            $pxToPt = 72.0 / 96.0;
            $cssSideMarginPx = 20;
            $leftMargin = $cssSideMarginPx * $pxToPt;
            $rightMargin = $cssSideMarginPx * $pxToPt;

            $y = $height - 28;
            $leftText = 'Bitwise Technologies - Soluções digitais para eficiência e inovação';
            $pageText = 'Página {PAGE_NUM} / {PAGE_COUNT}';

            // compute text width using sample replacement for page count
            $pageCount = null;
            if (method_exists($canvas, 'get_page_count')) {
                $pageCount = $canvas->get_page_count();
            } elseif (method_exists($dom, 'get_page_count')) {
                $pageCount = $dom->get_page_count();
            }
            $samplePageNum = $pageCount ?: 1;
            $sampleText = str_replace(['{PAGE_NUM}', '{PAGE_COUNT}'], [(string) $samplePageNum, (string) ($pageCount ?: $samplePageNum)], $pageText);
            $w = $fontMetrics->getTextWidth($sampleText, $font, 8);

            $innerGutter = (float) env('DOMPDF_FOOTER_INNER_GUTTER', 2);
            $x = $width - $rightMargin - $innerGutter - $w;

            try {
                $canvas->page_text($leftMargin, $y, $leftText, $font, 8, [0,0,0]);
                $canvas->page_text($x, $y, $pageText, $font, 8, [0,0,0]);

                if (env('DOMPDF_FOOTER_DEBUG', true)) {
                    $canvas->page_text($leftMargin, $y - 8, 'L', $font, 6, [1,0,0]);
                    $canvas->page_text($width - $rightMargin, $y - 8, 'R', $font, 6, [1,0,0]);
                    $canvas->page_text($x, $y - 8, 'P', $font, 6, [0,0,1]);
                }
            } catch (\Throwable $e) {
                // ignore drawing errors
            }
        }

        $filename = 'cadastro_socioeconomico_ocupantes_'.now()->format('Y-m-d_His').'.pdf';

        return $pdf->download($filename);
    }
}
