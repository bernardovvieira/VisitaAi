<?php

namespace App\Policies;

use App\Models\Morador;
use App\Models\User;

/**
 * Ocupantes no imóvel (dados do Visita Aí).
 *
 * Perfil ACS não incluído: cadastro de pessoas no território para APS/PEC permanece no e-SUS;
 * assim evitamos fluxo paralelo conflitante com Ficha de Visita Domiciliar e Territorial.
 */
class MoradorPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isGestor() || $user->isAgenteEndemias();
    }

    public function view(User $user, Morador $morador): bool
    {
        return $user->isGestor() || $user->isAgenteEndemias();
    }

    public function create(User $user): bool
    {
        return $user->isAgenteEndemias();
    }

    public function update(User $user, Morador $morador): bool
    {
        return $user->isAgenteEndemias();
    }

    public function delete(User $user, Morador $morador): bool
    {
        return $user->isAgenteEndemias();
    }
}
