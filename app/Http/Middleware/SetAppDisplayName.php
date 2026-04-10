<?php

namespace App\Http\Middleware;

use App\Models\Local;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

/**
 * Define o nome da aplicação para exibição: Visita Aí - {prefixo}. Imóveis, visitas e cadastro municipal (saúde pública / PNCD).
 * - APP_NAME=Base ou APP_INSTANCE_TYPE=base → Base
 * - APP_NAME=Demo ou APP_INSTANCE_TYPE=demo → Demo
 * - Local com cidade cadastrada → loc_cidade do primeiro Local
 * - Caso contrário → Local
 */
class SetAppDisplayName
{
    private const SUFIXO = ' - Imóveis, visitas e cadastro municipal (saúde pública / PNCD)';

    public function handle(Request $request, Closure $next): Response
    {
        $prefixo = $this->resolvePrefixo();
        config(['app.name' => 'Visita Aí - '.$prefixo.self::SUFIXO]);

        return $next($request);
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
