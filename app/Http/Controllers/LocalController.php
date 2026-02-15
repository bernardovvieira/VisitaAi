<?php

namespace App\Http\Controllers;

use App\Models\Local;
use App\Http\Requests\LocalRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\LogHelper;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\SvgWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Color\Color;


class LocalController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $search = strtolower(trim($request->input('search')));

        $query = Local::query();

        if ($search) {
            // Resolver tipo de imóvel por fragmento
            $tipo = null;
            if (str_starts_with($search, 'res')) {
                $tipo = 'R';
            } elseif (str_starts_with($search, 'com')) {
                $tipo = 'C';
            } elseif (str_starts_with($search, 'ter')) {
                $tipo = 'T';
            } elseif (in_array($search, ['r', 'c', 't'])) {
                $tipo = strtoupper($search);
            }

            // Resolver zona por fragmento
            $zona = null;
            if (str_starts_with($search, 'urb')) {
                $zona = 'U';
            } elseif (str_starts_with($search, 'rur')) {
                $zona = 'R';
            }

            $query->where(function ($q) use ($search, $tipo, $zona) {
                $q->whereRaw("LOWER(loc_endereco) LIKE ?", ["%{$search}%"])
                ->orWhereRaw("LOWER(loc_bairro) LIKE ?", ["%{$search}%"])
                ->orWhere('loc_codigo_unico', $search);

                if ($tipo) {
                    $q->orWhere('loc_tipo', $tipo);
                }

                if ($zona) {
                    $q->orWhere('loc_zona', $zona);
                }
            });
        }

        $locais = $query->orderByDesc('loc_id')->paginate(10)->appends(['search' => $search]);

        $view = $user->isAgente()
            ? 'agente.locais.index'
            : 'gestor.locais.index';

        return view($view, compact('locais', 'search'));
    }

    public function show(Local $local)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

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

        $view = $user->isAgente()
            ? 'agente.locais.show'
            : 'gestor.locais.show';

        return view($view, compact('local', 'qrCodeBase64', 'qrCodeMime'));
    }

    public function create()
    {
        $primario = Local::orderBy('loc_id')->first();
        $isPrimario = $primario === null;
        $cepPermitido = null;
        $cidadeEstado = null;
        if ($primario) {
            $cidadeEstado = ['cidade' => $primario->loc_cidade, 'estado' => $primario->loc_estado];
        }
        $storeRoute = Auth::user()->isGestor() ? 'gestor.locais.store' : 'agente.locais.store';
        $indexRoute = Auth::user()->isGestor() ? 'gestor.locais.index' : 'agente.locais.index';
        return view('agente.locais.create', compact('cepPermitido', 'cidadeEstado', 'isPrimario', 'storeRoute', 'indexRoute'));
    }

    public function store(LocalRequest $request)
    {
        $data = $request->validated();

        do {
            $codigo = mt_rand(10000000, 99999999);
        } while (Local::where('loc_codigo_unico', $codigo)->exists());

        $data['loc_codigo_unico'] = $codigo;

        $local = Local::create($data);

        $local->loc_numero = $local->loc_numero ?: 'N/A';

        LogHelper::registrar(
            'Cadastro de local',
            'Local',
            'create',
            'Local cadastrado: ' . $local->loc_endereco . ', ' . $local->loc_numero
        );

        $indexRoute = Auth::user()->isGestor() ? 'gestor.locais.index' : 'agente.locais.index';
        $redirectRoute = (Auth::user()->isGestor() && Local::count() === 1)
            ? 'gestor.dashboard'
            : $indexRoute;

        return redirect()
            ->route($redirectRoute)
            ->with('success', 'Local cadastrado com sucesso.');
    }

    public function edit(Local $local)
    {
        if ($local->isPrimary()) {
            return redirect()
                ->route(Auth::user()->isGestor() ? 'gestor.locais.index' : 'agente.locais.index')
                ->with('error', 'O local primário (primeiro cadastrado) não pode ser editado pela interface. Para alterações, entre em contato com o suporte técnico.');
        }
        $primario = Local::orderBy('loc_id')->first();
        $cepPermitido = null;
        $cidadeEstado = null;
        if ($primario) {
            $cidadeEstado = ['cidade' => $primario->loc_cidade, 'estado' => $primario->loc_estado];
        }
        return view('agente.locais.edit', compact('local', 'cepPermitido', 'cidadeEstado'));
    }

    public function update(LocalRequest $request, Local $local)
    {
        if ($local->isPrimary()) {
            return redirect()
                ->route(Auth::user()->isGestor() ? 'gestor.locais.index' : 'agente.locais.index')
                ->with('error', 'O local primário não pode ser editado pela interface. Para alterações, entre em contato com o suporte técnico.');
        }
        $local->update($request->validated());

        $local->loc_numero = $local->loc_numero ?: 'N/A';

        LogHelper::registrar(
            'Atualização de local',
            'Local',
            'update',
            'Local atualizado: ' . $local->loc_endereco . ', nº ' . $local->loc_numero
        );

        return redirect()
            ->route('agente.locais.index')
            ->with('success', 'Local atualizado com sucesso.');
    }

    public function destroy(Local $local)
    {
        if ($local->isPrimary()) {
            return redirect()
                ->route(Auth::user()->isGestor() ? 'gestor.locais.index' : 'agente.locais.index')
                ->with('error', 'O local primário (primeiro cadastrado) não pode ser excluído pela interface. Para exclusão ou alterações, entre em contato com o suporte técnico.');
        }
        if ($local->visitas()->exists()) {
            return redirect()
                ->route('agente.locais.index')
                ->with('error', 'Erro: este local possui visitas cadastradas e não pode ser excluído.');
        }

        $local->loc_numero = $local->loc_numero ?: 'N/A';
        $descricao = $local->loc_endereco . ', ' . $local->loc_numero;

        $local->delete();

        LogHelper::registrar(
            'Exclusão de local',
            'Local',
            'delete',
            'Local excluído: ' . $descricao
        );

        return redirect()
            ->route('agente.locais.index')
            ->with('success', 'Local excluído com sucesso.');
    }
}