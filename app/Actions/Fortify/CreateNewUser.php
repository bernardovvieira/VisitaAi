<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    /**
     * Valida e cria um novo usuário.
     */
    public function create(array $input)
    {
        Validator::make($input, [
            'cpf' => ['required', 'string', 'max:14', 'unique:users,use_cpf'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,use_email'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ], [
            'cpf.required' => __('O CPF é obrigatório'),
            'cpf.unique' => __('Este CPF já está cadastrado'),
            'email.required' => __('O e-mail é obrigatório.'),
            'email.email' => __('Formato de e-mail inválido.'),
            'email.unique' => __('Este e-mail já está cadastrado'),
            'password.required' => __('A senha é obrigatória'),
            'password.confirmed' => __('A confirmação de senha não confere'),
            'password.letters' => __('A senha deve conter pelo menos uma letra.'),
            'password.mixed' => __('A senha deve conter pelo menos uma letra maiúscula e uma minúscula.'),
            'password.numbers' => __('A senha deve conter pelo menos um número.'),
            'password.symbols' => __('A senha deve conter pelo menos um caractere especial (ex.: @, #, $, !).'),
        ])->validate();

        return User::create([
            'use_cpf' => $input['cpf'],
            'use_email' => $input['email'],
            'use_senha' => Hash::make($input['password']),
            'use_perfil' => 'agente_endemias',
            'use_tema' => 'light',
            'use_data_criacao' => now(),
        ]);
    }
}
