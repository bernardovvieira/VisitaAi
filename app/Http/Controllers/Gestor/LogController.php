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

        if ($request->filled('search')) {
            $search = $request->input('search');

            $query->whereHas('usuario', function ($q) use ($search) {
                $q->where('use_nome', 'like', "%{$search}%");
            })
            ->orWhere('log_acao', 'like', "%{$search}%")
            ->orWhere('log_entidade', 'like', "%{$search}%")
            ->orWhere('log_descricao', 'like', "%{$search}%");
        }

        $logs = $query->paginate(15)->appends(['search' => $request->search]);

        return view('gestor.logs.index', compact('logs'));
    }
}