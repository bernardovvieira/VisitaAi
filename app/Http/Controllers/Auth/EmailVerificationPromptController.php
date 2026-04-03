<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the e-mail verification prompt.
     */
    public function __invoke(Request $request): RedirectResponse|View
    {
        // Se o usuário já confirmou o e-mail, redireciona ao dashboard
        // (gerando URL relativa para evitar erro de assinatura do helper)
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', [], false));
        }

        // Caso contrário, mostra a view de verificação
        return view('auth.verify-email');
    }
}
