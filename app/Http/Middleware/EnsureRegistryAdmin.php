<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRegistryAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $allowed = config('tenant_registry.admin_emails', []);
        if ($allowed === []) {
            abort(403);
        }

        $user = $request->user();
        if ($user === null) {
            return redirect()->route('login');
        }

        $email = strtolower((string) $user->use_email);
        if (! in_array($email, $allowed, true)) {
            abort(403);
        }

        return $next($request);
    }
}
