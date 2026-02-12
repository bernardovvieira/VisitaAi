<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Normaliza o e-mail para lowercase antes de validar e salvar
        $this->merge([
            'email' => strtolower($this->email),
        ]);
    }

    /**
     * Regras de validação do formulário.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'tema' => ['required', 'string', 'in:light,dark'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email:rfc,dns',
                'max:255',
                Rule::unique('users', 'use_email')->ignore($this->user()->use_id, 'use_id'),
            ],
        ];
    }

    /**
     * Nome dos atributos para mensagens amigáveis.
     */
    public function attributes(): array
    {
        return [
            'name' => 'nome',
            'tema' => 'preferência de tema',
            'email' => 'e-mail',
        ];
    }

    /**
     * Mensagens de erro personalizadas.
     */
    public function messages(): array
    {
        return [
            'email.unique' => 'Este endereço de e-mail já está em uso por outro usuário.',
            'name.required' => 'O nome é obrigatório.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'Informe um e-mail válido.',
        ];
    }
}
