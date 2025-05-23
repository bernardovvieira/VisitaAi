<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Monitorada;

class MonitoradaPolicy
{
    /**
     * Apenas agentes devem poder manipular diretamente se necessário.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAgente();
    }

    public function view(User $user, Monitorada $monitorada): bool
    {
        return $user->isAgente();
    }

    public function create(User $user): bool
    {
        return $user->isAgente();
    }

    public function update(User $user, Monitorada $monitorada): bool
    {
        return $user->isAgente();
    }

    public function delete(User $user, Monitorada $monitorada): bool
    {
        return $user->isAgente();
    }

    public function restore(User $user, Monitorada $monitorada): bool
    {
        return $user->isAgente();
    }

    public function forceDelete(User $user, Monitorada $monitorada): bool
    {
        return $user->isAgente();
    }
}
