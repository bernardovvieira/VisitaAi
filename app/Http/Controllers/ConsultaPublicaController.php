<?php

namespace App\Http\Controllers;

use App\Models\Local;
use App\Models\Doenca;
use App\Services\ResumoVisitaCidadaoService;
use Illuminate\Http\Request;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\SvgWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Color\Color;

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

        $enderecoCompleto = $local->loc_endereco . ', ' . ($local->loc_numero ?? 'S/N') . ' - ' . $local->loc_bairro . ', ' . $local->loc_cidade . '/' . $local->loc_estado;
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
            $writer = new PngWriter();
            $result = $writer->write($qrCode);
            $qrCodeBase64 = base64_encode($result->getString());
            $qrCodeMime = 'image/png';
        } catch (\Throwable $e) {
            $writer = new SvgWriter();
            $result = $writer->write($qrCode);
            $qrCodeBase64 = base64_encode($result->getString());
            $qrCodeMime = 'image/svg+xml';
        }

        return view('consulta.codigo', compact('local', 'visitas', 'resumos', 'qrCodeBase64', 'qrCodeMime'));
    }
}