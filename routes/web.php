<?php

use App\Http\Middleware\CheckApproved;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

require __DIR__.'/web/public.php';

Route::middleware('auth')->group(function () {
    require __DIR__.'/web/authenticated.php';
});

/*
 * Rotas de diagnóstico apenas em ambiente local, com sessão autenticada e aprovada.
 * Não expor conteúdo de sessão/config em resposta HTTP (risco de vazamento em dev compartilhado).
 */
if (app()->environment('local')) {
    Route::middleware(['auth', CheckApproved::class])->group(function () {
        Route::get('/debug-session', function (Request $request) {
            return response()->json([
                'ok' => true,
                'authenticated' => $request->user() !== null,
                'middleware' => $request->route()->gatherMiddleware(),
            ]);
        });

        Route::get('/debug-cookie', function () {
            $response = response('ok from laravel cookie');
            $response->cookie('test_cookie', '123', 60);

            return $response;
        });
    });
}

require __DIR__.'/auth.php';
