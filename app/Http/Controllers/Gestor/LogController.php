<?php

namespace App\Http\Controllers\Gestor;

use App\Http\Controllers\Controller;
use App\Models\Log;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index(Request $request)
    {
        $query = Log::with('usuario')->orderByDesc('log_data');

        $search = trim((string) $request->input('search'));
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->whereHas('usuario', fn($u) => $u->where('use_nome', 'like', "%{$search}%")->orWhere('use_email', 'like', "%{$search}%"))
                  ->orWhere('log_acao', 'like', "%{$search}%")
                  ->orWhere('log_entidade', 'like', "%{$search}%")
                  ->orWhere('log_descricao', 'like', "%{$search}%")
                  ->orWhere('log_ip', 'like', "%{$search}%");
            });
        }

        $logs = $query->paginate(15)->appends(['search' => $search]);

        return view('gestor.logs.index', compact('logs'));
    }
}