<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        // Se já está verificado, apenas redireciona
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(
                route('dashboard', [], false).'?verified=1'
            );
        }

        // Marca como verificado e dispara o evento
        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return redirect()->intended(
            route('dashboard', [], false).'?verified=1'
        );
    }
}
