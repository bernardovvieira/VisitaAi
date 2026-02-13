<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\Contracts\FailedPasswordConfirmationResponse;
use Laravel\Fortify\Contracts\PasswordConfirmedResponse;

/**
 * Confirma senha (2FA etc.) usando apenas use_senha, sem guard->validate (evita coluna 'login').
 */
class ConfirmPasswordController extends Controller
{
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
