<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Illuminate\Validation\ValidationException;

class CreateNewUser implements CreatesNewUsers
{
    /**
     * Valida e cria um novo usuário.
     */
    public function create(array $input)
    {
        Validator::make($input, [
            'cpf'      => ['required','string','max:14','unique:users,use_cpf'],
            'email'    => ['required','string','email','max:255','unique:users,use_email'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ], [
            'cpf.required'      => 'O CPF é obrigatório.',
            'cpf.unique'        => 'Este CPF já está cadastrado.',
            'email.required'    => 'O e‑mail é obrigatório.',
            'email.email'       => 'Formato de e‑mail inválido.',
            'email.unique'      => 'Este e‑mail já está cadastrado.',
            'password.required' => 'A senha é obrigatória.',
            'password.confirmed'=> 'A confirmação de senha não confere.',
        ])->validate();

        return User::create([
            'use_cpf'           => $input['cpf'],
            'use_email'         => $input['email'],
            'use_senha'         => Hash::make($input['password']),
            'use_perfil'        => 'agente_endemias',
            'use_data_criacao'  => now(),
        ]);
    }
}
