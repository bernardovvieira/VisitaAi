<?php

namespace App\Actions\Fortify;

use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\Actions\ConfirmPassword as FortifyConfirmPassword;

/**
 * Confirma senha usando apenas use_senha (evita guard->validate que usa coluna 'login').
 */
class ConfirmPassword extends FortifyConfirmPassword
{
    public function __invoke(StatefulGuard $guard, $user, ?string $password = null): bool
    {
        return $password !== null && Hash::check($password, $user->use_senha);
    }
}
