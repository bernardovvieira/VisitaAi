<?php

namespace App\Providers;

use App\Http\Controllers\Auth\ConfirmPasswordOverride;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Laravel\Fortify\Http\Controllers\ConfirmablePasswordController as FortifyController;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // POST /user/confirm-password: confirma com use_senha, sem query por coluna 'login'
        $this->app->bind(FortifyController::class, ConfirmPasswordOverride::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
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
