<?php

namespace App\Http\Requests;

use App\Models\Local;
use App\Models\Morador;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;

class LocalRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('create', Local::class) || $this->user()->can('update', Local::class);
    }

    public function rules()
    {
        $localId = $this->route('local')?->loc_id;
        $escKeys = array_keys(config('visitaai_municipio.escolaridade_opcoes', []));
        $rendaKeys = array_keys(config('visitaai_municipio.renda_faixa_opcoes', []));
        $corKeys = array_keys(config('visitaai_municipio.cor_raca_opcoes', []));
        $trabKeys = array_keys(config('visitaai_municipio.situacao_trabalho_opcoes', []));

        return [
            'loc_cep' => [
                'required',
                'string',
                'size:9',
                function ($attribute, $value, $fail) use ($localId) {
                    $cepNormalizado = preg_replace('/\D/', '', $value);
                    $primario = Local::orderBy('loc_id')->first();

                    if (! $primario) {
                        return;
                    }
                    if ($localId && (int) $localId === (int) $primario->loc_id) {
                        return;
                    }

                    $response = Http::timeout(5)->get("https://viacep.com.br/ws/{$cepNormalizado}/json/");
                    if (! $response->successful()) {
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
                        $fail('O CEP informado não pertence ao município do local primário ('.$primario->loc_cidade.'/'.$primario->loc_estado.').');
                    }
                },
            ],
            'loc_tipo' => ['required', Rule::in(['R', 'C', 'T'])], // R-Residencial, C-Comercial, T-Terreno Baldio (conformidade PNCD)
            'loc_quarteirao' => ['nullable', 'string', 'max:50'],
            'loc_endereco' => ['required', 'string', 'max:255', "unique:locais,loc_endereco,{$localId},loc_id"],
            'loc_numero' => ['nullable', 'string', 'max:20'],
            'loc_bairro' => ['required', 'string', 'max:100'],
            'loc_cidade' => ['required', 'string', 'max:100'],
            'loc_estado' => ['required', 'string', 'max:2'],
            'loc_pais' => ['required', 'string', 'max:100'],
            'loc_latitude' => ['required', 'string', 'max:20'],
            'loc_longitude' => ['required', 'string', 'max:20'],
            'loc_zona' => ['required', 'string'],
            'loc_complemento' => ['nullable', 'string', 'max:255'],
            'loc_categoria' => ['nullable', 'string', 'max:100'],
            'loc_sequencia' => ['nullable', 'integer'],
            'loc_lado' => ['nullable', 'integer'],
            'loc_codigo' => ['required', 'string'],
            'loc_codigo_unico' => ['nullable', 'string', 'max:255', "unique:locais,loc_codigo_unico,{$localId},loc_id"],
            'loc_responsavel_nome' => ['nullable', 'string', 'max:255'],

            'ocupantes' => ['nullable', 'array', 'max:30'],
            'ocupantes.*.mor_id' => ['nullable', 'integer'],
            'ocupantes.*.mor_nome' => ['nullable', 'string', 'max:255'],
            'ocupantes.*.mor_data_nascimento' => ['nullable', 'date', 'before_or_equal:today'],
            'ocupantes.*.mor_escolaridade' => ['nullable', 'string', Rule::in($escKeys)],
            'ocupantes.*.mor_renda_faixa' => ['nullable', 'string', Rule::in($rendaKeys)],
            'ocupantes.*.mor_cor_raca' => ['nullable', 'string', Rule::in($corKeys)],
            'ocupantes.*.mor_situacao_trabalho' => ['nullable', 'string', Rule::in($trabKeys)],
            'ocupantes.*.mor_observacao' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $local = $this->route('local');
            if (! $local instanceof Local) {
                return;
            }
            $ocupantes = $this->input('ocupantes', []);
            if (! is_array($ocupantes)) {
                return;
            }
            foreach ($ocupantes as $i => $row) {
                $mid = isset($row['mor_id']) ? (int) $row['mor_id'] : 0;
                if ($mid <= 0) {
                    continue;
                }
                $ok = Morador::query()
                    ->where('mor_id', $mid)
                    ->where('fk_local_id', $local->loc_id)
                    ->exists();
                if (! $ok) {
                    $validator->errors()->add(
                        "ocupantes.$i.mor_id",
                        __('Ocupante inválido para este imóvel.')
                    );
                }
            }
        });
    }

    private function normalizeStr(string $s): string
    {
        $s = mb_strtolower(trim($s), 'UTF-8');
        $s = preg_replace('/\s+/u', ' ', $s);
        $map = ['á' => 'a', 'à' => 'a', 'ã' => 'a', 'â' => 'a', 'é' => 'e', 'ê' => 'e', 'í' => 'i', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ú' => 'u', 'ç' => 'c'];

        return strtr($s, $map);
    }
}
