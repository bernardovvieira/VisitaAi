<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\Contracts\ConfirmPasswordViewResponse;
use Laravel\Fortify\Contracts\FailedPasswordConfirmationResponse;
use Laravel\Fortify\Contracts\PasswordConfirmedResponse;
use Laravel\Fortify\Http\Controllers\ConfirmablePasswordController as FortifyConfirmablePasswordController;

/**
 * Substitui o controller do Fortify para confirmar senha com use_senha (evita query por coluna 'login').
 * Usado quando a rota que dispara é a do Fortify (ex.: route cache no demo).
 */
class FortifyConfirmPasswordControllerOverride extends FortifyConfirmablePasswordController
{
    public function store(Request $request)
    {
        $user     = $request->user();
        $password = $request->input('password');
        $confirmed = $password !== null && Hash::check($password, $user->use_senha);

        if ($confirmed) {
            $request->session()->put('auth.password_confirmed_at', Date::now()->unix());
        }

        return $confirmed
            ? app(PasswordConfirmedResponse::class)
            : app(FailedPasswordConfirmationResponse::class);
    }
}
