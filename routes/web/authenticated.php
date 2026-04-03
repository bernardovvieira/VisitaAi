<?php

/**
 * Área autenticada: redirecionamento por perfil e inclusão por módulo.
 *
 * - pncd: operação de campo (ACE/ACS) — vigilância / PNCD.
 * - gestao: painel do gestor municipal.
 * - conta: perfil e preferências do utilizador.
 */

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
    require __DIR__.'/pncd.php';
    require __DIR__.'/gestao.php';
    require __DIR__.'/conta.php';
});
