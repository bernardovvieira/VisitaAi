<?php

namespace App\Providers;

use App\Http\Controllers\Auth\FortifyConfirmPasswordControllerOverride;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Laravel\Fortify\Http\Controllers\ConfirmablePasswordController as FortifyConfirmablePasswordController;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Rota do Fortify (POST /user/confirm-password) usa nosso controller: confirma com use_senha, sem query por 'login'
        $this->app->bind(FortifyConfirmablePasswordController::class, FortifyConfirmPasswordControllerOverride::class);
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
