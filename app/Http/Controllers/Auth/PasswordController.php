<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the authenticated user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        // 1) Validação
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ], [
            'current_password.required' => 'Informe a senha atual',
            'current_password.current_password' => 'A senha atual não confere',
            'password.required' => 'A nova senha é obrigatória',
            'password.confirmed' => 'A confirmação de senha não confere',
            'password.min' => 'A nova senha deve ter no mínimo 8 caracteres',
            'password.letters' => 'A senha deve conter pelo menos uma letra.',
            'password.mixed' => 'A senha deve conter pelo menos uma letra maiúscula e uma minúscula.',
            'password.numbers' => 'A senha deve conter pelo menos um número.',
            'password.symbols' => 'A senha deve conter pelo menos um caractere especial (ex.: @, #, $, !).',
        ]);

        // 2) Atualiza o campo use_senha na tabela users
        $request->user()->update([
            'use_senha' => Hash::make($validated['password']),
        ]);

        // 3) Mensagem de sucesso
        return back()->with('status', __('Senha atualizada com sucesso!'));
    }
}
