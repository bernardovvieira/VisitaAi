<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\CheckApproved;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use App\Http\Middleware\SecurityHeaders;
use App\Http\Middleware\TrustProxies;
use App\Http\Middleware\SetAppDisplayName;
use App\Http\Middleware\RequirePrimaryLocal;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Define explicitamente o grupo "web" (proxy, headers, sessão, CSRF)
        $middleware->group('web', [
            TrustProxies::class,
            SecurityHeaders::class,
            SetAppDisplayName::class,
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            ShareErrorsFromSession::class,
            ValidateCsrfToken::class,
            SubstituteBindings::class,
        ]);

        // Alias de middleware personalizados
        $middleware->alias([
            'approved' => CheckApproved::class,
            'require.primary.local' => RequirePrimaryLocal::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Página não encontrada.'], 404);
            }
            return response()->view('errors.404', [], 404);
        });

        // 401 em rotas que pedem JSON (ex.: sugestões no celular): retornar JSON em vez de redirect para login
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson() || $request->is('agente/sugestoes-doencas', 'saude/sugestoes-doencas')) {
                return response()->json(['message' => 'Sessão expirada. Recarregue a página.'], 401);
            }
            return null;
        });

        // Erros em rotas de sync e sugestões: retornar JSON para o front (evita HTML no celular)
        $exceptions->render(function (\Throwable $e, \Illuminate\Http\Request $request) {
            if ($request->is('agente/visitas-sync', 'saude/visitas-sync') && $request->isMethod('POST')) {
                \Illuminate\Support\Facades\Log::error('visitas-sync: ' . $e->getMessage(), ['exception' => $e]);
                return response()->json([
                    'sincronizados' => 0,
                    'erros' => [['index' => 0, 'message' => 'Erro no servidor: ' . $e->getMessage()]],
                ], 500);
            }
            if ($request->is('agente/sugestoes-doencas', 'saude/sugestoes-doencas')) {
                \Illuminate\Support\Facades\Log::warning('sugestoes-doencas: ' . $e->getMessage());
                return response()->json([
                    'sugestoes' => [],
                    'doencas' => [],
                    'message' => 'Não foi possível carregar sugestões. Tente novamente.',
                ], 500);
            }
            return null;
        });
    })
    ->create();