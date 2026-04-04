<?php

namespace App\Http\Requests\Municipio;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
        ]);
    }

    public function rules(): array
    {
        $escolaridade = array_keys(config('visitaai_municipio.escolaridade_opcoes', []));
        $renda = array_keys(config('visitaai_municipio.renda_faixa_opcoes', []));
        $corRaca = array_keys(config('visitaai_municipio.cor_raca_opcoes', []));
        $trabalho = array_keys(config('visitaai_municipio.situacao_trabalho_opcoes', []));

        return [
            'mor_nome' => ['nullable', 'string', 'max:255'],
            'mor_data_nascimento' => ['nullable', 'date', 'before_or_equal:today'],
            'mor_escolaridade' => ['nullable', 'string', Rule::in($escolaridade)],
            'mor_renda_faixa' => ['nullable', 'string', Rule::in($renda)],
            'mor_cor_raca' => ['nullable', 'string', Rule::in($corRaca)],
            'mor_situacao_trabalho' => ['nullable', 'string', Rule::in($trabalho)],
            'mor_observacao' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
