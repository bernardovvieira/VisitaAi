<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Laravel\Fortify\Actions\DisableTwoFactorAuthentication;

class ConfirmablePasswordController extends Controller
{
    /**
     * Show the confirm-password view.
     */
    public function show(): View
    {
        return view('auth.confirm-password');
    }

    /**
     * Confirm the user's password.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Autentica usando use_email e a senha fornecida
        if (! Auth::guard('web')->validate([
            'use_email' => $request->user()->use_email,
            'password'  => $request->input('password'),
        ])) {
            throw ValidationException::withMessages([
                'password' => __('auth.password'), // “The provided password is incorrect.”
            ]);
        }

        // Marca o instante em que a senha foi confirmada
        $request->session()->put('auth.password_confirmed_at', time());

        // Se veio da tela de desativar 2FA (return_action=disable_2fa), desativa e vai para o perfil
        if ($request->input('return_action') === 'disable_2fa') {
            app(DisableTwoFactorAuthentication::class)($request->user());
            return redirect()->route('profile.edit')
                ->with('success', 'Autenticação em dois fatores (2FA) desativada. Você não precisará mais informar o código do aplicativo ao entrar.');
        }

        return redirect()->intended(route('dashboard', [], false));
    }
}
