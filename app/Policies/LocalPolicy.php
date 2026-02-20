<?php

namespace App\Policies;

use App\Models\Local;
use App\Models\User;

/**
 * Conformidade MS: cadastro e gestão de imóveis/locais é atribuição do ACE (Diretriz Nacional ACE/ACS).
 * ACS não possui rotas de locais; apenas ACE e gestor (local primário) podem criar/editar/excluir.
 */
class LocalPolicy
{
    /**
     * ACE, ACS e gestores podem listar e visualizar locais (ACS precisa selecionar local na visita LIRAa).
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
     * Primeiro local (primário): apenas gestor. Demais locais: apenas ACE (Diretriz MS).
     */
    public function create(User $user): bool
    {
        if (Local::count() === 0) {
            return $user->isGestor();
        }
        return $user->isAgenteEndemias();
    }

    /**
     * Apenas ACE pode editar locais (exceto primário, que não é editável pela UI).
     */
    public function update(User $user, Local $local): bool
    {
        if ($local->isPrimary()) {
            return false;
        }
        return $user->isAgenteEndemias();
    }

    /**
     * Apenas ACE pode excluir locais (exceto primário).
     */
    public function delete(User $user, Local $local): bool
    {
        if ($local->isPrimary()) {
            return false;
        }
        return $user->isAgenteEndemias();
    }

    public function restore(User $user, Local $local): bool
    {
        return $user->isAgenteEndemias();
    }

    public function forceDelete(User $user, Local $local): bool
    {
        return $user->isAgenteEndemias();
    }
}
