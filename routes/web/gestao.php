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
    Route::post('/approve/{user}', [UserApprovalController::class, 'approve'])
        ->middleware('throttle:30,1')
        ->name('approve');

    Route::resource('users', UserController::class)->except(['show']);
    Route::resource('doencas', DoencaController::class);
    Route::resource('locais', LocalController::class)
        ->parameters(['locais' => 'local'])
        ->only(['index', 'create', 'store'])
        ->middleware('can:viewAny,App\Models\Local');

    Route::get('locais/{local}/ficha-socioeconomica.pdf', [LocalController::class, 'fichaSocioeconomicaPdf'])
        ->name('locais.ficha-socioeconomica-pdf')
        ->middleware(['can:view,local', 'throttle:30,1']);

    // Individual morador ficha removed — use imóvel ficha instead

    Route::get('locais/{local}/moradores/{morador}/documento-pessoal', [MoradorController::class, 'downloadDocumentoPessoal'])
        ->name('locais.moradores.documento-pessoal')
        ->middleware(['can:view,local', 'can:view,morador', 'throttle:30,1']);

    Route::get('locais/{local}', [LocalController::class, 'show'])
        ->name('locais.show')
        ->middleware('can:view,local');

    Route::resource('locais.moradores', MoradorController::class)
        ->parameters(['locais' => 'local', 'moradores' => 'morador'])
        ->except(['show']);

    Route::get('visitas', [VisitaController::class, 'index'])->name('visitas.index');
    Route::get('visitas/{visita}', [VisitaController::class, 'show'])->name('visitas.show');

    Route::get('indicadores/ocupantes', [IndicadoresOcupantesController::class, 'index'])->name('indicadores.ocupantes');
    Route::get('indicadores/ocupantes/export', [IndicadoresOcupantesController::class, 'exportCsv'])
        ->middleware('throttle:12,1')
        ->name('indicadores.ocupantes.export');
    Route::get('indicadores/ocupantes/export-cadastro', [IndicadoresOcupantesController::class, 'exportCadastroOcupantesCsv'])
        ->middleware('throttle:12,1')
        ->name('indicadores.ocupantes.export-cadastro');
    Route::get('indicadores/ocupantes/export-cadastro-pdf', [IndicadoresOcupantesController::class, 'exportCadastroOcupantesPdf'])
        ->middleware('throttle:8,1')
        ->name('indicadores.ocupantes.export-cadastro-pdf');

    Route::get('relatorios', [RelatorioController::class, 'index'])->name('relatorios.index');
    Route::get('relatorios/pdf', fn () => redirect(route('gestor.relatorios.index'))->with('info', __('Use os filtros na página de relatórios e clique em "Gerar relatório em PDF" para gerar o documento.')));
    Route::post('relatorios/pdf', [RelatorioController::class, 'gerarPdf'])
        ->middleware('throttle:6,1')
        ->name('relatorios.pdf');

    Route::get('logs', [LogController::class, 'index'])->name('logs.index');
});
