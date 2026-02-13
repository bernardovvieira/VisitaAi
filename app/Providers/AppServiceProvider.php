<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
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
