<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    /**
     * Só gestores aprovados podem criar/editar usuários.
     */
    public function authorize(): bool
    {
        // usuário logado deve passar pelo Gate::create
        return Gate::allows('create', User::class);
    }

    /**
     * Regras de validação.
     */
    public function rules(): array
    {
        // pega o parâmetro {user} da rota, se existir
        $user = $this->route('user');
        $id = $user ? $user->use_id : null;

        return [
            'use_cpf' => ['required', 'string', 'max:255', Rule::unique('users', 'use_cpf')->ignore($id)],
            'use_email' => ['required', 'email', 'max:255', Rule::unique('users', 'use_email')->ignore($id)],
            'use_senha' => [$id ? 'nullable' : 'required', 'confirmed', Password::defaults()],
            'use_perfil' => ['required', Rule::in(['gestor', 'agente_endemias', 'agente_saude'])],
            'use_aprovado' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'use_senha.letters' => __('A senha deve conter pelo menos uma letra.'),
            'use_senha.mixed' => __('A senha deve conter pelo menos uma letra maiúscula e uma minúscula.'),
            'use_senha.numbers' => __('A senha deve conter pelo menos um número.'),
            'use_senha.symbols' => __('A senha deve conter pelo menos um caractere especial (ex.: @, #, $, !).'),
            'password.letters' => __('A senha deve conter pelo menos uma letra.'),
            'password.mixed' => __('A senha deve conter pelo menos uma letra maiúscula e uma minúscula.'),
            'password.numbers' => __('A senha deve conter pelo menos um número.'),
            'password.symbols' => __('A senha deve conter pelo menos um caractere especial (ex.: @, #, $, !).'),
        ];
    }
}
