<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Visita;

/**
 * Conformidade MS/PNCD: apenas ACE e ACS registram visitas (ACS somente LIRAa — Diretriz ACE/ACS).
 * Gestor apenas visualiza e gera relatórios.
 */
class VisitaPolicy
{
    /**
     * ACE, ACS e gestores podem listar (controller filtra: ACS só as próprias e LIRAa).
     */
    public function viewAny(User $user): bool
    {
        return $user->isAgente() || $user->isGestor();
    }

    /**
     * ACS só vê as próprias visitas. ACE e gestor veem todas.
     */
    public function view(User $user, Visita $visita): bool
    {
        if ($user->isAgenteSaude()) {
            return $visita->fk_usuario_id === $user->use_id;
        }
        return $user->isGestor() || $user->isAgenteEndemias();
    }

    /**
     * Apenas ACE e ACS registram visitas (VisitaRequest restringe ACS a atividade 7 - LIRAa).
     */
    public function create(User $user): bool
    {
        return $user->isAgente();
    }

    /**
     * ACE pode editar qualquer visita. ACS apenas a própria (conformidade Diretriz MS).
     */
    public function update(User $user, Visita $visita): bool
    {
        if ($user->isAgenteSaude()) {
            return $visita->fk_usuario_id === $user->use_id;
        }
        return $user->isAgenteEndemias();
    }

    /**
     * ACE pode excluir qualquer visita. ACS apenas a própria.
     */
    public function delete(User $user, Visita $visita): bool
    {
        if ($user->isAgenteSaude()) {
            return $visita->fk_usuario_id === $user->use_id;
        }
        return $user->isAgenteEndemias();
    }

    public function restore(User $user, Visita $visita): bool
    {
        if ($user->isAgenteSaude()) {
            return $visita->fk_usuario_id === $user->use_id;
        }
        return $user->isAgenteEndemias();
    }

    public function forceDelete(User $user, Visita $visita): bool
    {
        if ($user->isAgenteSaude()) {
            return $visita->fk_usuario_id === $user->use_id;
        }
        return $user->isAgenteEndemias();
    }
}
