<x-guest-layout>
    <h1 class="mb-1 text-2xl font-bold text-slate-900 dark:text-slate-100">
        {{ __('Redefinir Senha') }}
    </h1>
    <p class="mb-5 text-sm text-slate-600 dark:text-slate-400">
        {{ __('Defina uma nova senha forte para sua conta.') }}
    </p>

    <form method="POST" action="{{ route('password.store') }}" id="reset-password-form"
          data-msg-match-ok="{{ __('Senhas conferem.') }}"
          data-msg-match-bad="{{ __('As senhas não conferem.') }}"
          data-label-resetting="{{ __('Redefinindo…') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <x-form-field name="email" :label="__('E-mail')" :required="true" class="mb-4">
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
        </x-form-field>

        <x-form-field name="password" :label="__('Senha')" :required="true" class="mb-4">
            <x-text-input
                id="password"
                class="block mt-1 w-full"
                type="password"
                name="password"
                required
                autofocus
                autocomplete="new-password"
            />
            <div class="mt-2 h-1.5 w-full overflow-hidden rounded-full bg-gray-200 dark:bg-gray-600" role="presentation" aria-hidden="true">
                <div id="password-strength-bar" class="h-full rounded-full bg-red-500 transition-all duration-300 ease-out" style="width: 0%"></div>
            </div>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ __('Mínimo 8 caracteres, com letras, números e pelo menos um caractere especial (ex.: @, #, $, !).') }}</p>
        </x-form-field>

        <x-form-field name="password_confirmation" :label="__('Confirmar Senha')" :required="true" class="mb-6">
            <x-text-input
                id="password_confirmation"
                class="block mt-1 w-full"
                type="password"
                name="password_confirmation"
                required
                autocomplete="new-password"
            />
            <p id="password-match-feedback" class="mt-1 hidden text-sm" aria-live="polite"></p>
        </x-form-field>

        <div class="flex items-center justify-end">
            <x-primary-button id="reset-password-submit-btn">
                {{ __('Redefinir Senha') }}
            </x-primary-button>
        </div>
    </form>

    <p class="mt-6 text-center text-sm text-slate-600 dark:text-slate-400">
        {{ __('Já lembra da senha?') }}
        <a href="{{ route('login') }}" class="font-medium text-blue-700 underline hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500/35 rounded-sm">
            {{ __('Entrar') }}
        </a>
    </p>

    <script>
    (function () {
        var form = document.getElementById('reset-password-form');
        var msgOk = form && form.getAttribute('data-msg-match-ok');
        var msgBad = form && form.getAttribute('data-msg-match-bad');
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
                    matchFeedback.textContent = msgOk || '';
                    matchFeedback.className = 'mt-1 text-sm text-emerald-600 dark:text-emerald-400';
                } else {
                    matchFeedback.textContent = msgBad || '';
                    matchFeedback.className = 'mt-1 text-sm text-red-600 dark:text-red-400';
                }
            }
            pwd && pwd.addEventListener('input', updateMatch);
            conf.addEventListener('input', updateMatch);
        }
        var btn = document.getElementById('reset-password-submit-btn');
        if (form && btn) {
            form.addEventListener('submit', function () {
                btn.disabled = true;
                var resetLbl = form.getAttribute('data-label-resetting') || '';
                btn.innerHTML = '<span class="inline-flex items-center"><svg class="animate-spin -ml-1 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>' + resetLbl + '</span>';
            });
        }
    })();
    </script>
</x-guest-layout>
