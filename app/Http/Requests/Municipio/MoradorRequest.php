<?php

namespace App\Http\Requests\Municipio;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Local;
use App\Models\Morador;

class MoradorRequest extends FormRequest
{
    public function authorize(): bool
    {
        $u = $this->user();

        return $u !== null && ($u->isGestor() || $u->isAgenteEndemias());
    }

    protected function prepareForValidation(): void
    {
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
            'mor_documento_pessoal' => ['nullable', 'file', 'max:10240', 'mimes:pdf,jpeg,jpg,png,webp,heic,heif'],
            'remover_documento_pessoal' => ['nullable', 'boolean'],
            'mor_tempo_uniao_conjuge' => ['nullable', 'string', 'max:120'],
            'mor_ajuda_compra_imovel' => ['nullable', 'string', 'max:255'],
            'mor_renda_formal_informal' => ['nullable', 'string', Rule::in($rfi)],
            'mor_observacao' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function withValidator($validator): void
    {
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
