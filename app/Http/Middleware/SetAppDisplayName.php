<?php

namespace App\Http\Middleware;

use App\Models\Local;
use App\Models\RegistryTenant;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

/**
 * Define o nome da aplicação para exibição: Visita Aí - {prefixo} - Sistema de Apoio à Vigilância Entomológica e Controle Vetorial Municipal
 * - APP_NAME=Base ou APP_INSTANCE_TYPE=base → Base
 * - APP_NAME=Demo ou APP_INSTANCE_TYPE=demo → Demo
 * - Local com cidade cadastrada → loc_cidade do primeiro Local
 * - Caso contrário → Local
 */
class SetAppDisplayName
{
    private const SUFIXO = ' - Sistema de Apoio à Vigilância Entomológica e Controle Vetorial Municipal';

    public function handle(Request $request, Closure $next): Response
    {
        $tenant = app()->bound('registry.tenant') ? app('registry.tenant') : null;

        if ($tenant instanceof RegistryTenant) {
            if (filled($tenant->brand)) {
                config(['app.brand' => $tenant->brand]);
            }
            if (filled($tenant->display_name)) {
                config(['app.name' => $tenant->display_name]);
            } else {
                $prefixo = $this->resolvePrefixoForRegistryTenant($tenant);
                config(['app.name' => 'Visita Aí - '.$prefixo.self::SUFIXO]);
            }

            return $next($request);
        }

        $prefixo = $this->resolvePrefixo();
        config(['app.name' => 'Visita Aí - '.$prefixo.self::SUFIXO]);

        return $next($request);
    }

    private function resolvePrefixoForRegistryTenant(RegistryTenant $tenant): string
    {
        try {
            if (DB::connection()->getPdo() && Local::exists()) {
                $cidade = Local::query()->orderBy('loc_id')->value('loc_cidade');
                if ($cidade) {
                    return $cidade;
                }
            }
        } catch (\Throwable) {
            // DB indisponível
        }

        return $tenant->slug;
    }

    private function resolvePrefixo(): string
    {
        $tipo = strtolower(trim((string) env('APP_INSTANCE_TYPE', '')));
        $appName = trim((string) env('APP_NAME', ''));

        // Ignora APP_NAME no formato antigo (ex.: "Visita Aí - Sistema de Visitas")
        if ($appName !== '' && stripos($appName, 'Visita Aí') === 0) {
            $appName = '';
        }

        if ($tipo === 'base') {
            return 'Base';
        }
        if ($tipo === 'demo') {
            return 'Demo';
        }
        if ($appName !== '') {
            return $appName;
        }

        try {
            if (DB::connection()->getPdo() && Local::exists()) {
                $cidade = Local::first()?->loc_cidade;
                if ($cidade) {
                    return $cidade;
                }
            }
        } catch (\Throwable) {
            // DB indisponível (config:cache, etc.)
        }

        return 'Local';
    }
}
