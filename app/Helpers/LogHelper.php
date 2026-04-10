<?php

namespace App\Helpers;

use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogHelper
{
    /**
     * Registra ação no log de auditoria.
     * Grava IP e User-Agent do dispositivo quando há request HTTP.
     * Não inclua senhas, tokens ou dados sensíveis em $descricao.
     */
    public static function registrar(string $acao, string $entidade, string $tipo, ?string $descricao = null, ?Request $request = null): void
    {
        $req = $request ?? (function () {
            try {
                return request();
            } catch (\Throwable) {
                return null;
            }
        })();

        $ip = $req ? $req->ip() : null;
        $userAgent = $req ? $req->userAgent() : null;
        if ($userAgent !== null && strlen($userAgent) > 500) {
            $userAgent = substr($userAgent, 0, 500);
        }

        Log::create([
            'log_user_id' => Auth::id(),
            'log_acao' => $acao,
            'log_entidade' => $entidade,
            'log_tipo' => $tipo,
            'log_descricao' => $descricao,
            'log_ip' => $ip,
            'log_user_agent' => $userAgent,
        ]);
    }
}
