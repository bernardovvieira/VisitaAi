<x-guest-layout>
    <x-alert type="success" :message="session('status')" />

    @if ($errors->any())
        @php
            $fieldLabels = ['nome' => 'Nome', 'cpf' => 'CPF', 'email' => 'E-mail', 'password' => 'Senha', 'password_confirmation' => 'Confirmar senha'];
            $errorFields = array_unique($errors->keys());
            $labels = array_map(fn ($k) => $fieldLabels[$k] ?? $k, $errorFields);
        @endphp
        <div class="max-w-md mx-auto mt-4 px-4 py-3 rounded-lg bg-red-600 dark:bg-red-700 text-white text-sm" role="alert">
            <p class="font-medium">Corrija os erros nos campos indicados abaixo.</p>
            @if (count($labels) > 0)
                <p class="mt-1 opacity-90">Campos com erro: {{ implode(', ', $labels) }}.</p>
            @endif
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}" class="max-w-md mx-auto mt-8" id="register-form">
        @csrf

        {{-- Nome --}}
        <div class="mb-4">
            <x-input-label for="nome">{{ __('Nome') }} <span class="text-red-500">*</span></x-input-label>
            <x-text-input id="nome"
                        class="block mt-1 w-full @error('nome') border-red-500 dark:border-red-400 @enderror"
                        type="text"
                        name="nome"
                        :value="old('nome')"
                        required autofocus />
            <x-input-error :messages="$errors->get('nome')" class="mt-2" />
        </div>

        {{-- CPF --}}
        <div class="mb-4">
            <x-input-label for="cpf">{{ __('CPF') }} <span class="text-red-500">*</span></x-input-label>
            <x-text-input id="cpf"
                        class="block mt-1 w-full @error('cpf') border-red-500 dark:border-red-400 @enderror"
                        type="text"
                        name="cpf"
                        :value="old('cpf')"
                        required
                        inputmode="numeric"
                        pattern="\d{3}\.\d{3}\.\d{3}-\d{2}"
                        title="Formato: XXX.XXX.XXX-XX" />
            <p id="cpf-feedback" class="mt-1 text-sm hidden" aria-live="polite"></p>
            <x-input-error :messages="$errors->get('cpf')" class="mt-2" />
        </div>

        {{-- E-mail --}}
        <div class="mb-4">
            <x-input-label for="email">{{ __('E-mail') }} <span class="text-red-500">*</span></x-input-label>
            <x-text-input id="email" name="email" type="email"
                          :value="old('email')" required autocapitalize="off"
                          class="block w-full mt-1
                                 @error('email') border-red-500 dark:border-red-400 @enderror" />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        {{-- Senha --}}
        <div class="mb-4">
            <x-input-label for="password">{{ __('Senha') }} <span class="text-red-500">*</span></x-input-label>
            <x-text-input id="password" name="password" type="password"
                          required autocomplete="new-password"
                          class="block w-full mt-1
                                 @error('password') border-red-500 dark:border-red-400 @enderror" />
            {{-- Barra: vermelha ao digitar, amarela no meio, verde quando todos os requisitos ok --}}
            <div class="mt-2 h-1.5 w-full rounded-full bg-gray-200 dark:bg-gray-600 overflow-hidden" role="presentation" aria-hidden="true">
                <div id="password-strength-bar" class="h-full rounded-full bg-red-500 transition-all duration-300 ease-out" style="width: 0%"></div>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Mínimo 8 caracteres, com letras, números e pelo menos um caractere especial (ex.: @, #, $, !).</p>
            <x-input-error :messages="$errors->get('password')" />
        </div>

        {{-- Confirmar Senha --}}
        <div class="mb-6">
            <x-input-label for="password_confirmation">{{ __('Confirmar Senha') }} <span class="text-red-500">*</span></x-input-label>
            <x-text-input id="password_confirmation" name="password_confirmation"
                          type="password" required autocomplete="new-password"
                          class="block w-full mt-1
                                 @error('password_confirmation') border-red-500 dark:border-red-400 @enderror" />
            <p id="password-match-feedback" class="mt-1 text-sm hidden" aria-live="polite"></p>
            <x-input-error :messages="$errors->get('password_confirmation')" />
        </div>

        <x-primary-button id="register-submit-btn" class="w-full justify-center">Registrar</x-primary-button>
    </form>

    <p class="mt-6 text-center text-sm text-gray-600 dark:text-gray-400">
        Já tem conta?
        <a href="{{ route('login') }}" class="text-blue-600 dark:text-blue-400 hover:underline font-medium">
            Faça login
        </a>
    </p>

    <!-- Máscara CPF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <script>
        $(function(){
            $('#cpf').mask('000.000.000-00');

            // Barra de força da senha: vermelha → amarela → verde (mesmas regras do backend)
            var $bar = document.getElementById('password-strength-bar');
            var $pwd = document.getElementById('password');
            if ($bar && $pwd) {
                function updatePasswordStrength() {
                    var pwd = ($pwd.value || '');
                    var minLen = pwd.length >= 8;
                    var hasLetter = /[a-zA-Z]/.test(pwd);
                    var hasMixed = /[a-z]/.test(pwd) && /[A-Z]/.test(pwd);
                    var hasNumber = /\d/.test(pwd);
                    var hasSymbol = /[^a-zA-Z0-9]/.test(pwd);
                    var n = [minLen, hasLetter, hasMixed, hasNumber, hasSymbol].filter(Boolean).length;
                    $bar.style.width = (n * 20) + '%';
                    // 0 a 2 requisitos: vermelho; 3 a 4: amarelo; 5: verde
                    $bar.classList.remove('bg-red-500', 'bg-amber-500', 'bg-blue-500');
                    $bar.classList.add(n >= 5 ? 'bg-blue-500' : n >= 3 ? 'bg-amber-500' : 'bg-red-500');
                }
                $pwd.addEventListener('input', updatePasswordStrength);
                $pwd.addEventListener('change', updatePasswordStrength);
            }

            // Confirmar senha em tempo real
            var pwdConf = document.getElementById('password_confirmation');
            var matchFeedback = document.getElementById('password-match-feedback');
            if (pwdConf && matchFeedback) {
                function updateMatchFeedback() {
                    var p = ($('#password').val() || '');
                    var c = (pwdConf.value || '');
                    matchFeedback.classList.add('hidden');
                    if (c.length === 0) return;
                    if (p === c) {
                        matchFeedback.textContent = 'Senhas conferem.';
                        matchFeedback.classList.remove('text-red-600', 'dark:text-red-400');
                        matchFeedback.classList.add('text-blue-600', 'dark:text-blue-400');
                        matchFeedback.classList.remove('hidden');
                    } else {
                        matchFeedback.textContent = 'As senhas não conferem.';
                        matchFeedback.classList.remove('text-blue-600', 'dark:text-blue-400');
                        matchFeedback.classList.add('text-red-600', 'dark:text-red-400');
                        matchFeedback.classList.remove('hidden');
                    }
                }
                $('#password').on('input', updateMatchFeedback);
                pwdConf.addEventListener('input', updateMatchFeedback);
            }

            // Loading no botão ao enviar
            var form = document.getElementById('register-form');
            var btn = document.getElementById('register-submit-btn');
            if (form && btn) {
                form.addEventListener('submit', function () {
                    btn.disabled = true;
                    btn.innerHTML = '<span class="inline-flex items-center"><svg class="animate-spin -ml-1 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Registrando…</span>';
                });
            }
        });

            function validarCPF(cpf) {
        cpf = cpf.replace(/[^\d]+/g, '');

        if (cpf.length !== 11 || /^(\d)\1+$/.test(cpf)) return false;

        let soma = 0;
        for (let i = 0; i < 9; i++) soma += parseInt(cpf.charAt(i)) * (10 - i);
        let resto = (soma * 10) % 11;
        if (resto === 10 || resto === 11) resto = 0;
        if (resto !== parseInt(cpf.charAt(9))) return false;

        soma = 0;
        for (let i = 0; i < 10; i++) soma += parseInt(cpf.charAt(i)) * (11 - i);
        resto = (soma * 10) % 11;
        if (resto === 10 || resto === 11) resto = 0;
        return resto === parseInt(cpf.charAt(10));
    }

    var $cpfFeedback = $('#cpf-feedback');
    $('#cpf').on('blur', function () {
        const input = this;
        const cpf = input.value;
        $cpfFeedback.addClass('hidden').text('');

        if (!cpf) return; // ignora se estiver vazio (deixa o required tratar)

        if (!validarCPF(cpf)) {
            input.setCustomValidity('CPF inválido.');
            input.reportValidity();
            $cpfFeedback.removeClass('hidden text-blue-600 dark:text-blue-400').addClass('text-red-600 dark:text-red-400').text('CPF inválido.');
        } else {
            input.setCustomValidity('');
            $cpfFeedback.removeClass('hidden text-red-600 dark:text-red-400').addClass('text-blue-600 dark:text-blue-400').text('CPF válido.');
        }
    });
    </script>

</x-guest-layout>
