<x-guest-layout>
    {{-- BANNER DE STATUS (logout ou link de reset enviado) --}}
    <x-alert type="success" :message="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="max-w-md mx-auto mt-8">
        @csrf

        {{-- CPF ou E-mail --}}
        <div class="mb-5">
            <x-input-label for="login" :value="__('CPF ou E-mail')" />
            <x-text-input id="login"
                        name="login"
                        type="text"
                        :value="old('login')"
                        required autofocus
                        class="block w-full mt-1 @error('login') border-red-500 @enderror" />
            <x-input-error :messages="$errors->get('login')" />
        </div>

        {{-- Senha --}}
        <div x-data="{ show: false }" class="mb-5">
            <x-input-label for="password" :value="__('Senha')" />

            <div class="relative mt-1">
                <input
                    :type="show ? 'text' : 'password'"
                    id="password"
                    name="password"
                    required
                    autocomplete="current-password"
                    class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block w-full pr-10 @error('password') border-red-500 @enderror"
                />

                <button
                    type="button"
                    @click="show = !show"
                    class="absolute inset-y-0 right-0 px-3 flex items-center"
                    tabindex="-1"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        :class="show ? 'hidden' : 'block'"
                        class="h-5 w-5 text-gray-500"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        :class="show ? 'block' : 'hidden'"
                        class="h-5 w-5 text-gray-500"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.27-2.944-9.543-7a10.05 10.05 0 012.166-3.823m1.43-1.43A9.966 9.966 0 0112 5c4.478 0 8.27 2.944 9.543 7a9.97 9.97 0 01-4.032 5.687M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 3l18 18"/>
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
            <x-primary-button class="ml-3">Entrar</x-primary-button>
        </div>
    </form>

    <p class="mt-6 text-center text-sm text-gray-600 dark:text-gray-400">
        Não tem conta?
        <a href="{{ route('register') }}" class="underline text-blue-600 hover:text-blue-800">
            Registre‑se
        </a>
    </p>

    {{-- carregar jQuery e Mask Plugin --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <script>
    $(function(){
    var $login = $('#login');

    function toggleCpfMask(){
        // extrai apenas dígitos
        var digits = $login.val().replace(/\D/g, '');

        if (digits.length === 11) {
        if (!$login.data('mask-applied')) {
            // aplica máscara de CPF (reverse: true para facilitar a digitação)
            $login.mask('000.000.000-00', { reverse: true });
            $login.data('mask-applied', true);
        }
        } else {
        if ($login.data('mask-applied')) {
            // remove máscara sempre que não tiver 11 dígitos
            $login.unmask();
            $login.data('mask-applied', false);
        }
        }
    }

    $login
        .on('input', toggleCpfMask)
        .on('paste', function(){
        // aguarda o paste ser inserido no campo
        setTimeout(toggleCpfMask, 0);
        });

    // dispara ao carregar a página, caso já venha com valor
    toggleCpfMask();
    });
    </script>
    
</x-guest-layout>
