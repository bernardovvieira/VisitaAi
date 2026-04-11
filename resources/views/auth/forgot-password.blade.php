<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-slate-100">Recuperar acesso</h1>
        <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">
            Informe o e-mail da conta para receber um link de redefinição de senha.
        </p>
    </div>

    <div class="mb-4 text-sm text-slate-600 dark:text-slate-400">
        {{ __('Esqueceu sua senha? Sem problemas. Informe seu e-mail e enviaremos um link para resetar sua senha.') }}
    </div>

    @if (session('status'))
        <p class="mb-4 text-sm text-slate-600 dark:text-slate-400">
            {{ __('Não recebeu o e-mail? Verifique a pasta de spam ou solicite um novo link em alguns minutos.') }}
        </p>
    @endif

    <form method="POST" action="{{ route('password.email') }}" id="forgot-password-form" data-label-sending="{{ __('Enviando…') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email">{{ __('E-mail') }} <span class="text-red-500">*</span></x-input-label>
            <x-text-input
                id="email"
                class="block mt-1 w-full"
                type="email"
                name="email"
                :value="old('email')"
                required
                autofocus
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-5 flex items-center justify-between gap-3">
            <a href="{{ route('login') }}" class="text-sm font-medium text-slate-700 underline hover:text-slate-900 dark:text-slate-300 dark:hover:text-slate-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500/35 rounded-sm">
                {{ __('Voltar ao login') }}
            </a>
            <x-primary-button id="forgot-password-submit-btn">
                {{ __('Enviar link de reset') }}
            </x-primary-button>
        </div>
    </form>

    <script>
    (function () {
        var form = document.getElementById('forgot-password-form');
        var btn = document.getElementById('forgot-password-submit-btn');
        if (form && btn) {
            form.addEventListener('submit', function () {
                btn.disabled = true;
                var sendLbl = form.getAttribute('data-label-sending') || '';
                btn.innerHTML = '<span class="inline-flex items-center"><svg class="animate-spin -ml-1 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>' + sendLbl + '</span>';
            });
        }
    })();
    </script>
</x-guest-layout>
