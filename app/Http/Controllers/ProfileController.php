<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        $user->use_nome = $request->validated()['name'];
        $user->use_email = $request->validated()['email'];
        $user->use_tema = $request->validated()['tema'];

        $user->save();

        LogHelper::registrar(
            'Atualização de perfil',
            'Usuário',
            'update',
            'Usuário atualizou nome para: '.$user->use_nome.', email: '.$user->use_email
        );

        return Redirect::route('profile.edit')->with('success', __('Perfil atualizado com sucesso!'));
    }

    /**
     * Atualiza apenas a preferência de tema (chamado pelo toggle no front).
     */
    public function updateTema(Request $request): JsonResponse
    {
        $request->validate(['tema' => ['required', 'string', 'in:light,dark']]);

        $user = $request->user();
        $user->use_tema = $request->input('tema');
        $user->save();

        return response()->json(['ok' => true]);
    }

    /**
     * Página para ativar 2FA (requer senha confirmada via middleware).
     */
    public function twoFactor(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if (method_exists($user, 'hasEnabledTwoFactorAuthentication') && $user->hasEnabledTwoFactorAuthentication()) {
            return Redirect::route('profile.edit')->with('status', __('A autenticação em dois fatores já está ativa.'));
        }

        return view('profile.two-factor');
    }

    /**
     * Página para configurar o app autenticador: exibe QR code e formulário para confirmar com o código.
     * Só acessível após clicar em "Ativar 2FA" (quando two_factor_secret já existe mas ainda não confirmado).
     */
    public function twoFactorConfirm(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if (empty($user->two_factor_secret)) {
            return Redirect::route('profile.two-factor')
                ->with('error', __('Ative a autenticação em dois fatores primeiro.'));
        }

        if (! is_null($user->two_factor_confirmed_at ?? null)) {
            return Redirect::route('profile.edit')
                ->with('success', __('Autenticação em dois fatores já está ativa.'));
        }

        $qrCodeSvg = $user->twoFactorQrCodeSvg();
        $secretKey = decrypt($user->two_factor_secret);
        // Formato legível: grupos de 4 caracteres (ex: XXXX XXXX XXXX XXXX)
        $secretKeyFormatted = trim(chunk_split($secretKey, 4, ' '));

        return view('profile.two-factor-confirm', compact('qrCodeSvg', 'secretKey', 'secretKeyFormatted'));
    }
}
