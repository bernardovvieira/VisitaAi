<?php

use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('users:inactivate-inactive', function () {
    $limiteInatividade = now()->subMonthsNoOverflow(2);

    $inativados = User::query()
        ->where('use_aprovado', true)
        ->whereNull('use_data_anonimizacao')
        ->where(function ($query) use ($limiteInatividade) {
            $query->where(function ($subQuery) use ($limiteInatividade) {
                $subQuery->whereNotNull('use_ultimo_login_em')
                    ->where('use_ultimo_login_em', '<=', $limiteInatividade);
            })->orWhere(function ($subQuery) use ($limiteInatividade) {
                $subQuery->whereNull('use_ultimo_login_em')
                    ->where('use_data_criacao', '<=', $limiteInatividade);
            });
        })
        ->update(['use_aprovado' => false]);

    $this->info("Usuários inativados por inatividade: {$inativados}");
})->purpose('Inativa automaticamente usuários sem login há mais de 2 meses');

Schedule::command('users:inactivate-inactive')->dailyAt('01:00');
