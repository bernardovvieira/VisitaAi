<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\Contracts\ConfirmPasswordViewResponse;
use Laravel\Fortify\Contracts\FailedPasswordConfirmationResponse;
use Laravel\Fortify\Contracts\PasswordConfirmedResponse;
use Laravel\Fortify\Http\Controllers\ConfirmablePasswordController as BaseFortifyConfirmablePasswordController;

/**
 * Substitui o controller do Fortify para confirmar senha usando use_senha (evita coluna 'login').
 */
class ConfirmPasswordControllerOverride extends BaseFortifyConfirmablePasswordController
{
    public function show(Request $request)
    {
        return app(ConfirmPasswordViewResponse::class);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $password = $request->input('password');

        $confirmed = $password !== null && Hash::check($password, $user->use_senha);

        if ($confirmed) {
            $request->session()->put('auth.password_confirmed_at', Date::now()->unix());
        }

        return $confirmed
            ? app(PasswordConfirmedResponse::class)->toResponse($request)
            : app(FailedPasswordConfirmationResponse::class)->toResponse($request);
    }
}
