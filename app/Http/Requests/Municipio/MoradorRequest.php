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
        ]);
    }

    public function rules(): array
    {
        $escolaridade = array_keys(config('visitaai_municipio.escolaridade_opcoes', []));
        $renda = array_keys(config('visitaai_municipio.renda_faixa_opcoes', []));

        return [
            'mor_nome' => ['nullable', 'string', 'max:255'],
            'mor_data_nascimento' => ['nullable', 'date', 'before_or_equal:today'],
            'mor_escolaridade' => ['nullable', 'string', Rule::in($escolaridade)],
            'mor_renda_faixa' => ['nullable', 'string', Rule::in($renda)],
            'mor_observacao' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
