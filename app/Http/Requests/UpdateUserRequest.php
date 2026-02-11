<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'use_senha'  => ['nullable', 'string', 'min:8'], // senha agora é opcional
        ];
    }
}
