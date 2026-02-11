<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'login'    => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'max:255'],
        ];
    }

    /**
     * Autentica o usuário por CPF **ou** e‑mail,
     * verifica aprovação do gestor e trata mensagens de erro.
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $login    = $this->input('login');
        $password = $this->input('password');

        /* -----------------------------------------------------------------
         | 1. Descobre se login é e‑mail ou CPF
         |-----------------------------------------------------------------*/
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'use_email' : 'use_cpf';

        /* -----------------------------------------------------------------
         | 2. Procura usuário
         |-----------------------------------------------------------------*/
        $user = User::where($field, $login)->first();

        if (! $user) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'login' => 'Usuário não encontrado.',
            ]);
        }

        /* -----------------------------------------------------------------
         | 3. Conta precisa estar aprovada
         |-----------------------------------------------------------------*/
        if (! $user->isAprovado()) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'login' => 'Conta ainda não aprovada por um gestor.',
            ]);
        }

        /* -----------------------------------------------------------------
         | 4. Verifica senha
         |-----------------------------------------------------------------*/
        if (! Hash::check($password, $user->use_senha)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'password' => 'Senha incorreta.',
            ]);
        }

        /* -----------------------------------------------------------------
         | 5. Tudo certo → efetua login
         |-----------------------------------------------------------------*/
        Auth::login($user, $this->boolean('remember'));

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Garante que o request não estoure o limite de tentativas.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 3)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'login' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Chave usada pelo RateLimiter.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->input('login')).'|'.$this->ip());
    }
}