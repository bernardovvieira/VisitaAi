<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('X-DNS-Prefetch-Control', 'off');
        $response->headers->set('X-Permitted-Cross-Domain-Policies', 'none');
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=(), payment=(), usb=()');

        if ($request->secure() && config('security-headers.hsts_enabled', false)) {
            $maxAge = max(0, (int) config('security-headers.hsts_max_age', 31536000));
            $parts = ['max-age='.$maxAge];
            if (config('security-headers.hsts_include_subdomains', false)) {
                $parts[] = 'includeSubDomains';
            }
            if (config('security-headers.hsts_preload', false)) {
                $parts[] = 'preload';
            }
            $response->headers->set('Strict-Transport-Security', implode('; ', $parts));
        }

        if (config('security-headers.csp_enabled', true)) {
            $csp = implode('; ', config('security-headers.csp_directives', []));
            if ($csp !== '') {
                $response->headers->set('Content-Security-Policy', $csp);
            }
        }

        return $response;
    }
}
