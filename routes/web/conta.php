<?php

/**
 * Módulo de conta do utilizador (perfil, tema, 2FA, encerrar sessão).
 */

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
Route::patch('/profile', [ProfileController::class, 'update'])
    ->middleware('throttle:20,1')
    ->name('profile.update');
Route::patch('/profile/tema', [ProfileController::class, 'updateTema'])
    ->middleware('throttle:60,1')
    ->name('profile.tema.update');
Route::delete('/profile', [ProfileController::class, 'destroy'])
    ->middleware('throttle:5,1')
    ->name('profile.destroy');
Route::get('/profile/two-factor', [ProfileController::class, 'twoFactor'])->name('profile.two-factor');
Route::get('/profile/two-factor-confirm', [ProfileController::class, 'twoFactorConfirm'])->name('profile.two-factor-confirm');
