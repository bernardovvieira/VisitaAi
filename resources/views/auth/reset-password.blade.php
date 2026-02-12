<!-- resources/views/auth/reset-password.blade.php -->
<x-guest-layout>
    <div class="max-w-md mx-auto mt-8 p-6 bg-white dark:bg-gray-800 rounded-lg shadow">
        <h1 class="text-2xl font-semibold mb-4 text-gray-900 dark:text-gray-100">
            Redefinir Senha
        </h1>

        <!-- Session Status (link enviado com sucesso) -->
        <x-auth-session-status class="mb-4 text-green-600 dark:text-green-400" :status="session('status')" />

        <form method="POST" action="{{ route('password.store') }}">
            @csrf

            <!-- Token de Reset -->
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <!-- E‑mail -->
            <div class="mb-4">
                <x-input-label for="email" :value="__('E‑mail')" />
                <x-text-input
                    id="email"
                    class="block mt-1 w-full"
                    type="email"
                    name="email"
                    :value="old('email', $request->email)"
                    required
                    readonly
                    autocomplete="username"

                />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Senha Nova -->
            <div class="mb-4">
                <x-input-label for="password" :value="__('Senha')" />
                <x-text-input
                    id="password"
                    class="block mt-1 w-full"
                    type="password"
                    name="password"
                    required
                    autofocus
                    autocomplete="new-password"
                />
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Mínimo 8 caracteres, com letras, números e pelo menos um caractere especial (ex.: @, #, $, !).</p>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Confirmar Senha -->
            <div class="mb-6">
                <x-input-label for="password_confirmation" :value="__('Confirmar Senha')" />
                <x-text-input
                    id="password_confirmation"
                    class="block mt-1 w-full"
                    type="password"
                    name="password_confirmation"
                    required
                    autocomplete="new-password"
                />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <div class="flex items-center justify-end">
                <x-primary-button>
                    {{ __('Redefinir Senha') }}
                </x-primary-button>
            </div>
        </form>

        <p class="mt-6 text-center text-sm text-gray-600 dark:text-gray-400">
            Já lembra da senha?
            <a href="{{ route('login') }}" class="underline text-blue-600 hover:text-blue-800 dark:text-blue-400">
                Entrar
            </a>
        </p>
    </div>
</x-guest-layout>
