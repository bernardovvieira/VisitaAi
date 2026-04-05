<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\ResetsUserPasswords;

class ResetUserPassword implements ResetsUserPasswords
{
    use PasswordValidationRules;

    /**
     * Validate and reset the user's forgotten password.
     *
     * @param  array<string, string>  $input
     */
    public function reset(User $user, array $input): void
    {
        Validator::make($input, [
            'password' => $this->passwordRules(),
        ], [
            'password.letters' => __('A senha deve conter pelo menos uma letra.'),
            'password.mixed' => __('A senha deve conter pelo menos uma letra maiúscula e uma minúscula.'),
            'password.numbers' => __('A senha deve conter pelo menos um número.'),
            'password.symbols' => __('A senha deve conter pelo menos um caractere especial (ex.: @, #, $, !).'),
        ])->validate();

        $user->forceFill([
            'use_senha' => Hash::make($input['password']),
        ])->save();
    }
}
