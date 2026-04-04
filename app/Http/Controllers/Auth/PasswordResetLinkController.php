<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // 1) Validação do campo e-mail
        $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => __('O e-mail é obrigatório.'),
            'email.email' => __('Formato de e-mail inválido.'),
        ]);

        // 2) Tenta enviar o link usando a coluna use_email
        $status = Password::sendResetLink([
            'use_email' => $request->input('email'),
        ]);

        // 3) Mesma mensagem e mesmo canal (flash de status) para enviado, e-mail inexistente, etc.; evita enumeração
        $neutral = __('Se o e-mail estiver cadastrado, você receberá um link de recuperação de senha.');

        if ($status === Password::RESET_THROTTLED) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => __('Por favor, aguarde um momento antes de solicitar outro link.')]);
        }

        return back()
            ->withInput($request->only('email'))
            ->with('status', $neutral);
    }
}
