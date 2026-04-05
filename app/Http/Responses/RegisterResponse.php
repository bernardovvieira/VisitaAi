<?php

namespace App\Http\Responses;

use Illuminate\Http\RedirectResponse;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;

class RegisterResponse implements RegisterResponseContract
{
    /**
     * Return the response after a user registers.
     */
    public function toResponse($request): RedirectResponse
    {
        // redireciona ao dashboard com um flash de sucesso
        return redirect()->intended('/dashboard')
            ->with('status', __('Cadastro realizado com sucesso!'));
    }
}
