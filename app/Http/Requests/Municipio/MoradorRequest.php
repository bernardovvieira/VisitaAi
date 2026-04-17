<?php

namespace App\Http\Requests\Municipio;

use App\Models\Local;
use App\Models\Morador;
use App\Models\MoradorDocumento;
use App\Support\UploadErrorMessage;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\File\UploadedFile as SymfonyUploadedFile;

class MoradorRequest extends FormRequest
{
    public function authorize(): bool
    {
        $u = $this->user();

        return $u !== null && ($u->isAgenteEndemias() || $u->isAgenteSaude());
    }

    protected function prepareForValidation(): void
    {
        $all = $this->files->all();
        $touched = false;

        if (isset($all['mor_documentos_pessoal']) && is_array($all['mor_documentos_pessoal'])) {
            foreach ($all['mor_documentos_pessoal'] as $i => $f) {
                if ($f instanceof SymfonyUploadedFile
                    && ! $f->isValid()
                    && (int) $f->getError() === UPLOAD_ERR_NO_FILE) {
                    unset($all['mor_documentos_pessoal'][$i]);
                    $touched = true;
                }
            }
            if (($all['mor_documentos_pessoal'] ?? []) === []) {
                unset($all['mor_documentos_pessoal']);
                $touched = true;
            }
        }

        if ($touched) {
            $this->files->replace($all);
            $this->convertedFiles = null;
        }

        $this->merge([
            'mor_escolaridade' => $this->mor_escolaridade === '' ? null : $this->mor_escolaridade,
            'mor_renda_faixa' => $this->mor_renda_faixa === '' ? null : $this->mor_renda_faixa,
            'mor_cor_raca' => $this->mor_cor_raca === '' ? null : $this->mor_cor_raca,
            'mor_situacao_trabalho' => $this->mor_situacao_trabalho === '' ? null : $this->mor_situacao_trabalho,
            'mor_sexo' => $this->mor_sexo === '' ? null : $this->mor_sexo,
            'mor_estado_civil' => $this->mor_estado_civil === '' ? null : $this->mor_estado_civil,
            'mor_parentesco' => $this->mor_parentesco === '' ? null : $this->mor_parentesco,
            'mor_rg_expedicao' => $this->mor_rg_expedicao === '' ? null : $this->mor_rg_expedicao,
            'mor_renda_formal_informal' => $this->mor_renda_formal_informal === '' ? null : $this->mor_renda_formal_informal,
        ]);
    }

    public function rules(): array
    {
        $escolaridade = array_keys(config('visitaai_municipio.escolaridade_opcoes', []));
        $renda = array_keys(config('visitaai_municipio.renda_faixa_opcoes', []));
        $corRaca = array_keys(config('visitaai_municipio.cor_raca_opcoes', []));
        $trabalho = array_keys(config('visitaai_municipio.situacao_trabalho_opcoes', []));
        $sexo = array_keys(config('visitaai_socioeconomico.sexo_opcoes', []));
        $ec = array_keys(config('visitaai_socioeconomico.estado_civil_opcoes', []));
        $par = array_keys(config('visitaai_socioeconomico.parentesco_opcoes', []));
        $rfi = array_keys(config('visitaai_socioeconomico.renda_formal_informal_opcoes', []));

        return [
            'mor_nome' => ['nullable', 'string', 'max:255'],
            'mor_data_nascimento' => ['nullable', 'date', 'before_or_equal:today'],
            'mor_escolaridade' => ['nullable', 'string', Rule::in($escolaridade)],
            'mor_renda_faixa' => ['nullable', 'string', Rule::in($renda)],
            'mor_cor_raca' => ['nullable', 'string', Rule::in($corRaca)],
            'mor_situacao_trabalho' => ['nullable', 'string', Rule::in($trabalho)],
            'mor_sexo' => ['nullable', 'string', Rule::in($sexo)],
            'mor_estado_civil' => ['nullable', 'string', Rule::in($ec)],
            'mor_naturalidade' => ['nullable', 'string', 'max:150'],
            'mor_profissao' => ['nullable', 'string', 'max:150'],
            'mor_parentesco' => ['nullable', 'string', Rule::in($par)],
            'mor_referencia_familiar' => ['nullable', 'boolean'],
            'mor_telefone' => ['nullable', 'string', 'max:40'],
            'mor_rg_numero' => ['nullable', 'string', 'max:45'],
            'mor_rg_orgao' => ['nullable', 'string', 'max:60'],
            'mor_rg_expedicao' => ['nullable', 'date'],
            'mor_cpf' => ['nullable', 'string', 'max:20'],
            'mor_documentos_pessoal' => ['nullable', 'array', 'max:15'],
            'mor_documentos_pessoal.*' => ['nullable', 'file', 'max:10240', 'mimes:pdf,jpeg,jpg,png,webp,heic,heif'],
            'remover_documentos_pessoal' => ['nullable', 'array', 'max:30'],
            'remover_documentos_pessoal.*' => ['integer', 'exists:morador_documentos,id'],
            'mor_tempo_uniao_conjuge' => ['nullable', 'string', 'max:120'],
            'mor_ajuda_compra_imovel' => ['nullable', 'string', 'max:255'],
            'mor_renda_formal_informal' => ['nullable', 'string', Rule::in($rfi)],
            'mor_observacao' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'mor_documentos_pessoal.*' => __('documento pessoal'),
        ];
    }

