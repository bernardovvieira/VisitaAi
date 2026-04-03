<?php

namespace App\Providers;

use App\Models\Doenca;
use App\Models\Local;
use App\Models\Log;
use App\Models\Monitorada;
use App\Models\Morador;
use App\Models\User;
use App\Models\Visita;
use App\Policies\DoencaPolicy;
use App\Policies\LocalPolicy;
use App\Policies\LogPolicy;
use App\Policies\MonitoradaPolicy;
use App\Policies\MoradorPolicy;
use App\Policies\UserPolicy;
use App\Policies\VisitaPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Mapas de policies (adicione aqui quando criar políticas).
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Doenca::class => DoencaPolicy::class,
        Local::class => LocalPolicy::class,
        Visita::class => VisitaPolicy::class,
        Monitorada::class => MonitoradaPolicy::class,
        Log::class => LogPolicy::class,
        Morador::class => MoradorPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        /*
        |---------------------------------------------------------------
        | GATES SIMPLES
        |---------------------------------------------------------------
        */

        // Apenas usuários com perfil 'gestor'
        Gate::define('isGestor', fn (User $user) => $user->isGestor());

        // Apenas usuários aprovados
        Gate::define('isAprovado', fn (User $user) => $user->isAprovado());

        // ACS (Agente Comunitário de Saúde) — Lei 11.350/2006
        Gate::define('isAgenteSaude', fn (User $user) => $user->isAgenteSaude());

        // ACE (Agente de Combate às Endemias) — Lei 11.350/2006
        Gate::define('isAgenteEndemias', fn (User $user) => $user->isAgenteEndemias());
    }
}
