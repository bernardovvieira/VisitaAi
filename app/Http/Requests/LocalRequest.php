<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\BuildsSocioeconomicoRules;
use App\Models\Local;
use App\Models\LocalDocumento;
use App\Models\Morador;
use App\Models\MoradorDocumento;
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

        if (isset($all['loc_documentos_posse']) && is_array($all['loc_documentos_posse'])) {
            foreach ($all['loc_documentos_posse'] as $i => $f) {
                if ($f instanceof SymfonyUploadedFile
                    && ! $f->isValid()
                    && (int) $f->getError() === UPLOAD_ERR_NO_FILE) {
                    unset($all['loc_documentos_posse'][$i]);
                    $touched = true;
                }
            }
            if ($all['loc_documentos_posse'] === []) {
                unset($all['loc_documentos_posse']);
                $touched = true;
            }
        }

        if (isset($all['ocupantes']) && is_array($all['ocupantes'])) {
            foreach ($all['ocupantes'] as $i => $row) {
                if (! is_array($row) || ! isset($row['mor_documentos_pessoal']) || ! is_array($row['mor_documentos_pessoal'])) {
                    continue;
                }
                foreach ($row['mor_documentos_pessoal'] as $j => $f) {
                    if ($f instanceof SymfonyUploadedFile
                        && ! $f->isValid()
                        && (int) $f->getError() === UPLOAD_ERR_NO_FILE) {
                        unset($all['ocupantes'][$i]['mor_documentos_pessoal'][$j]);
                        $touched = true;
                    }
                }
                if (($all['ocupantes'][$i]['mor_documentos_pessoal'] ?? []) === []) {
                    unset($all['ocupantes'][$i]['mor_documentos_pessoal']);
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
            'ocupantes.*.mor_documentos_pessoal.*' => __('documento pessoal do ocupante'),
            'loc_documentos_posse.*' => __('contrato, matrícula ou escritura do imóvel'),
        ];
    }

    public function messages(): array
    {
        return [
            'ocupantes.*.mor_documentos_pessoal.*.max' => __('O documento pessoal não pode ter mais de 10 MB.'),
            'ocupantes.*.mor_documentos_pessoal.*.mimes' => __('O documento pessoal tem de ser PDF, JPG, PNG, WEBP ou HEIC/HEIF.'),
            'loc_documentos_posse.*.max' => __('O documento do imóvel não pode ter mais de 10 MB.'),
            'loc_documentos_posse.*.mimes' => __('O documento do imóvel tem de ser PDF, JPG, PNG, WEBP ou HEIC/HEIF.'),
        ];
    }

    public function rules()
    {
        $localId = $this->editingLocalId();
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
            'loc_endereco' => [
                'required',
                'string',
                'max:255',
            ],
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
            'loc_documentos_posse' => ['nullable', 'array', 'max:20'],
            'loc_documentos_posse.*' => [
                'nullable',
                'file',
                'max:10240',
                'mimes:pdf,jpeg,jpg,png,webp,heic,heif',
            ],
            'remover_documentos_posse' => ['nullable', 'array', 'max:50'],
            'remover_documentos_posse.*' => ['integer', 'exists:local_documentos,id'],

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
            'ocupantes.*.mor_documentos_pessoal' => ['nullable', 'array', 'max:15'],
            'ocupantes.*.mor_documentos_pessoal.*' => [
                'nullable',
                'file',
                'max:10240',
                'mimes:pdf,jpeg,jpg,png,webp,heic,heif',
            ],
            'ocupantes.*.remover_documentos_pessoal' => ['nullable', 'array', 'max:30'],
            'ocupantes.*.remover_documentos_pessoal.*' => ['integer', 'exists:morador_documentos,id'],
            'ocupantes.*.mor_tempo_uniao_conjuge' => ['nullable', 'string', 'max:120'],
            'ocupantes.*.mor_ajuda_compra_imovel' => ['nullable', 'string', Rule::in(['sim', 'nao'])],
            'ocupantes.*.mor_renda_formal_informal' => ['nullable', 'string', Rule::in($rfiKeys)],
        ], $this->socioeconomicoFormRules());
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (! $validator->errors()->hasAny(['loc_endereco', 'loc_numero', 'loc_bairro', 'loc_cidade', 'loc_estado'])) {
                $excludeId = $this->editingLocalId();
                $conflictId = $this->findConflictingLocalIdByEnderecoTerritorial($excludeId);
                if ($conflictId !== null) {
                    $validator->errors()->add(
                        'loc_endereco',
                        __('Já existe outro imóvel com o mesmo endereço (logradouro, número, bairro, cidade e UF). Registro em conflito: ID :id.', ['id' => $conflictId])
                    );
                }
            }
        });

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

            $locDocs = $this->file('loc_documentos_posse');
            if (is_array($locDocs)) {
                foreach (array_keys($locDocs) as $i) {
                    $rewrite("loc_documentos_posse.$i");
                }
            }

            $ocupantes = $this->input('ocupantes', []);
            if (! is_array($ocupantes)) {
                return;
            }
            foreach (array_keys($ocupantes) as $i) {
                $filesRow = $this->file("ocupantes.$i", []);
                if (! is_array($filesRow)) {
                    continue;
                }
                $nested = $filesRow['mor_documentos_pessoal'] ?? null;
                if (! is_array($nested)) {
                    continue;
                }
                foreach (array_keys($nested) as $j) {
                    $rewrite("ocupantes.$i.mor_documentos_pessoal.$j");
                }
            }
        });

        $validator->after(function ($validator) {
            $local = $this->route('local');
            if (! $local instanceof Local) {
                return;
            }
            $ids = $this->input('remover_documentos_posse', []);
            if (! is_array($ids)) {
                return;
            }
            foreach ($ids as $k => $raw) {
                $id = is_numeric($raw) ? (int) $raw : 0;
                if ($id <= 0) {
                    continue;
                }
                $ok = LocalDocumento::query()
                    ->whereKey($id)
                    ->where('fk_local_id', $local->loc_id)
                    ->exists();
                if (! $ok) {
                    $validator->errors()->add(
                        "remover_documentos_posse.$k",
                        __('Documento do imóvel inválido para remoção.')
                    );
                }
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
                if (! is_array($row)) {
                    continue;
                }
                $mid = isset($row['mor_id']) ? (int) $row['mor_id'] : 0;
                $removeIds = $row['remover_documentos_pessoal'] ?? [];
                if ($mid <= 0 || ! is_array($removeIds)) {
                    continue;
                }
                $okMor = Morador::query()
                    ->where('mor_id', $mid)
                    ->where('fk_local_id', $local->loc_id)
                    ->exists();
                if (! $okMor) {
                    continue;
                }
                foreach ($removeIds as $k => $raw) {
                    $id = is_numeric($raw) ? (int) $raw : 0;
                    if ($id <= 0) {
                        continue;
                    }
                    $ok = MoradorDocumento::query()
                        ->whereKey($id)
                        ->where('fk_morador_id', $mid)
                        ->exists();
                    if (! $ok) {
                        $validator->errors()->add(
                            "ocupantes.$i.remover_documentos_pessoal.$k",
                            __('Documento pessoal inválido para remoção.')
                        );
                    }
                }
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

    /**
     * ID do local em edição (PATCH), ou null no cadastro.
     */
    private function editingLocalId(): ?int
    {
        $param = $this->route()?->parameter('local');
        if ($param instanceof Local) {
            return (int) $param->loc_id;
        }
        if (is_numeric($param)) {
            return (int) $param;
        }
        $path = (string) $this->path();
        if (preg_match('#/locais/(\d+)(?:/|$|\?)#', $path, $m)) {
            return (int) $m[1];
        }

        return null;
    }

    /**
     * Mesmo critério de número que o LocalController ao persistir (inteiro ou null).
     */
    private function normalizeLocNumeroInput(mixed $value): ?int
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

    /**
     * Outro local com o mesmo endereço territorial (exclui o registro em edição).
     */
    private function findConflictingLocalIdByEnderecoTerritorial(?int $excludeLocId): ?int
    {
        $end = mb_strtolower(trim((string) $this->input('loc_endereco', '')), 'UTF-8');
        $bairro = mb_strtolower(trim((string) $this->input('loc_bairro', '')), 'UTF-8');
        $cidadeNorm = $this->normalizeStr(trim((string) $this->input('loc_cidade', '')));
        $estado = strtoupper(trim((string) $this->input('loc_estado', '')));
        $numero = $this->normalizeLocNumeroInput($this->input('loc_numero'));

        if ($end === '' || $bairro === '' || $cidadeNorm === '' || $estado === '') {
            return null;
        }

        $q = Local::query()
            ->whereRaw('LOWER(TRIM(loc_endereco)) = ?', [$end])
            ->whereRaw('LOWER(TRIM(loc_bairro)) = ?', [$bairro])
            ->whereRaw('UPPER(TRIM(loc_estado)) = ?', [$estado]);

        if ($excludeLocId) {
            $q->where('loc_id', '!=', $excludeLocId);
        }

        foreach ($q->get(['loc_id', 'loc_cidade', 'loc_numero']) as $row) {
            if ($this->normalizeStr((string) ($row->loc_cidade ?? '')) !== $cidadeNorm) {
                continue;
            }
            $dbNum = $row->loc_numero !== null ? (int) $row->loc_numero : null;
            if ($dbNum !== $numero) {
                continue;
            }

            return (int) $row->loc_id;
        }

        return null;
    }

    private function normalizeStr(string $s): string
    {
        $s = mb_strtolower(trim($s), 'UTF-8');
        $s = preg_replace('/\s+/u', ' ', $s);
        $map = ['á' => 'a', 'à' => 'a', 'ã' => 'a', 'â' => 'a', 'é' => 'e', 'ê' => 'e', 'í' => 'i', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ú' => 'u', 'ç' => 'c'];

        return strtr($s, $map);
    }
}
