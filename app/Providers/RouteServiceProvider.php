<?php

namespace App\Providers;

use App\Http\Middleware\CheckApproved;
use App\Http\Middleware\CheckPerfil;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Rota padrão pós‑login.
     */
    public const HOME = '/dashboard';

    /**
     * Bootstrap.
     */
    public function boot(): void
    {
        /* -------------------------------------------------------------
         | Alias para o middleware "approved"
         |-------------------------------------------------------------
         | Se já existir (registrado em bootstrap/app.php, p.ex.),
         | não tentamos registrar de novo.
         */
        if (! array_key_exists('approved', Route::getFacadeRoot()->getMiddleware())) {
            Route::aliasMiddleware('approved', CheckApproved::class);
        }

        if (! array_key_exists('perfil', Route::getFacadeRoot()->getMiddleware())) {
            Route::aliasMiddleware('perfil', CheckPerfil::class);
        }

        // Rate limiting
        $this->configureRateLimiting();

        // Arquivos de rota
        $this->routes(function () {
            Route::middleware('api')
                 ->prefix('api')
                 ->group(base_path('routes/api.php'));

            Route::middleware('web')
                 ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Limites de requisições (API).
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)
                        ->by($request->user()?->id ?? $request->ip());
        });
    }
}
