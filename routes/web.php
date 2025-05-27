<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Middleware\CheckApproved;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Gestor\UserApprovalController;
use App\Http\Controllers\Gestor\UserController;
use App\Http\Controllers\Gestor\LogController;
use App\Http\Controllers\DoencaController;
use App\Http\Controllers\LocalController;
use App\Http\Controllers\VisitaController;
use App\Http\Controllers\RelatorioController;
use App\Http\Controllers\ConsultaPublicaController;
use App\Http\Controllers\PublicController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Página pública
Route::get('/', [PublicController::class, 'welcome']);

// Consulta pública
Route::get('/consulta-publica', [ConsultaPublicaController::class, 'index'])->name('consulta.index');
Route::get('/consulta-publica/codigo', [ConsultaPublicaController::class, 'consultaPorCodigo'])->name('consulta.codigo');

// Conta pendente
Route::view('/pendente', 'auth.pending')->name('pendente');

// ÁREA LOGADA
Route::middleware('auth')->group(function () {

    // Redirecionamento conforme perfil
    Route::middleware(CheckApproved::class)->get('/dashboard', function (Request $request) {
        $user = $request->user();

        return match ($user->use_perfil) {
            'gestor'          => redirect()->route('gestor.dashboard'),
            'agente_endemias' => redirect()->route('agente.dashboard'),
            'agente_saude'    => redirect()->route('saude.dashboard'),
            default           => view('dashboard'),
        };
    })->name('dashboard');

    // USUÁRIOS APROVADOS
    Route::middleware(CheckApproved::class)->group(function () {

        /**
         * AGENTE DE ENDEMIAS
         */
        Route::prefix('agente')->name('agente.')->group(function () {
            Route::view('/dashboard', 'agente.dashboard')->name('dashboard');

            Route::resource('locais', LocalController::class)
                ->parameters(['locais' => 'local'])
                ->except(['show'])
                ->middleware('can:viewAny,App\Models\Local');

            Route::get('locais/{local}', [LocalController::class, 'show'])
                ->name('locais.show')
                ->middleware('can:view,local');

            Route::resource('visitas', VisitaController::class)
                ->except(['show'])
                ->middleware('can:viewAny,App\Models\Visita');

            Route::get('visitas/{visita}', [VisitaController::class, 'show'])
                ->name('visitas.show')
                ->middleware('can:view,visita');

            Route::get('doencas', [DoencaController::class, 'index'])->name('doencas.index')
                ->middleware('can:viewAny,App\Models\Doenca');

            Route::get('doencas/{doenca}', [DoencaController::class, 'show'])->name('doencas.show')
                ->middleware('can:view,doenca');
        });

        /**
         * AGENTE DE SAÚDE
         */
        Route::prefix('saude')->name('saude.')->group(function () {
            Route::view('/dashboard', 'saude.dashboard')->name('dashboard');

            Route::resource('visitas', VisitaController::class)
                ->only(['index', 'create', 'store', 'show'])
                ->middleware('can:viewAny,App\Models\Visita');
        });

        /**
         * GESTOR
         */
        Route::middleware('can:isGestor')->prefix('gestor')->name('gestor.')->group(function () {
            Route::view('/dashboard', 'gestor.dashboard')->name('dashboard');

            Route::get('/pendentes', [UserApprovalController::class, 'index'])->name('pendentes');
            Route::post('/approve/{user}', [UserApprovalController::class, 'approve'])->name('approve');

            Route::resource('users', UserController::class)->except(['show']);
            Route::resource('doencas', DoencaController::class);
            Route::resource('locais', LocalController::class)
                ->parameters(['locais' => 'local'])
                ->except(['show']);

            Route::get('locais/{local}', [LocalController::class, 'show'])->name('locais.show');

            Route::get('visitas', [VisitaController::class, 'index'])->name('visitas.index');
            Route::get('visitas/{visita}', [VisitaController::class, 'show'])->name('visitas.show');

            Route::get('relatorios', [RelatorioController::class, 'index'])->name('relatorios.index');
            Route::post('relatorios/pdf', [RelatorioController::class, 'gerarPdf'])->name('relatorios.pdf');

            Route::get('logs', [LogController::class, 'index'])->name('logs.index');
        });

        /**
         * PERFIL DO USUÁRIO
         */
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });
});

// Autenticação Breeze / Fortify
require __DIR__.'/auth.php';