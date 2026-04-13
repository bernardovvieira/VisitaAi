<?php

namespace App\Http\Controllers\Gestor;

use App\Http\Controllers\Controller;
use App\Models\Log;
use App\Support\SmartSearch;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index(Request $request)
    {
        $query = Log::with('usuario')->orderByDesc('log_data');

        $search = trim((string) $request->input('search'));
        $terms = SmartSearch::terms($search);
        if ($search !== '') {
            $query->where(function ($q) use ($terms) {
                foreach ($terms as $term) {
                    $like = '%'.$term.'%';
                    $q->orWhereHas('usuario', fn ($u) => $u
                        ->whereRaw('LOWER(COALESCE(use_nome, "")) LIKE ?', [$like])
                        ->orWhereRaw(SmartSearch::foldExpr('use_nome').' LIKE ?', [$like])
                        ->orWhereRaw('LOWER(COALESCE(use_email, "")) LIKE ?', [$like])
                        ->orWhereRaw('CAST(use_id AS CHAR) LIKE ?', [$like]))
                        ->orWhereRaw('LOWER(COALESCE(log_acao, "")) LIKE ?', [$like])
                        ->orWhereRaw('LOWER(COALESCE(log_entidade, "")) LIKE ?', [$like])
                        ->orWhereRaw('LOWER(COALESCE(log_descricao, "")) LIKE ?', [$like])
                        ->orWhereRaw('LOWER(COALESCE(log_ip, "")) LIKE ?', [$like])
                        ->orWhereRaw('LOWER(COALESCE(log_user_agent, "")) LIKE ?', [$like])
                        ->orWhereRaw('CAST(log_id AS CHAR) LIKE ?', [$like]);
                }
            });
        }

        $logs = $query->paginate(15)->appends(['search' => $search]);

        return view('gestor.logs.index', compact('logs'));
    }
}
