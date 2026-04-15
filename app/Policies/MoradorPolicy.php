<?php

namespace App\Policies;

use App\Models\Morador;
use App\Models\User;

/**
 * Ocupantes no imóvel (dados do Visita Aí): gestor, ACE e ACS no fluxo de cadastro territorial.
 */
class MoradorPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isGestor() || $user->isAgenteEndemias() || $user->isAgenteSaude();
    }

    public function view(User $user, Morador $morador): bool
    {
        return $user->isGestor() || $user->isAgenteEndemias() || $user->isAgenteSaude();
    }

    public function create(User $user): bool
    {
        return $user->isAgenteEndemias() || $user->isAgenteSaude();
    }

    public function update(User $user, Morador $morador): bool
    {
        return $user->isAgenteEndemias() || $user->isAgenteSaude();
    }

    public function delete(User $user, Morador $morador): bool
    {
        return $user->isAgenteEndemias() || $user->isAgenteSaude();
    }
}
