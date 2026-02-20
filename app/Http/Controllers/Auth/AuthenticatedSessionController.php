<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Helpers\LogHelper;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Laravel\Fortify\Features;

class AuthenticatedSessionController extends Controller
{
    /**
     * Show the login page.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // Valida + autentica (LoginRequest já trata tentativas e mensagens)
        $request->authenticate();

        // Regenera a sessão para evitar fixation
        $request->session()->regenerate();

        $user = $request->user();

        // Se 2FA está ativo, redireciona para a tela do código em vez do dashboard
        if (Features::enabled(Features::twoFactorAuthentication()) &&
            method_exists($user, 'hasEnabledTwoFactorAuthentication') &&
            $user->hasEnabledTwoFactorAuthentication()) {
            $dashboard = $user->isGestor() ? route('gestor.dashboard') : ($user->isAgenteSaude() ? route('saude.dashboard') : route('agente.dashboard'));
            Auth::guard('web')->logout();
            $request->session()->put([
                'login.id' => $user->getKey(),
                'login.remember' => $request->boolean('remember'),
                'url.intended' => $dashboard,
            ]);
            return redirect()->route('two-factor.login');
        }

        // Registra login na auditoria
        LogHelper::registrar('login', 'sessão', 'login', 'Login realizado', $request);

        // Redireciona conforme o perfil
        if ($user->isGestor()) {
            $redirect = route('gestor.dashboard', [], false);
        } elseif ($user->isAgenteSaude()) {
            $redirect = route('saude.dashboard', [], false);
        } elseif ($user->isAgenteEndemias()) {
            $redirect = route('agente.dashboard', [], false);
        } else {
            abort(403);
        }

        return redirect()->intended($redirect);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
