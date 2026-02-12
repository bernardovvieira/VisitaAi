<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    /**
     * Display the password reset view.
     */
    public function create(Request $request): View
    {
        return view('auth.reset-password', ['request' => $request]);
    }

    /**
     * Handle an incoming new password request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // 1) Validação dos campos
        $request->validate([
            'token'                 => ['required'],
            'email'                 => ['required','email'],
            'password'              => ['required','confirmed', Rules\Password::defaults()],
        ], [
            'token.required'            => 'O token de recuperação é obrigatório',
            'email.required'            => 'O e‑mail é obrigatório',
            'email.email'               => 'Formato de e‑mail inválido',
            'password.required'         => 'A senha é obrigatória',
            'password.confirmed'        => 'A confirmação de senha não confere',
            'password.min'              => 'A senha deve ter no mínimo 8 caracteres',
            'password.letters'           => 'A senha deve conter pelo menos uma letra.',
            'password.mixed'             => 'A senha deve conter pelo menos uma letra maiúscula e uma minúscula.',
            'password.numbers'          => 'A senha deve conter pelo menos um número.',
            'password.symbols'          => 'A senha deve conter pelo menos um caractere especial (ex.: @, #, $, !).',
        ]);

        // 2) Tenta redefinir usando a coluna use_email
        $status = Password::reset(
            [
                'use_email'             => $request->input('email'),
                'password'              => $request->input('password'),
                'password_confirmation' => $request->input('password_confirmation'),
                'token'                 => $request->input('token'),
            ],
            function (User $user) use ($request) {
                // atualiza a senha no campo use_senha
                $user->forceFill([
                    'use_senha'      => Hash::make($request->input('password')),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        // 3) Resposta de sucesso ou falha
        if ($status === Password::PASSWORD_RESET) {
            return redirect()
                ->route('login')
                ->with('status', 'Senha redefinida com sucesso! Faça login com sua nova senha.');
        }

        throw ValidationException::withMessages([
            'email' => ['Não foi possível redefinir a senha. Por favor, tente novamente.'],
        ]);
    }
}
