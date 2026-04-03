<?php

/**
 * Rotas públicas e semi-públicas (sem sessão de utilizador obrigatória).
 */

use App\Http\Controllers\ConsultaPublicaController;
use App\Http\Controllers\PublicController;
use Illuminate\Support\Facades\Route;

Route::get('/ping', function () {
    return response('', 204);
})->name('ping');

Route::get('/', [PublicController::class, 'welcome']);

Route::get('/consulta-publica', [ConsultaPublicaController::class, 'index'])->name('consulta.index');
Route::get('/consulta-publica/codigo', [ConsultaPublicaController::class, 'consultaPorCodigo'])
    ->middleware('throttle:10,1')
    ->name('consulta.codigo');

Route::view('/pendente', 'auth.pending')->name('pendente');
