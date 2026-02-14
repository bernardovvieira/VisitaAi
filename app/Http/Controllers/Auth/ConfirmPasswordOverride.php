<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\Contracts\ConfirmPasswordViewResponse;
use Laravel\Fortify\Contracts\FailedPasswordConfirmationResponse;
use Laravel\Fortify\Contracts\PasswordConfirmedResponse;
use Laravel\Fortify\Http\Controllers\ConfirmablePasswordController as BaseController;

/**
 * Substitui o controller do Fortify para confirmar senha.
 * Usa Hash::check(use_senha) em vez de guard->validate() — evita query por coluna 'login'.
 */
class ConfirmPasswordOverride extends BaseController
{
    public function store(Request $request)
    {
        $user     = $request->user();
        $password = $request->input('password');
        $ok       = $password && Hash::check($password, $user->use_senha);

        if ($ok) {
            $request->session()->put('auth.password_confirmed_at', Date::now()->unix());
        }

        return $ok
            ? app(PasswordConfirmedResponse::class)
            : app(FailedPasswordConfirmationResponse::class);
    }
}
