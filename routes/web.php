<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Gestor\UserApprovalController;
use App\Http\Controllers\Gestor\UserController;
use App\Http\Controllers\DoencaController;
use App\Http\Controllers\VisitaController;
use App\Http\Controllers\LocalController;
use App\Http\Controllers\RelatorioController;
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

            // Locais
            Route::resource('locais', LocalController::class)
                ->parameters(['locais' => 'local']) // 👈 ADICIONE ISSO
                ->except(['show'])
                ->middleware('can:viewAny,App\Models\Local');

            Route::get('locais/{local}', [LocalController::class, 'show'])
                ->name('locais.show')
                ->middleware('can:view,local');

            // Visitas
            Route::resource('visitas', VisitaController::class)
                ->except(['show'])
                ->middleware('can:viewAny,App\Models\Visita');

            Route::get('visitas/{visita}', [VisitaController::class, 'show'])
                ->name('visitas.show')
                ->middleware('can:view,visita');

            // Doenças (somente leitura)
            Route::get('doencas', [DoencaController::class, 'index'])
                ->name('doencas.index')
                ->middleware('can:viewAny,App\Models\Doenca');

            Route::get('doencas/{doenca}', [DoencaController::class, 'show'])
                ->name('doencas.show')
                ->middleware('can:view,doenca');
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
            Route::resource('doencas', DoencaController::class)
                ->middleware('auth');

            // CRUD completo de locais (RF04)
            Route::resource('locais', LocalController::class)
                ->parameters(['locais' => 'local']) 
                ->except(['show'])
                ->middleware('can:viewAny,App\Models\Local');


            Route::get('locais/{local}', [LocalController::class, 'show'])
                ->name('locais.show')
                ->middleware('can:view,local');

            // CRUD completo de visitas 
            Route::get('visitas', [VisitaController::class, 'index'])
                ->name('visitas.index')
                ->middleware('can:viewAny,App\Models\Visita');

            Route::get('visitas/{visita}', [VisitaController::class, 'show'])
                ->name('visitas.show')
                ->middleware('can:view,visita');

            // CRUD completo de relatórios (RF05)
            Route::get('relatorios', [RelatorioController::class, 'index'])
                ->name('relatorios.index');
            
            Route::post('relatorios/pdf', [RelatorioController::class, 'gerarPdf'])
                ->name('relatorios.pdf');

        });

    });

});

// Rotas de autenticação Breeze / Fortify
require __DIR__.'/auth.php';