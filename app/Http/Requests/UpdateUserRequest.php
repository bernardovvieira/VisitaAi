<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('user'));
    }

    public function rules(): array
    {
        $userId = $this->route('user')->use_id; // pega o ID correto do usuário

        return [
            'use_nome'   => ['required', 'string', 'max:255'],
            'use_email'  => ['required', 'email', 'max:255', 'unique:users,use_email,' . $userId . ',use_id'],
            'use_perfil' => ['required', 'in:gestor,agente_endemias,agente_saude'],
            'use_senha'  => ['nullable', 'confirmed', Password::defaults()], // opcional; se informada, segue regra forte
        ];
    }

    public function messages(): array
    {
        return [
            'use_senha.letters' => 'A senha deve conter pelo menos uma letra.',
            'use_senha.mixed'   => 'A senha deve conter pelo menos uma letra maiúscula e uma minúscula.',
            'use_senha.numbers' => 'A senha deve conter pelo menos um número.',
            'use_senha.symbols' => 'A senha deve conter pelo menos um caractere especial (ex.: @, #, $, !).',
            'password.letters'  => 'A senha deve conter pelo menos uma letra.',
            'password.mixed'    => 'A senha deve conter pelo menos uma letra maiúscula e uma minúscula.',
            'password.numbers'  => 'A senha deve conter pelo menos um número.',
            'password.symbols'  => 'A senha deve conter pelo menos um caractere especial (ex.: @, #, $, !).',
        ];
    }
}
