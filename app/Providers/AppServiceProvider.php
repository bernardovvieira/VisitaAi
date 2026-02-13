<?php

namespace App\Providers;

use App\Actions\Fortify\ConfirmPassword as ConfirmPasswordAction;
use App\Http\Controllers\Auth\ConfirmPasswordControllerOverride;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Laravel\Fortify\Actions\ConfirmPassword as FortifyConfirmPassword;
use Laravel\Fortify\Http\Controllers\ConfirmablePasswordController as FortifyConfirmablePasswordControllerBase;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Confirma senha sem coluna 'login' (evita query quebrada em 2FA)
        $this->app->singleton(FortifyConfirmPassword::class, ConfirmPasswordAction::class);
        // Controller de confirmar senha: usa use_senha em vez de guard->validate (coluna 'login')
        $this->app->bind(FortifyConfirmablePasswordControllerBase::class, ConfirmPasswordControllerOverride::class);
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
