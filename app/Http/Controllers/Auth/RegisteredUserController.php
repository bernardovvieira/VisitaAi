<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Tela de registro.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Processa o registro de um novo usuário.
     */
    public function store(Request $request): RedirectResponse
    { 
        /* -----------------------------------------------------------------
         |  1. Validação
         |-----------------------------------------------------------------*/
        $request->validate([
            'nome'     => ['required','string','max:255'],
            'cpf'      => ['required','string','max:14','unique:users,use_cpf'],
            'email'    => ['required','string','email:rfc,dns','max:255','unique:users,use_email'],
            'password' => ['required','confirmed', Rules\Password::defaults()],
        ], [
            'nome.required'   => 'O nome é obrigatório',
            'cpf.required'   => 'O CPF é obrigatório',
            'cpf.unique'     => 'Este CPF já está cadastrado',
            'email.required' => 'O e‑mail é obrigatório',
            'email.email'    => 'Formato de e‑mail inválido',
            'email.unique'   => 'Este e‑mail já está cadastrado',
            'password.confirmed' => 'A confirmação de senha não confere',
            'password.required'  => 'Por favor, digite uma senha',
            'password.min'       => 'A senha deve ter no mínimo 8 caracteres',
        ]);

        /* -----------------------------------------------------------------
         |  2. Criação – usuário entra como NÃO aprovado
         |-----------------------------------------------------------------*/
        $user = User::create([
            'use_nome'         => $request->input('nome'),
            'use_cpf'          => $request->input('cpf'),
            'use_email'        => $request->input('email'),
            'use_senha'        => Hash::make($request->input('password')),
            'use_perfil'       => 'agente_endemias',   // padrão
            'use_aprovado'     => false,      // aguardando aprovação do gestor
            'use_data_criacao' => now(),
        ]);

        // event(new Registered($user));

        /* -----------------------------------------------------------------
         |  3. Redireciona para login com aviso
         |     (não faz login automático porque ainda não foi aprovado)
         |-----------------------------------------------------------------*/
        return redirect()
            ->route('login')
            ->with('status', 'Cadastro enviado! Aguarde a aprovação de um gestor.');
    }
}
