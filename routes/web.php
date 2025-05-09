<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Gestor\UserApprovalController;
use App\Http\Controllers\Gestor\UserController;
use App\Http\Controllers\DoencaController;
use App\Http\Middleware\CheckApproved;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Página pública
Route::view('/', 'welcome');

// Página de "conta pendente"
Route::view('/pendente', 'auth.pending')->name('pendente');

// ----------------------------
// ÁREA LOGADA
// ----------------------------
Route::middleware('auth')->group(function () {

    // Dashboard genérico (redireciona conforme perfil)
    Route::middleware(CheckApproved::class)
        ->get('/dashboard', function (Request $request) {
            return $request->user()->isGestor()
                ? redirect()->route('gestor.dashboard')
                : redirect()->route('agente.dashboard');
        })
        ->name('dashboard');

    // Rotas para usuários aprovados
    Route::middleware(CheckApproved::class)->group(function () {

        // Dashboard do Agente
        Route::view('/agente/dashboard', 'agente.dashboard')->name('agente.dashboard');

        // Rotas específicas do Agente
        Route::prefix('agente')->name('agente.')->group(function () {
            Route::view('visitas', 'agente.dashboard')->name('visitas.index');
            Route::view('visitas/create', 'agente.dashboard')->name('visitas.create');
            Route::view('locais', 'agente.dashboard')->name('locais.index');
            Route::view('doencas', 'agente.dashboard')->name('doencas.index');
        });

        // Perfil
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

        // Dashboard do Gestor
        Route::view('/gestor/dashboard', 'gestor.dashboard')->name('gestor.dashboard');

        // Rotas exclusivas do Gestor
        Route::middleware('can:isGestor')->prefix('gestor')->name('gestor.')->group(function () {
            Route::get('/pendentes', [UserApprovalController::class, 'index'])->name('pendentes');
            Route::post('/approve/{user}', [UserApprovalController::class, 'approve'])->name('approve');

            // CRUD completo de usuários (RF02)
            Route::resource('users', UserController::class)->except(['show']);

            // CRUD completo de doenças (RF03)
            Route::resource('doencas', DoencaController::class)->except(['show']);
        });

    });

});

// Rotas de autenticação Breeze / Fortify
require __DIR__.'/auth.php';