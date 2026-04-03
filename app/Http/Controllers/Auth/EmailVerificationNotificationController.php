<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new e-mail verification notification.
     */
    public function store(Request $request): RedirectResponse
    {
        // Se já está verificado, redireciona para o dashboard
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(
                route('dashboard', [], false)    // URL relativa
            );
        }

        // Dispara o e-mail de verificação
        $request->user()->sendEmailVerificationNotification();

        // Flash de sucesso
        return back()->with('status', 'verification-link-sent');
    }
}
