<?php

namespace App\Policies;

use App\Models\User;

/**
 * Conformidade MS: apenas gestor municipal gerencia usuários (ACE/ACS) e aprovações (Diretriz Vigilância em Saúde).
 */
class UserPolicy
{
    public function viewAny(User $user)
    {
        return $user->use_perfil === 'gestor';
    }

    public function create(User $user)
    {
        return $user->use_perfil === 'gestor';
    }

    public function update(User $user, User $model)
    {
        return $user->use_perfil === 'gestor';
    }

    public function delete(User $user, User $model)
    {
        return $user->use_perfil === 'gestor';
    }
}
