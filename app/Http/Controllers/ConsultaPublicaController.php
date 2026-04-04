<?php

namespace App\Http\Controllers;

use App\Models\Doenca;
use App\Models\Local;
use App\Services\Vigilancia\ResumoVisitaCidadaoService;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\SvgWriter;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ConsultaPublicaController extends Controller
{
    public function index()
    {
        $doencasIndisponivel = false;
        try {
            $doencas = Doenca::query()->orderBy('doe_nome')->get();
        } catch (\Throwable $e) {
            report($e);
            $doencas = new Collection;
            $doencasIndisponivel = true;
        }

        return view('consulta.index', compact('doencas', 'doencasIndisponivel'));
    }

    public function consultaPorCodigo(Request $request)
    {
        $validated = $request->validate([
            'codigo' => ['required', 'digits:8'],
        ], [
            'codigo.required' => __('Informe o código do imóvel.'),
            'codigo.digits' => __('O código deve ter exatamente 8 dígitos numéricos.'),
        ]);

        $codigo = $validated['codigo'];

        try {
            $local = Local::where('loc_codigo_unico', $codigo)->first();

            if (! $local) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('erro', __('Código não encontrado. Verifique o número informado (8 dígitos) e tente novamente. Se o problema persistir, entre em contato com o agente que realizou a visita.'));
            }

            $visitas = $local->visitas()
                ->with('doencas')
                ->orderByDesc('vis_data')
                ->get();

            $visitas->each(fn ($v) => $v->setRelation('local', $local));

            $ascPorData = $visitas->sortBy(fn ($v) => [$v->vis_data, $v->vis_id])->values();
            $revisitaPosterior = [];
            for ($i = 0; $i < $ascPorData->count(); $i++) {
                $atual = $ascPorData[$i];
                for ($j = $i + 1; $j < $ascPorData->count(); $j++) {
                    if ($ascPorData[$j]->vis_data > $atual->vis_data) {
                        $revisitaPosterior[$atual->vis_id] = $ascPorData[$j];
                        break;
                    }
                }
            }

            $enderecoCompleto = $local->loc_endereco.', '.($local->loc_numero ?? 'S/N').' - '.$local->loc_bairro.', '.$local->loc_cidade.'/'.$local->loc_estado;
            $resumoService = app(ResumoVisitaCidadaoService::class);
            $resumos = [];
            foreach ($visitas as $v) {
                $resumos[$v->vis_id] = $resumoService->resumir($v, $enderecoCompleto);
            }

            $qrCode = new QrCode(
                data: route('consulta.codigo', ['codigo' => $local->loc_codigo_unico]),
                encoding: new Encoding('UTF-8'),
                errorCorrectionLevel: ErrorCorrectionLevel::Low,
                size: 250,
                margin: 10,
                roundBlockSizeMode: RoundBlockSizeMode::Margin,
                foregroundColor: new Color(0, 0, 0),
                backgroundColor: new Color(255, 255, 255)
            );

            try {
                $writer = new PngWriter;
                $result = $writer->write($qrCode);
                $qrCodeBase64 = base64_encode($result->getString());
                $qrCodeMime = 'image/png';
            } catch (\Throwable $e) {
                $writer = new SvgWriter;
                $result = $writer->write($qrCode);
                $qrCodeBase64 = base64_encode($result->getString());
                $qrCodeMime = 'image/svg+xml';
            }

            return view('consulta.codigo', compact('local', 'visitas', 'resumos', 'revisitaPosterior', 'qrCodeBase64', 'qrCodeMime'));
        } catch (\Throwable $e) {
            report($e);

            return redirect()
                ->route('consulta.index')
                ->withInput($request->only('codigo'))
                ->with('erro', __('Não foi possível consultar o imóvel no momento. Verifique sua conexão ou tente novamente em instantes.'));
        }
    }
}
