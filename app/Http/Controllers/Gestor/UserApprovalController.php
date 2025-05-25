<?php

namespace App\Http\Controllers\Gestor;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Helpers\LogHelper;

class UserApprovalController extends Controller
{
    /**
     * Exibe todos os usuários ainda não aprovados.
     */
    public function index(): View
    {
        $pendentes = User::where('use_aprovado', false)->orderBy('use_data_criacao')->get();

        return view('gestor.users.pendentes', compact('pendentes'));
    }

    /**
     * Aprova um usuário específico e registra o gestor que aprovou.
     */
    public function approve(User $user): RedirectResponse
    {
        // Recupera o ID do gestor logado
        $gestorId = Auth::user()->use_id;

        // Atualiza o usuário: marca aprovado e define quem aprovou
        $user->update([
            'use_aprovado'  => true,
            'fk_gestor_id' => $gestorId,
        ]);

        LogHelper::registrar(
            'Aprovação de usuário',
            'Usuário',
            'approve',
            'Usuário aprovado: ' . $user->use_nome . ' (ID: ' . $user->use_id . ')'
        );

        return back()->with('status', 'Usuário aprovado com sucesso!');
    }
}
