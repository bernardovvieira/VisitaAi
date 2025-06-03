<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Visita;

class VisitaPolicy
{
    /**
     * Agentes e gestores podem listar visitas.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAgente() || $user->isGestor();
    }

    /**
     * Agentes e gestores podem ver visitas específicas.
     */
    public function view(User $user, Visita $visita): bool
    {
        if ($user->isAgenteSaude()) {
            return $visita->fk_usuario_id === $user->use_id;
        }

        return $user->isGestor() || $user->isAgente(); // agentes epidemiológicos (não de saúde) podem ver tudo
    }

    /**
     * Apenas agentes podem registrar visitas.
     */
    public function create(User $user): bool
    {
        return $user->isAgente();
    }

    /**
     * Apenas agentes podem editar visitas.
     */
    public function update(User $user, Visita $visita): bool
    {
        return $user->isAgente();
    }

    /**
     * Apenas agentes podem excluir visitas.
     */
    public function delete(User $user, Visita $visita): bool
    {
        return $user->isAgente();
    }

    public function restore(User $user, Visita $visita): bool
    {
        return $user->isAgente();
    }

    public function forceDelete(User $user, Visita $visita): bool
    {
        return $user->isAgente();
    }
}
