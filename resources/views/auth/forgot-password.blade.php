<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
        {{ __('Esqueceu sua senha? Sem problemas. Informe seu e-mail e enviaremos um link para resetar sua senha.') }}
    </div>

    @if (session('status'))
        <div class="mb-4 p-4 rounded-lg bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800" role="alert">
            <p class="font-medium text-blue-800 dark:text-blue-200 flex items-center gap-2">
                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ session('status') }}
            </p>
            <p class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                {{ __('Não recebeu o e-mail? Verifique a pasta de spam ou solicite um novo link em alguns minutos.') }}
            </p>
        </div>
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

        <div class="flex items-center justify-end mt-4">
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
