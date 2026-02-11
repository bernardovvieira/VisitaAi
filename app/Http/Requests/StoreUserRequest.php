<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

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
        $id   = $user ? $user->use_id : null;

        return [
            'use_cpf'       => ['required','string','max:255', Rule::unique('users','use_cpf')->ignore($id)],
            'use_email'     => ['required','email','max:255', Rule::unique('users','use_email')->ignore($id)],
            'use_senha'     => [$id ? 'nullable' : 'required','confirmed','min:8'],
            'use_perfil'    => ['required', Rule::in(['gestor','agente_endemias','agente_saude'])],
            'use_aprovado'  => ['required','boolean'],
        ];
    }
}
