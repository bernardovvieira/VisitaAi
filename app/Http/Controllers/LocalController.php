<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use App\Http\Requests\LocalRequest;
use App\Models\Local;
use App\Services\Municipio\ResumoOcupantesMunicipioService;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\SvgWriter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LocalController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($request->has('guardada')) {
            session()->flash('warning', 'Local cadastrado no dispositivo com sucesso. Sincronize quando se reconectar.');

            return redirect()->to($request->url());
        }

        $search = strtolower(trim($request->input('search')));

        $query = Local::query();

        if ($search !== '') {
            // Busca inteligente: prefixos de residencial, comercial, terreno, urbano, rural (sem regras fixas).
            $tipo = null;
            $zona = null;
            $minPrefix = 2;
            if (strlen($search) >= $minPrefix) {
                if (str_starts_with('residencial', $search)) {
                    $tipo = 'R';
                } elseif (str_starts_with('comercial', $search)) {
                    $tipo = 'C';
                } elseif (str_starts_with('terreno', $search)) {
                    $tipo = 'T';
                }
                if (str_starts_with('urbano', $search) || str_starts_with('urbana', $search)
                    || str_starts_with($search, 'urbano') || str_starts_with($search, 'urbana')) {
                    $zona = 'U';
                } elseif (str_starts_with('rural', $search) || str_starts_with($search, 'rural')) {
                    $zona = 'R';
                }
            }
            if ($search === 'r' || $search === 'c' || $search === 't') {
                $tipo = strtoupper($search);
            }
            if ($search === 'u') {
                $zona = 'U';
            } elseif ($search === 'r' && $zona === null) {
                $zona = 'R';
            }

            $query->where(function ($q) use ($search, $tipo, $zona) {
                $q->whereRaw('LOWER(loc_endereco) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(loc_bairro) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw("LOWER(COALESCE(loc_responsavel_nome, '')) LIKE ?", ["%{$search}%"])
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

        $coordenadasDuplicadas = Local::whereNotNull('loc_latitude')
            ->whereNotNull('loc_longitude')
            ->selectRaw('loc_latitude, loc_longitude, COUNT(*) as qtd')
            ->groupBy('loc_latitude', 'loc_longitude')
            ->having('qtd', '>', 1)
            ->exists();

        $view = $user->isAgente()
            ? 'agente.locais.index'
            : 'gestor.locais.index';

        return view($view, compact('locais', 'search', 'coordenadasDuplicadas'));
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

        $view = $user->isAgente()
            ? 'agente.locais.show'
            : 'gestor.locais.show';

        $local->load('moradores');
        $moradorResumo = app(ResumoOcupantesMunicipioService::class)->resumoParaLocal($local);

        return view($view, compact('local', 'qrCodeBase64', 'qrCodeMime', 'moradorResumo'));
    }

    public function create()
    {
        $primario = Local::orderBy('loc_id')->first();
        $isPrimario = $primario === null;
        $cepPermitido = null;
        $cidadeEstado = null;
        $cepsCadastrados = [];
        if ($primario) {
            $cidadeEstado = ['cidade' => $primario->loc_cidade, 'estado' => $primario->loc_estado];
            $cepsCadastrados = $this->buildCepsCadastrados($primario->loc_cidade, $primario->loc_estado);
        }
        $storeRoute = Auth::user()->isGestor() ? 'gestor.locais.store' : 'agente.locais.store';
        $indexRoute = Auth::user()->isGestor() ? 'gestor.locais.index' : 'agente.locais.index';

        return view('agente.locais.create', compact('cepPermitido', 'cidadeEstado', 'cepsCadastrados', 'isPrimario', 'storeRoute', 'indexRoute'));
    }

    public function store(LocalRequest $request)
    {
        $data = $request->validated();

        do {
            $codigo = mt_rand(10000000, 99999999);
        } while (Local::where('loc_codigo_unico', $codigo)->exists());

        $data['loc_codigo_unico'] = $codigo;
        $data['loc_numero'] = $this->normalizeLocNumero($data['loc_numero'] ?? null);

        $local = Local::create($data);

        LogHelper::registrar(
            'Cadastro de local',
            'Local',
            'create',
            'Local cadastrado: '.$local->loc_endereco.', '.($local->loc_numero ?? 'S/N')
        );

        $indexRoute = Auth::user()->isGestor() ? 'gestor.locais.index' : 'agente.locais.index';
        $redirectRoute = (Auth::user()->isGestor() && Local::count() === 1)
            ? 'gestor.dashboard'
            : $indexRoute;

        return redirect()
            ->route($redirectRoute)
            ->with('success', 'Local cadastrado com sucesso.')
            ->with('created_local_id', $local->loc_id);
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
        $cepsCadastrados = [];
        if ($primario) {
            $cidadeEstado = ['cidade' => $primario->loc_cidade, 'estado' => $primario->loc_estado];
            $cepsCadastrados = $this->buildCepsCadastrados($primario->loc_cidade, $primario->loc_estado);
        }

        return view('agente.locais.edit', compact('local', 'cepPermitido', 'cidadeEstado', 'cepsCadastrados'));
    }

    /**
     * Retorna lista de endereços por CEP já cadastrados no sistema (mesmo município), no formato ViaCEP.
     */
    private function buildCepsCadastrados(?string $cidade, ?string $estado): array
    {
        if (! $cidade || ! $estado) {
            return [];
        }
        $locais = Local::where('loc_cidade', $cidade)
            ->where('loc_estado', $estado)
            ->get();
        $out = [];
        foreach ($locais as $loc) {
            $cepNorm = preg_replace('/\D/', '', $loc->loc_cep ?? '');
            if (strlen($cepNorm) >= 8) {
                $cepNorm = substr($cepNorm, 0, 8);
                $lat = $loc->loc_latitude !== null && $loc->loc_latitude !== '' ? (float) $loc->loc_latitude : null;
                $lng = $loc->loc_longitude !== null && $loc->loc_longitude !== '' ? (float) $loc->loc_longitude : null;
                $out[] = [
                    'cep' => $cepNorm,
                    'logradouro' => $loc->loc_endereco ?? '',
                    'bairro' => $loc->loc_bairro ?? '',
                    'localidade' => $loc->loc_cidade ?? '',
                    'uf' => $loc->loc_estado ?? '',
                    'latitude' => $lat,
                    'longitude' => $lng,
                ];
            }
        }

        return $out;
    }

    public function update(LocalRequest $request, Local $local)
    {
        if ($local->isPrimary()) {
            return redirect()
                ->route(Auth::user()->isGestor() ? 'gestor.locais.index' : 'agente.locais.index')
                ->with('error', 'O local primário não pode ser editado pela interface. Para alterações, entre em contato com o suporte técnico.');
        }
        $data = $request->validated();
        $data['loc_numero'] = $this->normalizeLocNumero($data['loc_numero'] ?? null);
        $local->update($data);

        LogHelper::registrar(
            'Atualização de local',
            'Local',
            'update',
            'Local atualizado: '.$local->loc_endereco.', nº '.($local->loc_numero ?? 'S/N')
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
        if ($local->moradores()->exists()) {
            return redirect()
                ->route(Auth::user()->isGestor() ? 'gestor.locais.index' : 'agente.locais.index')
                ->with('error', 'Erro: este local possui ocupantes registrados no Visita Aí e não pode ser excluído.');
        }

        if ($local->visitas()->exists()) {
            return redirect()
                ->route(Auth::user()->isGestor() ? 'gestor.locais.index' : 'agente.locais.index')
                ->with('error', 'Erro: este local possui visitas cadastradas e não pode ser excluído.');
        }

        $descricao = $local->loc_endereco.', '.($local->loc_numero ?? 'S/N');

        $local->delete();

        LogHelper::registrar(
            'Exclusão de local',
            'Local',
            'delete',
            'Local excluído: '.$descricao
        );

        return redirect()
            ->route(Auth::user()->isGestor() ? 'gestor.locais.index' : 'agente.locais.index')
            ->with('success', 'Local excluído com sucesso.');
    }

    /**
     * Recebe locais salvos offline e persiste no servidor.
     * Retorna JSON: { "criados": int, "erros": [ { "index", "message" } ], "ids": [ loc_id, ... ] }
     */
    public function syncStore(Request $request)
    {
        $user = Auth::user();
        if (! $user->isAgenteEndemias() && ! $user->isGestor()) {
            return response()->json(['message' => 'Acesso negado.'], 403);
        }
        $this->authorize('create', Local::class);

        $payload = $request->validate([
            'locais' => ['required', 'array', 'max:50'],
            'locais.*' => ['array'],
        ]);

        $locais = $payload['locais'];
        $ids = array_fill(0, count($locais), null);
        $erros = [];
        $rules = $this->localSyncRules();

        foreach ($locais as $index => $item) {
            $validator = Validator::make($item, $rules);
            if ($validator->fails()) {
                $erros[] = ['index' => $index, 'message' => implode(' ', $validator->errors()->all())];

                continue;
            }
            $data = $validator->validated();
            if (Local::where('loc_endereco', $data['loc_endereco'])->exists()) {
                $erros[] = ['index' => $index, 'message' => 'Já existe um local com este endereço.'];

                continue;
            }
            do {
                $codigo = mt_rand(10000000, 99999999);
            } while (Local::where('loc_codigo_unico', $codigo)->exists());
            $data['loc_codigo_unico'] = $codigo;
            $data['loc_numero'] = $this->normalizeLocNumero($data['loc_numero'] ?? null);
            try {
                $local = Local::create($data);
                $ids[$index] = $local->loc_id;
                LogHelper::registrar('Sincronização offline', 'Local', 'create', 'Local sincronizado: '.$local->loc_endereco);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Local syncStore: '.$e->getMessage());
                $erros[] = ['index' => $index, 'message' => $e->getMessage()];
            }
        }

        return response()->json([
            'criados' => count(array_filter($ids)),
            'erros' => $erros,
            'ids' => $ids,
        ]);
    }

    /** Converte valor de número do local para integer ou null (coluna é integer). */
    private function normalizeLocNumero(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }
        $s = is_string($value) ? trim($value) : (string) $value;
        if ($s === '' || strtoupper($s) === 'N/A' || strtoupper($s) === 'S/N') {
            return null;
        }

        return is_numeric($s) ? (int) $s : null;
    }

    /** Regras de validação para sync de locais (sem ViaCEP). */
    private function localSyncRules(): array
    {
        return [
            'loc_cep' => ['required', 'string', 'max:20'],
            'loc_tipo' => ['required', 'string', 'in:R,C,T'],
            'loc_zona' => ['required', 'string', 'max:10'],
            'loc_endereco' => ['required', 'string', 'max:255'],
            'loc_numero' => ['nullable', 'string', 'max:20'],
            'loc_bairro' => ['required', 'string', 'max:100'],
            'loc_cidade' => ['required', 'string', 'max:100'],
            'loc_estado' => ['required', 'string', 'max:2'],
            'loc_pais' => ['required', 'string', 'max:100'],
            'loc_latitude' => ['required', 'string', 'max:20'],
            'loc_longitude' => ['required', 'string', 'max:20'],
            'loc_codigo' => ['required', 'string', 'max:50'],
            'loc_quarteirao' => ['nullable', 'string', 'max:50'],
            'loc_complemento' => ['nullable', 'string', 'max:255'],
            'loc_categoria' => ['nullable', 'string', 'max:100'],
            'loc_sequencia' => ['nullable', 'integer'],
            'loc_lado' => ['nullable', 'integer'],
            'loc_responsavel_nome' => ['nullable', 'string', 'max:255'],
        ];
    }
}
