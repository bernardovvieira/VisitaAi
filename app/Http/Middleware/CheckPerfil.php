<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPerfil
{
    public function handle(Request $request, Closure $next, ...$perfis)
    {
        $user = $request->user();

        if (! $user || ! in_array($user->use_perfil, $perfis)) {
            abort(403);
        }

        return $next($request);
    }
}
