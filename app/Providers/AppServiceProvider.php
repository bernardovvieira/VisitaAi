<?php

namespace App\Providers;

use App\Actions\Fortify\ConfirmPassword as ConfirmPasswordAction;
use App\Http\Controllers\User\ConfirmPasswordController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Laravel\Fortify\Actions\ConfirmPassword as FortifyConfirmPassword;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Confirma senha sem coluna 'login' (antes do Fortify; evita query quebrada em 2FA)
        $this->app->singleton(FortifyConfirmPassword::class, ConfirmPasswordAction::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Rota própria para confirmar senha (2FA); registrada após todos os providers (sobrescreve a do Fortify)
        $this->app->booted(function () {
            Route::middleware(['web', 'auth'])->group(function () {
                Route::post('user/confirm-password', [ConfirmPasswordController::class, 'store'])
                    ->name('password.confirm.store');
            });
        });

        // Evita travamento longo se MySQL estiver inacessível (ex.: atrás do Coolify)
        ini_set('default_socket_timeout', (string) 10);

        // Regra global de senha: mínimo 8 caracteres, letras (maiúscula e minúscula), números e caractere especial
        Password::defaults(function () {
            return Password::min(8)
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols();
        });
    }
}
