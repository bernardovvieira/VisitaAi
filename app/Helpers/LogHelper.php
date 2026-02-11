<?php

namespace App\Helpers;

use App\Models\Log;
use Illuminate\Support\Facades\Auth;

class LogHelper
{
    /**
     * Registra ação no log de auditoria.
     * Não inclua senhas, tokens ou dados sensíveis em $descricao.
     */
    public static function registrar(string $acao, string $entidade, string $tipo, ?string $descricao = null): void
    {
        Log::create([
            'log_user_id' => Auth::id(),
            'log_acao' => $acao,
            'log_entidade' => $entidade,
            'log_tipo' => $tipo,
            'log_descricao' => $descricao,
        ]);
    }
}
