<?php

namespace App\Http\Responses;

use Illuminate\Http\RedirectResponse;
use Laravel\Fortify\Contracts\TwoFactorDisabledResponse as TwoFactorDisabledResponseContract;

class TwoFactorDisabledResponse implements TwoFactorDisabledResponseContract
{
    public function toResponse($request): RedirectResponse
    {
        return redirect()->route('profile.edit')
            ->with('success', 'Autenticação em dois fatores (2FA) desativada. Você não precisará mais informar o código do aplicativo ao entrar.');
    }
}
