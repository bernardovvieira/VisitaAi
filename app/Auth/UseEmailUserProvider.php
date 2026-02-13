<?php

namespace App\Auth;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;

/**
 * Provider que usa use_email quando a credencial vier como 'login' (Fortify/confirm-password).
 */
class UseEmailUserProvider extends EloquentUserProvider
{
    public function retrieveByCredentials(array $credentials)
    {
        if (isset($credentials['login'])) {
            $credentials['use_email'] = $credentials['login'];
            unset($credentials['login']);
        }

        return parent::retrieveByCredentials($credentials);
    }
}
