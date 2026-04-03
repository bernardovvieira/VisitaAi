<!-- resources/views/auth/reset-password.blade.php -->
<x-guest-layout>
    <div class="max-w-md mx-auto mt-8 rounded-xl border border-gray-200/80 bg-white dark:bg-gray-800 p-6 shadow-sm">
        <h1 class="text-2xl font-semibold mb-4 text-gray-900 dark:text-gray-100">
            Redefinir Senha
        </h1>

        <!-- Session Status (link enviado com sucesso) -->
        <x-auth-session-status class="mb-4 text-green-600 dark:text-green-400" :status="session('status')" />

        <form method="POST" action="{{ route('password.store') }}" id="reset-password-form">
            @csrf

            <!-- Token de Reset -->
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <!-- E‑mail -->
            <div class="mb-4">
                <x-input-label for="email">{{ __('E‑mail') }} <span class="text-red-500">*</span></x-input-label>
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
                <x-input-label for="password">{{ __('Senha') }} <span class="text-red-500">*</span></x-input-label>
                <x-text-input
                    id="password"
                    class="block mt-1 w-full"
                    type="password"
                    name="password"
                    required
                    autofocus
                    autocomplete="new-password"
                />
                <div class="mt-2 h-1.5 w-full rounded-full bg-gray-200 dark:bg-gray-600 overflow-hidden" role="presentation" aria-hidden="true">
                    <div id="password-strength-bar" class="h-full rounded-full bg-red-500 transition-all duration-300 ease-out" style="width: 0%"></div>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Mínimo 8 caracteres, com letras, números e pelo menos um caractere especial (ex.: @, #, $, !).</p>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Confirmar Senha -->
            <div class="mb-6">
                <x-input-label for="password_confirmation">{{ __('Confirmar Senha') }} <span class="text-red-500">*</span></x-input-label>
                <x-text-input
                    id="password_confirmation"
                    class="block mt-1 w-full"
                    type="password"
                    name="password_confirmation"
                    required
                    autocomplete="new-password"
                />
                <p id="password-match-feedback" class="mt-1 text-sm hidden" aria-live="polite"></p>
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <div class="flex items-center justify-end">
                <x-primary-button id="reset-password-submit-btn">
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

    <script>
    (function () {
        var bar = document.getElementById('password-strength-bar');
        var pwd = document.getElementById('password');
        if (bar && pwd) {
            function updateStrength() {
                var val = pwd.value || '';
                var n = [
                    val.length >= 8,
                    /[a-zA-Z]/.test(val),
                    /[a-z]/.test(val) && /[A-Z]/.test(val),
                    /\d/.test(val),
                    /[^a-zA-Z0-9]/.test(val)
                ].filter(Boolean).length;
                bar.style.width = (n * 20) + '%';
                bar.classList.remove('bg-red-500', 'bg-amber-500', 'bg-emerald-500');
                bar.classList.add(n >= 5 ? 'bg-emerald-500' : n >= 3 ? 'bg-amber-500' : 'bg-red-500');
            }
            pwd.addEventListener('input', updateStrength);
            pwd.addEventListener('change', updateStrength);
        }
        var conf = document.getElementById('password_confirmation');
        var matchFeedback = document.getElementById('password-match-feedback');
        if (conf && matchFeedback) {
            function updateMatch() {
                var p = (pwd && pwd.value) || '';
                var c = conf.value || '';
                matchFeedback.classList.add('hidden');
                if (c.length === 0) return;
                matchFeedback.classList.remove('hidden');
                if (p === c) {
                    matchFeedback.textContent = 'Senhas conferem.';
                    matchFeedback.className = 'mt-1 text-sm text-emerald-600 dark:text-emerald-400';
                } else {
                    matchFeedback.textContent = 'As senhas não conferem.';
                    matchFeedback.className = 'mt-1 text-sm text-red-600 dark:text-red-400';
                }
            }
            pwd && pwd.addEventListener('input', updateMatch);
            conf.addEventListener('input', updateMatch);
        }
        var form = document.getElementById('reset-password-form');
        var btn = document.getElementById('reset-password-submit-btn');
        if (form && btn) {
            form.addEventListener('submit', function () {
                btn.disabled = true;
                btn.innerHTML = '<span class="inline-flex items-center"><svg class="animate-spin -ml-1 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Redefinindo…</span>';
            });
        }
    })();
    </script>
</x-guest-layout>
