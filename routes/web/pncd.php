<?php

/**
 * Módulo operacional PNCD / vigilância entomológica (ACE e ACS no campo).
 * Rotas HTTP de agentes; nomes `agente.*` e `saude.*` mantidos por compatibilidade.
 */

use App\Http\Controllers\Api\SugestaoDoencasController;
use App\Http\Controllers\DoencaController;
use App\Http\Controllers\Municipio\MoradorController;
use App\Http\Controllers\Vigilancia\LocalController;
use App\Http\Controllers\Vigilancia\VisitaController;
use Illuminate\Support\Facades\Route;

Route::middleware('perfil:agente_endemias')->prefix('agente')->name('agente.')->group(function () {
    Route::view('/dashboard', 'agente.dashboard')->name('dashboard');

    Route::resource('locais', LocalController::class)
        ->parameters(['locais' => 'local'])
        ->except(['show'])
        ->middleware('can:viewAny,App\Models\Local');

    Route::get('locais/{local}/ficha-socioeconomica.pdf', [LocalController::class, 'fichaSocioeconomicaPdf'])
        ->name('locais.ficha-socioeconomica-pdf')
        ->middleware(['can:view,local', 'throttle:30,1']);

    Route::get('locais/{local}/documento-posse', [LocalController::class, 'downloadDocumentoPosse'])
        ->name('locais.documento-posse')
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

    Route::get('sugestoes-doencas', SugestaoDoencasController::class)
        ->middleware('throttle:120,1')
        ->name('sugestoes-doencas');
});

Route::middleware('perfil:agente_saude')->prefix('saude')->name('saude.')->group(function () {
    Route::view('/dashboard', 'saude.dashboard')->name('dashboard');

    Route::resource('locais', LocalController::class)
        ->parameters(['locais' => 'local'])
        ->except(['show'])
        ->middleware('can:viewAny,App\Models\Local');

    Route::get('locais/{local}/ficha-socioeconomica.pdf', [LocalController::class, 'fichaSocioeconomicaPdf'])
        ->name('locais.ficha-socioeconomica-pdf')
        ->middleware(['can:view,local', 'throttle:30,1']);

    Route::get('locais/{local}/documento-posse', [LocalController::class, 'downloadDocumentoPosse'])
        ->name('locais.documento-posse')
        ->middleware(['can:view,local', 'throttle:30,1']);

    Route::get('locais/{local}/moradores/{morador}/documento-pessoal', [MoradorController::class, 'downloadDocumentoPessoal'])
        ->name('locais.moradores.documento-pessoal')
        ->middleware(['can:view,local', 'can:view,morador', 'throttle:30,1']);

    Route::get('locais/{local}', [LocalController::class, 'show'])
        ->name('locais.show')
        ->middleware('can:view,local');

    Route::resource('locais.moradores', MoradorController::class)
        ->parameters(['locais' => 'local', 'moradores' => 'morador'])
        ->except(['show']);

    Route::resource('visitas', VisitaController::class)
        ->only(['index', 'create', 'store', 'show'])
        ->middleware('can:viewAny,App\Models\Visita');

    Route::get('sincronizar', [VisitaController::class, 'syncPage'])->name('sincronizar');
    Route::get('visitas-sync', fn () => redirect()->route('saude.sincronizar'))->name('visitas.sync');
    Route::post('visitas-sync', [VisitaController::class, 'syncStore'])->name('visitas.sync.submit')
        ->middleware('throttle:60,1');
    Route::post('locais-sync', [LocalController::class, 'syncStore'])->name('locais.sync.submit')
        ->middleware('throttle:60,1');

    Route::get('doencas', [DoencaController::class, 'index'])->name('doencas.index')
        ->middleware('can:viewAny,App\Models\Doenca');

    Route::get('doencas/{doenca}', [DoencaController::class, 'show'])->name('doencas.show')
        ->middleware('can:view,doenca');

    Route::get('sugestoes-doencas', SugestaoDoencasController::class)
        ->middleware('throttle:120,1')
        ->name('sugestoes-doencas');
});
