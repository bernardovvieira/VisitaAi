<?php

namespace App\Http\Middleware;

use App\Models\Local;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Quando o gestor ainda não cadastrou o local primário, redireciona para o cadastro.
 * Bloqueia acesso a qualquer outra rota do gestor até que exista pelo menos um local.
 */
class RequirePrimaryLocal
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->isGestor()) {
            return $next($request);
        }

        if (Local::count() > 0) {
            return $next($request);
        }

        $routeName = $request->route()?->getName();
        if ($routeName === 'gestor.locais.create' || $routeName === 'gestor.locais.store') {
            return $next($request);
        }

        return redirect()
            ->route('gestor.locais.create')
            ->with('info', 'Cadastre o local primário do município (ex.: prefeitura ou secretaria de saúde) para iniciar o uso do sistema.');
    }
}
