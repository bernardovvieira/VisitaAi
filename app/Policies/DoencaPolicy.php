<?php

namespace App\Policies;

use App\Models\Doenca;
use App\Models\User;

/**
 * Conformidade MS: lista de doenças de notificação é de responsabilidade do gestor (Vigilância em Saúde).
 * ACE e ACS apenas consultam para registrar nas visitas.
 */
class DoencaPolicy
{
    /**
     * Gestor, ACE e ACS podem listar e visualizar doenças.
     */
    public function viewAny(User $user): bool
    {
        return $user->isGestor() || $user->isAgente();
    }

    /**
     * Gestor, ACE e ACS podem ver detalhes de uma doença.
     */
    public function view(User $user, Doenca $doenca): bool
    {
        return $user->isGestor() || $user->isAgente();
    }

    /**
     * Apenas gestores podem criar.
     */
    public function create(User $user): bool
    {
        return $user->isGestor();
    }

    /**
     * Apenas gestores podem editar.
     */
    public function update(User $user, Doenca $doenca): bool
    {
        return $user->isGestor();
    }

    /**
     * Apenas gestores podem excluir.
     */
    public function delete(User $user, Doenca $doenca): bool
    {
        return $user->isGestor();
    }

    /**
     * Restaurar modelos apagados — idem delete.
     */
    public function restore(User $user, Doenca $doenca): bool
    {
        return $user->isGestor();
    }

    /**
     * Exclusão permanente — idem delete.
     */
    public function forceDelete(User $user, Doenca $doenca): bool
    {
        return $user->isGestor();
    }
}