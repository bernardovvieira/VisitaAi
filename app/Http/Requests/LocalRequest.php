<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\BuildsSocioeconomicoRules;
use App\Models\Local;
use App\Models\Morador;
use App\Support\UploadErrorMessage;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\File\UploadedFile as SymfonyUploadedFile;

class LocalRequest extends FormRequest
{
    use BuildsSocioeconomicoRules;

    public function authorize()
    {
        $user = $this->user();
        $local = $this->route('local');

        if ($local instanceof Local) {
            return $user->can('update', $local);
        }

        return $user->can('create', Local::class);
    }

    /**
     * Remove nested file slots with no selected file (UPLOAD_ERR_NO_FILE).
     * Laravel's validator treats invalid UploadedFile as "uploaded" failure before nullable applies.
     */
    protected function prepareForValidation(): void
    {
        $all = $this->files->all();
        $touched = false;

        if (isset($all['loc_documento_posse']) && $all['loc_documento_posse'] instanceof SymfonyUploadedFile) {
            $f = $all['loc_documento_posse'];
            if (! $f->isValid() && (int) $f->getError() === UPLOAD_ERR_NO_FILE) {
                unset($all['loc_documento_posse']);
                $touched = true;
            }
        }

        if (isset($all['ocupantes']) && is_array($all['ocupantes'])) {
            foreach ($all['ocupantes'] as $i => $row) {
                if (! is_array($row) || ! isset($row['mor_documento_pessoal'])) {
                    continue;
                }
                $f = $row['mor_documento_pessoal'];
                if ($f instanceof SymfonyUploadedFile
                    && ! $f->isValid()
                    && (int) $f->getError() === UPLOAD_ERR_NO_FILE) {
                    unset($all['ocupantes'][$i]['mor_documento_pessoal']);
                    $touched = true;
                }
            }
        }

        if ($touched) {
            $this->files->replace($all);
            $this->convertedFiles = null;
        }
    }

    public function attributes(): array
    {
        return [
            'ocupantes.*.mor_documento_pessoal' => __('documento pessoal do ocupante'),
            'loc_documento_posse' => __('contrato, matrícula ou escritura do imóvel'),
        ];
    }

    public function messages(): array
    {
        return [
            'ocupantes.*.mor_documento_pessoal.max' => __('O documento pessoal não pode ter mais de 10 MB.'),
            'ocupantes.*.mor_documento_pessoal.mimes' => __('O documento pessoal tem de ser PDF, JPG, PNG, WEBP ou HEIC/HEIF.'),
            'loc_documento_posse.max' => __('O documento do imóvel não pode ter mais de 10 MB.'),
            'loc_documento_posse.mimes' => __('O documento do imóvel tem de ser PDF, JPG, PNG, WEBP ou HEIC/HEIF.'),
        ];
    }

