<?php

namespace App\Http\Responses;

use Illuminate\Http\RedirectResponse;
use Laravel\Fortify\Contracts\TwoFactorConfirmedResponse as TwoFactorConfirmedResponseContract;

class TwoFactorConfirmedResponse implements TwoFactorConfirmedResponseContract
{
    public function toResponse($request): RedirectResponse
    {
        return redirect()->route('profile.edit')
            ->with('success', 'Autenticação em dois fatores (2FA) ativada com sucesso. Na próxima sessão você precisará informar o código do aplicativo autenticador.');
    }
}