    public function messages(): array
    {
        return [
            'mor_documentos_pessoal.*.max' => __('O documento pessoal não pode ter mais de 10 MB.'),
            'mor_documentos_pessoal.*.mimes' => __('O documento pessoal tem de ser PDF, JPG, PNG, WEBP ou HEIC/HEIF.'),
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $files = $this->file('mor_documentos_pessoal');
            if (! is_array($files)) {
                return;
            }
            foreach (array_keys($files) as $i) {
                $key = "mor_documentos_pessoal.$i";
                if (! $validator->errors()->has($key)) {
                    continue;
                }
                $f = $this->file("mor_documentos_pessoal.$i");
                if (! $f instanceof SymfonyUploadedFile || $f->isValid()) {
                    continue;
                }
                $code = (int) $f->getError();
                if ($code === UPLOAD_ERR_NO_FILE) {
                    $validator->errors()->forget($key);

                    continue;
                }
                $msg = UploadErrorMessage::forPhpUploadError($code);
                if ($msg === '') {
                    continue;
                }
                $validator->errors()->forget($key);
                $validator->errors()->add($key, $msg);
            }
        });

        $validator->after(function ($validator) {
            $moradorAtual = $this->route('morador');
            if (! $moradorAtual instanceof Morador) {
                return;
            }
            $ids = $this->input('remover_documentos_pessoal', []);
            if (! is_array($ids)) {
                return;
            }
            foreach ($ids as $k => $raw) {
                $id = is_numeric($raw) ? (int) $raw : 0;
                if ($id <= 0) {
                    continue;
                }
                $ok = MoradorDocumento::query()
                    ->whereKey($id)
                    ->where('fk_morador_id', $moradorAtual->mor_id)
                    ->exists();
                if (! $ok) {
                    $validator->errors()->add(
                        "remover_documentos_pessoal.$k",
                        __('Documento pessoal inválido para remoção.')
                    );
                }
            }
        });

        $validator->after(function ($validator) {
            $refRaw = $this->input('mor_referencia_familiar', false);
            $isTitular = in_array($refRaw, [true, 1, '1', 'true', 'on', 'yes'], true);
            if (! $isTitular) {
                return;
            }

            $local = $this->route('local');
            if (! $local instanceof Local) {
                return;
            }

            $moradorAtual = $this->route('morador');
            $moradorAtualId = $moradorAtual instanceof Morador ? (int) $moradorAtual->mor_id : null;

            $q = Morador::query()
                ->where('fk_local_id', $local->loc_id)
                ->where('mor_referencia_familiar', true);

            if ($moradorAtualId) {
                $q->where('mor_id', '!=', $moradorAtualId);
            }

            if ($q->exists()) {
                $validator->errors()->add(
                    'mor_referencia_familiar',
                    __('Este imóvel já possui um titular cadastrado. Remova a marcação do titular atual antes de definir outro.')
                );
            }
        });
    }
}
