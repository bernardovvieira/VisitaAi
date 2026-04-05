<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\UpdatesUserPasswords;

class UpdateUserPassword implements UpdatesUserPasswords
{
    use PasswordValidationRules;

    /**
     * Validate and update the user's password.
     *
     * @param  array<string, string>  $input
     */
    public function update(User $user, array $input): void
    {
        Validator::make($input, [
            'current_password' => ['required', 'string', 'current_password:web'],
            'password' => $this->passwordRules(),
        ], [
            'current_password.current_password' => __('A senha atual não confere com a senha cadastrada.'),
            'password.letters' => __('A senha deve conter pelo menos uma letra.'),
            'password.mixed' => __('A senha deve conter pelo menos uma letra maiúscula e uma minúscula.'),
            'password.numbers' => __('A senha deve conter pelo menos um número.'),
            'password.symbols' => __('A senha deve conter pelo menos um caractere especial (ex.: @, #, $, !).'),
        ])->validateWithBag('updatePassword');

        $user->forceFill([
            'use_senha' => Hash::make($input['password']),
        ])->save();
    }
}
