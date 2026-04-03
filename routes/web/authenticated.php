<?php

/**
 * Área autenticada: dashboards por perfil, operação PNCD e complemento municipal (ocupantes do imóvel).
 */

use App\Http\Controllers\Api\SugestaoDoencasController;
use App\Http\Controllers\DoencaController;
use App\Http\Controllers\Gestor\LogController;
use App\Http\Controllers\Gestor\UserApprovalController;
use App\Http\Controllers\Gestor\UserController;
use App\Http\Controllers\LocalController;
use App\Http\Controllers\MoradorController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RelatorioController;
use App\Http\Controllers\VisitaController;
use App\Http\Middleware\CheckApproved;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', function (Request $request) {
    $user = $request->user();

    return match ($user->use_perfil) {
        'gestor' => redirect()->route('gestor.dashboard'),
        'agente_endemias' => redirect()->route('agente.dashboard'),
        'agente_saude' => redirect()->route('saude.dashboard'),
        default => redirect()->route('pendente'),
    };
})->name('dashboard');

Route::middleware(['auth', CheckApproved::class])->group(function () {

    Route::middleware('perfil:agente_endemias')->prefix('agente')->name('agente.')->group(function () {
        Route::view('/dashboard', 'agente.dashboard')->name('dashboard');

        Route::resource('locais', LocalController::class)
            ->parameters(['locais' => 'local'])
            ->except(['show'])
            ->middleware('can:viewAny,App\Models\Local');

        Route::get('locais/{local}', [LocalController::class, 'show'])
            ->name('locais.show')
            ->middleware('can:view,local');

        Route::resource('locais.moradores', MoradorController::class)
            ->parameters(['locais' => 'local', 'moradores' => 'morador']);

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

        Route::resource('locais.moradores', MoradorController::class)
            ->parameters(['locais' => 'local', 'moradores' => 'morador']);

        Route::get('visitas', [VisitaController::class, 'index'])->name('visitas.index');
        Route::get('visitas/{visita}', [VisitaController::class, 'show'])->name('visitas.show');

        Route::get('relatorios', [RelatorioController::class, 'index'])->name('relatorios.index');
        Route::get('relatorios/pdf', fn () => redirect()->route('gestor.relatorios.index')->with('info', 'Use os filtros na página de relatórios e clique em "Gerar relatório em PDF" para gerar o documento.'));
        Route::post('relatorios/pdf', [RelatorioController::class, 'gerarPdf'])->name('relatorios.pdf');

        Route::get('logs', [LogController::class, 'index'])->name('logs.index');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/tema', [ProfileController::class, 'updateTema'])->name('profile.tema.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/profile/two-factor', [ProfileController::class, 'twoFactor'])->name('profile.two-factor');
    Route::get('/profile/two-factor-confirm', [ProfileController::class, 'twoFactorConfirm'])->name('profile.two-factor-confirm');
});
