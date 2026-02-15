<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Local;
use Illuminate\Support\Facades\Http;

class LocalRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('create', Local::class) || $this->user()->can('update', Local::class);
    }

    public function rules()
    {
        $localId = $this->route('local')?->loc_id;

        return [
            'loc_cep'            => [
                'required',
                'string',
                'size:9',
                function ($attribute, $value, $fail) use ($localId) {
                    $cepNormalizado = preg_replace('/\D/', '', $value);
                    $primario = Local::orderBy('loc_id')->first();

                    if (!$primario) {
                        return;
                    }
                    if ($localId && (int) $localId === (int) $primario->loc_id) {
                        return;
                    }

                    $response = Http::timeout(5)->get("https://viacep.com.br/ws/{$cepNormalizado}/json/");
                    if (!$response->successful()) {
                        $fail('Não foi possível validar o CEP. Tente novamente.');
                        return;
                    }
                    $data = $response->json();
                    if (isset($data['erro']) && $data['erro']) {
                        $fail('CEP inválido.');
                        return;
                    }

                    $localidade = trim($data['localidade'] ?? '');
                    $uf = strtoupper(trim($data['uf'] ?? ''));
                    $cidadePrimario = $this->normalizeStr($primario->loc_cidade);
                    $estadoPrimario = strtoupper(trim($primario->loc_estado ?? ''));

                    if ($this->normalizeStr($localidade) !== $cidadePrimario || $uf !== $estadoPrimario) {
                        $fail('O CEP informado não pertence ao município do local primário (' . $primario->loc_cidade . '/' . $primario->loc_estado . ').');
                    }
                },
            ],
            'loc_tipo'           => ['required', 'string', 'max:50'],
            'loc_quarteirao'     => ['nullable', 'string', 'max:50'],
            'loc_endereco'       => ['required', 'string', 'max:255', "unique:locais,loc_endereco,{$localId},loc_id"],
            'loc_numero'         => ['nullable', 'string', 'max:20'],
            'loc_bairro'         => ['required', 'string', 'max:100'],
            'loc_cidade'         => ['required', 'string', 'max:100'],
            'loc_estado'         => ['required', 'string', 'max:2'],
            'loc_pais'           => ['required', 'string', 'max:100'],
            'loc_latitude'       => ['required', 'string', 'max:20'],
            'loc_longitude'      => ['required', 'string', 'max:20'],
            'loc_zona'           => ['required', 'string'],
            'loc_complemento'    => ['nullable', 'string', 'max:255'],
            'loc_categoria'      => ['nullable', 'string', 'max:100'],
            'loc_sequencia'      => ['nullable', 'integer'],
            'loc_lado'           => ['nullable', 'integer'],
            'loc_codigo'         => ['required', 'string'],
            'loc_codigo_unico'   => ['nullable', 'string', 'max:255', "unique:locais,loc_codigo_unico,{$localId},loc_id"],
        ];
    }

    private function normalizeStr(string $s): string
    {
        $s = mb_strtolower(trim($s), 'UTF-8');
        $s = preg_replace('/\s+/u', ' ', $s);
        $map = ['á' => 'a', 'à' => 'a', 'ã' => 'a', 'â' => 'a', 'é' => 'e', 'ê' => 'e', 'í' => 'i', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ú' => 'u', 'ç' => 'c'];
        return strtr($s, $map);
    }
}