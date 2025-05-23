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
     * Apenas agentes podem criar locais.
     */
    public function create(User $user): bool
    {
        return $user->isAgente();
    }

    /**
     * Apenas agentes podem editar locais.
     */
    public function update(User $user, Local $local): bool
    {
        return $user->isAgente();
    }

    /**
     * Apenas agentes podem excluir locais (se aplicável).
     */
    public function delete(User $user, Local $local): bool
    {
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