    public function rules()
    {
        $localId = $this->route('local')?->loc_id;
        $escKeys = array_keys(config('visitaai_municipio.escolaridade_opcoes', []));
        $rendaKeys = array_keys(config('visitaai_municipio.renda_faixa_opcoes', []));
        $corKeys = array_keys(config('visitaai_municipio.cor_raca_opcoes', []));
        $trabKeys = array_keys(config('visitaai_municipio.situacao_trabalho_opcoes', []));
        $sexoKeys = array_keys(config('visitaai_socioeconomico.sexo_opcoes', []));
        $ecKeys = array_keys(config('visitaai_socioeconomico.estado_civil_opcoes', []));
        $parKeys = array_keys(config('visitaai_socioeconomico.parentesco_opcoes', []));
        $rfiKeys = array_keys(config('visitaai_socioeconomico.renda_formal_informal_opcoes', []));

        return array_merge([
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
                        $fail(__('Não foi possível validar o CEP. Tente novamente.'));

                        return;
                    }
                    $data = $response->json();
                    if (isset($data['erro']) && $data['erro']) {
                        $fail(__('CEP inválido.'));

                        return;
                    }

                    $localidade = trim($data['localidade'] ?? '');
                    $uf = strtoupper(trim($data['uf'] ?? ''));
                    $cidadePrimario = $this->normalizeStr($primario->loc_cidade);
                    $estadoPrimario = strtoupper(trim($primario->loc_estado ?? ''));

                    if ($this->normalizeStr($localidade) !== $cidadePrimario || $uf !== $estadoPrimario) {
                        $fail(__('O CEP informado não pertence ao município do local primário (:city/:state).', [
                            'city' => $primario->loc_cidade,
                            'state' => $primario->loc_estado,
                        ]));
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
            'loc_documento_posse' => [
                'nullable',
                'file',
                'max:10240',
                'mimes:pdf,jpeg,jpg,png,webp,heic,heif',
            ],
            'remover_documento_posse' => ['nullable', 'boolean'],

            'ocupantes' => ['nullable', 'array', 'max:30'],
            'ocupantes.*.mor_id' => ['nullable', 'integer'],
            'ocupantes.*.mor_nome' => ['nullable', 'string', 'max:255'],
            'ocupantes.*.mor_data_nascimento' => ['nullable', 'date', 'before_or_equal:today'],
            'ocupantes.*.mor_escolaridade' => ['nullable', 'string', Rule::in($escKeys)],
            'ocupantes.*.mor_renda_faixa' => ['nullable', 'string', Rule::in($rendaKeys)],
            'ocupantes.*.mor_cor_raca' => ['nullable', 'string', Rule::in($corKeys)],
            'ocupantes.*.mor_situacao_trabalho' => ['nullable', 'string', Rule::in($trabKeys)],
            'ocupantes.*.mor_observacao' => ['nullable', 'string', 'max:2000'],
            'ocupantes.*.mor_sexo' => ['nullable', 'string', Rule::in($sexoKeys)],
            'ocupantes.*.mor_estado_civil' => ['nullable', 'string', Rule::in($ecKeys)],
            'ocupantes.*.mor_naturalidade' => ['nullable', 'string', 'max:150'],
            'ocupantes.*.mor_profissao' => ['nullable', 'string', 'max:150'],
            'ocupantes.*.mor_parentesco' => ['nullable', 'string', Rule::in($parKeys)],
            'ocupantes.*.mor_referencia_familiar' => ['nullable', 'boolean'],
            'ocupantes.*.mor_telefone' => ['nullable', 'string', 'max:40'],
            'ocupantes.*.mor_rg_numero' => ['nullable', 'string', 'max:45'],
            'ocupantes.*.mor_rg_orgao' => ['nullable', 'string', 'max:60'],
            'ocupantes.*.mor_rg_expedicao' => ['nullable', 'date'],
            'ocupantes.*.mor_cpf' => ['nullable', 'string', 'max:20'],
            'ocupantes.*.mor_documento_pessoal' => [
                'nullable',
                'file',
                'max:10240',
                'mimes:pdf,jpeg,jpg,png,webp,heic,heif',
            ],
            'ocupantes.*.mor_tempo_uniao_conjuge' => ['nullable', 'string', 'max:120'],
            'ocupantes.*.mor_ajuda_compra_imovel' => ['nullable', 'string', Rule::in(['sim', 'nao'])],
            'ocupantes.*.mor_renda_formal_informal' => ['nullable', 'string', Rule::in($rfiKeys)],
        ], $this->socioeconomicoFormRules());
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $rewrite = function (string $key) use ($validator): void {
                if (! $validator->errors()->has($key)) {
                    return;
                }
                $f = $this->file($key);
                if (! $f instanceof SymfonyUploadedFile || $f->isValid()) {
                    return;
                }
                $code = (int) $f->getError();
                if ($code === UPLOAD_ERR_NO_FILE) {
                    $validator->errors()->forget($key);

                    return;
                }
                $msg = UploadErrorMessage::forPhpUploadError($code);
                if ($msg === '') {
                    return;
                }
                $validator->errors()->forget($key);
                $validator->errors()->add($key, $msg);
            };

            $rewrite('loc_documento_posse');

            $ocupantes = $this->input('ocupantes', []);
            if (! is_array($ocupantes)) {
                return;
            }
            foreach (array_keys($ocupantes) as $i) {
                $rewrite("ocupantes.$i.mor_documento_pessoal");
            }
        });

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

            $refs = 0;
            foreach ($ocupantes as $row) {
                if (! is_array($row)) {
                    continue;
                }
                $ref = $row['mor_referencia_familiar'] ?? false;
                if ($ref === true || $ref === 1 || $ref === '1' || $ref === 'true') {
                    $refs++;
                }
            }
            if ($refs > 1) {
                $validator->errors()->add('ocupantes', __('Marque no máximo um morador como referência familiar (titular).'));

                return;
            }

            if (! $local instanceof Local) {
                return;
            }

            $rowsByMorId = [];
            foreach ($ocupantes as $row) {
                if (! is_array($row)) {
                    continue;
                }
                $mid = isset($row['mor_id']) ? (int) $row['mor_id'] : 0;
                if ($mid > 0) {
                    $rowsByMorId[$mid] = $row;
                }
            }

            $titularIdsExistentes = Morador::query()
                ->where('fk_local_id', $local->loc_id)
                ->where('mor_referencia_familiar', true)
                ->pluck('mor_id')
                ->map(fn ($id) => (int) $id)
                ->all();

            $titularesMantidos = 0;
            foreach ($titularIdsExistentes as $titularId) {
                if (! array_key_exists($titularId, $rowsByMorId)) {
                    // Se o titular existente não veio no payload, ele continua titular.
                    $titularesMantidos++;

                    continue;
                }
                $rowTitular = $rowsByMorId[$titularId];
                $refAtualizado = $rowTitular['mor_referencia_familiar'] ?? false;
                if ($refAtualizado === true || $refAtualizado === 1 || $refAtualizado === '1' || $refAtualizado === 'true') {
                    $titularesMantidos++;
                }
            }

            $titularesNovos = 0;
            foreach ($ocupantes as $row) {
                if (! is_array($row)) {
                    continue;
                }
                $mid = isset($row['mor_id']) ? (int) $row['mor_id'] : 0;
                if ($mid > 0 && in_array($mid, $titularIdsExistentes, true)) {
                    continue;
                }
                $ref = $row['mor_referencia_familiar'] ?? false;
                if ($ref === true || $ref === 1 || $ref === '1' || $ref === 'true') {
                    $titularesNovos++;
                }
            }

            if (($titularesMantidos + $titularesNovos) > 1) {
                $validator->errors()->add('ocupantes', __('Este imóvel já possui um titular cadastrado. Mantenha apenas um ocupante como referência familiar.'));
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
