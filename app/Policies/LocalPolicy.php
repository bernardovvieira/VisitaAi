<?php

namespace App\Policies;

use App\Models\Local;
use App\Models\User;

class LocalPolicy
{
    /**
     * Agentes e gestores podem listar locais.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAgente() || $user->isGestor();
    }

    /**
     * Agentes e gestores podem ver detalhes de um local.
     */
    public function view(User $user, Local $local): bool
    {
        return $user->isAgente() || $user->isGestor();
    }

    /**
     * Gestor pode criar o primeiro local (primário). Demais locais: apenas agentes.
     */
    public function create(User $user): bool
    {
        if (Local::count() === 0) {
            return $user->isGestor();
        }
        return $user->isAgente();
    }

    /**
     * Apenas agentes podem editar. O local primário nunca pode ser editado pela UI.
     */
    public function update(User $user, Local $local): bool
    {
        if ($local->isPrimary()) {
            return false;
        }
        return $user->isAgente();
    }

    /**
     * Apenas agentes podem excluir. O local primário nunca pode ser excluído pela UI.
     */
    public function delete(User $user, Local $local): bool
    {
        if ($local->isPrimary()) {
            return false;
        }
        return $user->isAgente();
    }

    public function restore(User $user, Local $local): bool
    {
        return $user->isAgente();
    }

    public function forceDelete(User $user, Local $local): bool
    {
        return $user->isAgente();
    }
}
