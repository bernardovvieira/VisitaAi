<?php

namespace App\Policies;

use App\Models\Local;
use App\Models\User;

/**
 * Imóveis/locais: gestor vê tudo e baixa anexos; ACE/ACS editam cadastro territorial (exceto primário na UI).
 */
class LocalPolicy
{
    /**
     * ACE, ACS e gestores podem listar e visualizar locais (inclui documentos de posse em modo leitura).
     */
    public function viewAny(User $user): bool
    {
        return $user->isAgente() || $user->isGestor();
    }

    public function view(User $user, Local $local): bool
    {
        return $user->isAgente() || $user->isGestor();
    }

    /**
     * Primeiro local (primário): apenas gestor. Demais locais: ACE ou ACS.
     */
    public function create(User $user): bool
    {
        if (Local::count() === 0) {
            return $user->isGestor();
        }

        return $user->isAgenteEndemias() || $user->isAgenteSaude();
    }

    /**
     * ACE ou ACS editam locais (exceto primário, que não é editável pela UI).
     */
    public function update(User $user, Local $local): bool
    {
        if ($local->isPrimary()) {
            return false;
        }

        return $user->isAgenteEndemias() || $user->isAgenteSaude();
    }

    /**
     * ACE ou ACS excluem locais (exceto primário).
     */
    public function delete(User $user, Local $local): bool
    {
        if ($local->isPrimary()) {
            return false;
        }

        return $user->isAgenteEndemias() || $user->isAgenteSaude();
    }

    public function restore(User $user, Local $local): bool
    {
        return $user->isAgenteEndemias() || $user->isAgenteSaude();
    }

    public function forceDelete(User $user, Local $local): bool
    {
        return $user->isAgenteEndemias() || $user->isAgenteSaude();
    }
}
