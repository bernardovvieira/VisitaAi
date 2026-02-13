<?php

namespace App\Providers;

use App\Http\Middleware\CheckApproved;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Http\Controllers\ConfirmedTwoFactorAuthenticationController;
use Laravel\Fortify\Http\Controllers\RecoveryCodeController;
use Laravel\Fortify\Http\Controllers\TwoFactorAuthenticationController;
use Laravel\Fortify\Http\Controllers\TwoFactorQrCodeController;
use Laravel\Fortify\Http\Controllers\TwoFactorSecretKeyController;

/**
 * Rotas de ativação de 2FA sem pedir senha.
 *
 * Registradas antes do Fortify (em config/app.php) para ter prioridade.
 * Usam os mesmos controllers do Fortify, só sem o middleware password.confirm.
 * Desativar 2FA continua indo pela tela de confirmar senha (link no perfil).
 */
class TwoFactorRoutesServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Route::middleware(['web', 'auth', CheckApproved::class])->group(function () {
            Route::prefix('user')->group(function () {
                Route::post('two-factor-authentication', [TwoFactorAuthenticationController::class, 'store'])->name('two-factor.enable');
                Route::post('confirmed-two-factor-authentication', [ConfirmedTwoFactorAuthenticationController::class, 'store'])->name('two-factor.confirm');
                Route::get('two-factor-qr-code', [TwoFactorQrCodeController::class, 'show'])->name('two-factor.qr-code');
                Route::get('two-factor-secret-key', [TwoFactorSecretKeyController::class, 'show'])->name('two-factor.secret-key');
                Route::get('two-factor-recovery-codes', [RecoveryCodeController::class, 'index'])->name('two-factor.recovery-codes');
                Route::post('two-factor-recovery-codes', [RecoveryCodeController::class, 'store'])->name('two-factor.regenerate-recovery-codes');
            });
        });
    }
}
