<?php

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

if (app()->environment('local')) {
    Route::get('/debug-session', function (Request $request) {
        session(['test' => 'ok']);

        return [
            'session' => session()->all(),
            'config' => config('session'),
            'middleware' => $request->route()->gatherMiddleware(),
        ];
    });

    Route::get('/debug-cookie', function () {
        $response = response('ok from laravel cookie');
        $response->cookie('test_cookie', '123', 60);

        return $response;
    });
}

require __DIR__.'/auth.php';
