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
use App\Http\Controllers\Api\SugestaoDoencasController;
use App\Models\Local;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Ping para detecção de conexão (Windows/macOS/iOS/Android) — retorna 204
Route::get('/ping', function () {
    return response('', 204);
})->name('ping');

// Página pública
Route::get('/', [PublicController::class, 'welcome']);

// Consulta pública (throttle: 10 consultas por minuto por IP)
Route::get('/consulta-publica', [ConsultaPublicaController::class, 'index'])->name('consulta.index');
Route::get('/consulta-publica/codigo', [ConsultaPublicaController::class, 'consultaPorCodigo'])
    ->middleware('throttle:10,1')
    ->name('consulta.codigo');

// Conta pendente
Route::view('/pendente', 'auth.pending')->name('pendente');

// ÁREA LOGADA
Route::middleware('auth')->group(function () {

    // Redirecionamento conforme perfil
    Route::get('/dashboard', function (Request $request) {
        $user = $request->user();

        return match ($user->use_perfil) {
            'gestor'          => redirect()->route('gestor.dashboard'),
            'agente_endemias' => redirect()->route('agente.dashboard'),
            'agente_saude'    => redirect()->route('saude.dashboard'),
            default           => redirect()->route('pendente'),
        };
    })->name('dashboard');

    // USUÁRIOS APROVADOS
    Route::middleware(['auth', CheckApproved::class])->group(function () {

        /**
         * ACE (Agente de Combate às Endemias) — Lei 11.350/2006
         */
        Route::middleware('perfil:agente_endemias')->prefix('agente')->name('agente.')->group(function () {
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

            Route::get('sincronizar', [VisitaController::class, 'syncPage'])->name('sincronizar');
            Route::get('visitas-sync', fn () => redirect()->route('agente.sincronizar'))->name('visitas.sync');
            Route::post('visitas-sync', [VisitaController::class, 'syncStore'])->name('visitas.sync.submit')
                ->middleware('throttle:60,1');
            Route::post('locais-sync', [LocalController::class, 'syncStore'])->name('locais.sync.submit')
                ->middleware('throttle:60,1');

            Route::get('doencas', [DoencaController::class, 'index'])->name('doencas.index')
                ->middleware('can:viewAny,App\Models\Doenca');

            Route::get('doencas/{doenca}', [DoencaController::class, 'show'])->name('doencas.show')
                ->middleware('can:view,doenca');

            Route::get('sugestoes-doencas', SugestaoDoencasController::class)->name('sugestoes-doencas');
        });

        /**
         * ACS (Agente Comunitário de Saúde) — Lei 11.350/2006
         */
        Route::middleware('perfil:agente_saude')->prefix('saude')->name('saude.')->group(function () {
            Route::view('/dashboard', 'saude.dashboard')->name('dashboard');

            Route::resource('visitas', VisitaController::class)
                ->only(['index', 'create', 'store', 'show'])
                ->middleware('can:viewAny,App\Models\Visita');

            Route::get('sincronizar', [VisitaController::class, 'syncPage'])->name('sincronizar');
            Route::get('visitas-sync', fn () => redirect()->route('saude.sincronizar'))->name('visitas.sync');
            Route::post('visitas-sync', [VisitaController::class, 'syncStore'])->name('visitas.sync.submit')
                ->middleware('throttle:60,1');

            Route::get('doencas', [DoencaController::class, 'index'])->name('doencas.index')
                ->middleware('can:viewAny,App\Models\Doenca');

            Route::get('doencas/{doenca}', [DoencaController::class, 'show'])->name('doencas.show')
                ->middleware('can:view,doenca');

            Route::get('sugestoes-doencas', SugestaoDoencasController::class)->name('sugestoes-doencas');
        });

        /**
         * GESTOR
         */
        Route::middleware(['can:isGestor', 'require.primary.local'])->prefix('gestor')->name('gestor.')->group(function () {
            Route::get('/dashboard', fn () => view('gestor.dashboard'))->name('dashboard');

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
            Route::get('relatorios/pdf', fn () => redirect()->route('gestor.relatorios.index')->with('info', 'Use os filtros na página de relatórios e clique em "Gerar relatório em PDF" para gerar o documento.'));
            Route::post('relatorios/pdf', [RelatorioController::class, 'gerarPdf'])->name('relatorios.pdf');

            Route::get('logs', [LogController::class, 'index'])->name('logs.index');
        });

        /**
         * PERFIL DO USUÁRIO
         */
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::patch('/profile/tema', [ProfileController::class, 'updateTema'])->name('profile.tema.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
        Route::get('/profile/two-factor', [ProfileController::class, 'twoFactor'])->name('profile.two-factor');
        Route::get('/profile/two-factor-confirm', [ProfileController::class, 'twoFactorConfirm'])->name('profile.two-factor-confirm');
    });
});

if (app()->environment('local')) {
    Route::get('/debug-session', function (\Illuminate\Http\Request $request) {
        session(['test' => 'ok']);

        return [
            'session'    => session()->all(),
            'config'     => config('session'),
            'middleware' => $request->route()->gatherMiddleware(),
        ];
    });

    Route::get('/debug-cookie', function () {
        $response = response('ok from laravel cookie');
        $response->cookie('test_cookie', '123', 60);

        return $response;
    });
}

// Autenticação (Fortify/Breeze)
require __DIR__.'/auth.php';