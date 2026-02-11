<?php

namespace App\Http\Responses;

use Illuminate\Http\RedirectResponse;
use Laravel\Fortify\Contracts\TwoFactorEnabledResponse as TwoFactorEnabledResponseContract;

class TwoFactorEnabledResponse implements TwoFactorEnabledResponseContract
{
    /**
     * Redireciona para o perfil com mensagem de sucesso após ativar 2FA.
     */
    public function toResponse($request): RedirectResponse
    {
        return redirect()->route('profile.two-factor-confirm');
    }
}
