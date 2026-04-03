<x-guest-layout>
    {{-- BANNER DE STATUS (logout ou link de reset enviado) --}}
    <x-alert type="success" :message="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="max-w-md mx-auto mt-8" id="login-form">
        @csrf

        {{-- CPF ou E-mail --}}
        <div class="mb-5">
            <x-input-label for="use_email">{{ __('CPF ou E-mail') }} <span class="text-red-500">*</span></x-input-label>
            <x-text-input id="use_email"
                        name="use_email"
                        type="text"
                        :value="old('use_email')"
                        required autofocus
                        class="block w-full mt-1 @error('use_email') border-red-500 @enderror"
                        autocomplete="username" />
            <x-input-error :messages="$errors->get('use_email')" />
            <div id="use_email_format_error" class="mt-1 text-sm text-red-600 dark:text-red-400 hidden" role="alert"></div>
        </div>

        {{-- Senha --}}
        <div class="mb-5" id="password-toggle-wrap">
            <x-input-label for="password">{{ __('Senha') }} <span class="text-red-500">*</span></x-input-label>

            <div class="relative mt-1">
                <input
                    type="password"
                    id="password"
                    name="password"
                    required
                    autocomplete="current-password"
                    class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-emerald-500 focus:ring-emerald-500 rounded-lg dark:focus:border-emerald-600 dark:focus:ring-emerald-600 shadow-sm block w-full pr-10 @error('password') border-red-500 @enderror"
                />

                <button
                    type="button"
                    id="password-toggle-btn"
                    aria-label="Mostrar senha"
                    class="absolute inset-y-0 right-0 px-3 flex items-center focus:outline-none"
                    tabindex="-1"
                >
                    {{-- Ícone: olho aberto (senha oculta) --}}
                    <svg id="icon-password-show" class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    {{-- Ícone: olho riscado (senha visível) --}}
                    <svg id="icon-password-hide" class="h-5 w-5 text-gray-500 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.27-2.944-9.543-7a10.05 10.05 0 012.166-3.823m1.43-1.43A9.966 9.966 0 0112 5c4.478 0 8.27 2.944 9.543 7a9.97 9.97 0 01-4.032 5.687M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18"/>
                    </svg>
                </button>
            </div>

            <x-input-error :messages="$errors->get('password')" />
        </div>

        {{-- Botões --}}
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mt-6">
            <a href="{{ route('password.request') }}"
            class="text-sm underline  text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                Esqueceu a senha?
            </a>
            <x-primary-button id="login-submit-btn" class="ml-3">Entrar</x-primary-button>
        </div>
    </form>

    <p class="mt-6 text-center text-sm text-gray-600 dark:text-gray-400">
        Não tem conta?
        <a href="{{ route('register') }}" class="text-emerald-600 dark:text-emerald-400 hover:underline font-medium">
            Registre‑se
        </a>
    </p>

    {{-- Toggle mostrar/ocultar senha (JS puro para evitar conflito com Alpine) --}}
    <script>
    (function(){
        var btn = document.getElementById('password-toggle-btn');
        var input = document.getElementById('password');
        var iconShow = document.getElementById('icon-password-show');
        var iconHide = document.getElementById('icon-password-hide');
        if (btn && input && iconShow && iconHide) {
            btn.addEventListener('click', function(){
                var isPassword = input.type === 'password';
                input.type = isPassword ? 'text' : 'password';
                iconShow.classList.toggle('hidden', isPassword);
                iconHide.classList.toggle('hidden', !isPassword);
                btn.setAttribute('aria-label', isPassword ? 'Ocultar senha' : 'Mostrar senha');
            });
        }
    })();
    </script>

    {{-- jQuery e Mask Plugin (CPF) --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <script>
    $(function(){
        var $input = $('#use_email');

        function toggleCpfMask(){
            var val = $input.val();
            var hasAt = val.indexOf('@') !== -1;
            var digits = val.replace(/\D/g, '');
            var startsWithDigit = /^\d/.test(val) || (digits.length > 0 && !hasAt);

            // Se contém @, é e-mail: remove máscara
            if (hasAt) {
                if ($input.data('mask-applied')) {
                    $input.unmask();
                    $input.data('mask-applied', false);
                }
                return;
            }
            // Se começa com número ou tem dígitos (CPF): aplica máscara desde o início
            if (startsWithDigit || digits.length > 0) {
                if (!$input.data('mask-applied')) {
                    $input.mask('000.000.000-00', { reverse: true });
                    $input.data('mask-applied', true);
                }
                // Se tem 11 dígitos sem formato (ex: colou), formata na hora
                if (digits.length === 11 && !/^\d{3}\.\d{3}\.\d{3}-\d{2}$/.test(val)) {
                    var fmt = digits.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
                    $input.val(fmt);
                }
            } else {
                if ($input.data('mask-applied')) {
                    $input.unmask();
                    $input.data('mask-applied', false);
                }
            }
        }

        $input
            .on('input', function(){
                toggleCpfMask();
                $('#use_email_format_error').addClass('hidden');
                $input.removeClass('border-red-500');
            })
            .on('paste', function(){
                setTimeout(toggleCpfMask, 0);
            })
            .on('focus', toggleCpfMask);

        toggleCpfMask();

        // Validação ao enviar: CPF com apenas números deve seguir o padrão
        $('#login-form').on('submit', function(e){
            var val = $input.val().trim();
            var digits = val.replace(/\D/g, '');
            var isEmail = val.indexOf('@') !== -1;
            var $err = $('#use_email_format_error');

            $err.addClass('hidden').text('');
            if (!isEmail && digits.length === 11 && !/^\d{3}\.\d{3}\.\d{3}-\d{2}$/.test(val)) {
                e.preventDefault();
                $err.text('O CPF deve estar no formato XXX.XXX.XXX-XX (com pontos e traço).').removeClass('hidden');
                $input.focus().addClass('border-red-500');
                return false;
            }
            // Loading no botão ao enviar
            var btn = document.getElementById('login-submit-btn');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<span class="inline-flex items-center"><svg class="animate-spin -ml-1 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Entrando…</span>';
            }
        });

        // Foco no primeiro campo com erro (ex.: credenciais inválidas)
        var form = document.getElementById('login-form');
        if (form) {
            var firstError = form.querySelector('.border-red-500');
            if (firstError && typeof firstError.focus === 'function') firstError.focus();
        }

        // Enter em qualquer campo: se CPF/e-mail e senha preenchidos, envia o formulário
        form.addEventListener('keydown', function(e) {
            if (e.key !== 'Enter') return;
            var emailVal = (document.getElementById('use_email') || {}).value || '';
            var passVal = (document.getElementById('password') || {}).value || '';
            if (emailVal.trim() && passVal) {
                e.preventDefault();
                form.requestSubmit();
            }
        });
    });
    </script>
    
</x-guest-layout>
