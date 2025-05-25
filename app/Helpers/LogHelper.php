<?php

// app/Helpers/LogHelper.php
namespace App\Helpers;

use App\Models\Log;
use Illuminate\Support\Facades\Auth;

class LogHelper
{
    public static function registrar(string $acao, string $entidade, string $tipo, string $descricao = null)
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
