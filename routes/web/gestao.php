<?php

/**
 * Módulo de gestão municipal (painel do gestor): cadastros, relatórios, usuários,
 * visitas em consulta e complemento municipal (ocupantes do imóvel / Visita Aí).
 */

use App\Http\Controllers\DoencaController;
use App\Http\Controllers\Gestor\LogController;
use App\Http\Controllers\Gestor\UserApprovalController;
use App\Http\Controllers\Gestor\UserController;
use App\Http\Controllers\Municipio\IndicadoresOcupantesController;
use App\Http\Controllers\Municipio\MoradorController;
use App\Http\Controllers\RelatorioController;
use App\Http\Controllers\Vigilancia\LocalController;
use App\Http\Controllers\Vigilancia\VisitaController;
use Illuminate\Support\Facades\Route;

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

    Route::get('indicadores/ocupantes', [IndicadoresOcupantesController::class, 'index'])->name('indicadores.ocupantes');
    Route::get('indicadores/ocupantes/exportar.csv', [IndicadoresOcupantesController::class, 'exportCsv'])
        ->name('indicadores.ocupantes.export')
        ->middleware('throttle:30,1');

    Route::get('relatorios', [RelatorioController::class, 'index'])->name('relatorios.index');
    Route::get('relatorios/pdf', fn () => redirect()->route('gestor.relatorios.index')->with('info', 'Use os filtros na página de relatórios e clique em "Gerar relatório em PDF" para gerar o documento.'));
    Route::post('relatorios/pdf', [RelatorioController::class, 'gerarPdf'])->name('relatorios.pdf');

    Route::get('logs', [LogController::class, 'index'])->name('logs.index');
});
