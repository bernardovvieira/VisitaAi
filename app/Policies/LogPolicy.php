<?php

// app/Policies/LogPolicy.php

namespace App\Policies;

use App\Models\User;
use App\Models\Log;

class LogPolicy
{
    /**
     * Permitir que apenas gestores visualizem os logs.
     */
    public function viewAny(User $user): bool
    {
        return $user->isGestor();
    }

    /**
     * Opcional: proteger ações individuais (não usadas neste caso).
     */
    public function view(User $user, Log $log): bool
    {
        return $user->isGestor();
    }
}