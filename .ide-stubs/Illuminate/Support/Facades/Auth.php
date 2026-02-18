<?php

namespace Illuminate\Support\Facades;

/**
 * @method static \Illuminate\Contracts\Auth\Guard|\Illuminate\Contracts\Auth\StatefulGuard guard(string|null $name = null)
 * @method static void setGuards(array $guards)
 * @method static \Illuminate\Contracts\Auth\Authenticatable|null user()
 * @method static bool check()
 * @method static bool guest()
 * @method static bool id()
 * @method static bool validate(array $credentials = [])
 * @method static void setUser(\Illuminate\Contracts\Auth\Authenticatable $user)
 * @method static bool attempt(array $credentials = [], bool $remember = false)
 * @method static bool once(array $credentials = [])
 * @method static void login(\Illuminate\Contracts\Auth\Authenticatable $user, bool $remember = false)
 * @method static \Illuminate\Contracts\Auth\Authenticatable|null loginUsingId(mixed $id, bool $remember = false)
 * @method static bool onceUsingId(mixed $id)
 * @method static void logout()
 * @method static void logoutCurrentDevice()
 * @method static void invalidate()
 * @method static void regenerate(bool $destroy = false)
 *
 * @see \Illuminate\Auth\AuthManager
 * @see \Illuminate\Contracts\Auth\Guard
 * @see \Illuminate\Contracts\Auth\StatefulGuard
 */
class Auth extends Facade
{
    /** @return \Illuminate\Contracts\Auth\Authenticatable|\App\Models\User|null */
    public static function user()
    {
        return null;
    }

    protected static function getFacadeAccessor(): string
    {
        return 'auth';
    }
}
