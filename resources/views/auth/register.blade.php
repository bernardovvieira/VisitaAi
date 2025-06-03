<x-guest-layout>
    <x-alert type="success" :message="session('status')" />

    <form method="POST" action="{{ route('register') }}" class="max-w-md mx-auto mt-8">
        @csrf

        {{-- Nome --}}
        <div class="mb-4">
            <x-input-label for="nome" :value="__('Nome')" />
            <x-text-input id="nome"
                        class="block mt-1 w-full @error('cpf') border-red-500 @enderror"
                        type="text"
                        name="nome"
                        :value="old('nome')"
                        required autofocus />
            <x-input-error :messages="$errors->get('nome')" class="mt-2" />
        </div>

        {{-- CPF --}}
        <div class="mb-4">
            <x-input-label for="cpf" :value="__('CPF')" />
            <x-text-input id="cpf"
                        class="block mt-1 w-full @error('use_cpf') border-red-500 @enderror"
                        type="text"
                        name="cpf"
                        :value="old('cpf')"
                        required
                        inputmode="numeric"
                        pattern="\d{3}\.\d{3}\.\d{3}-\d{2}"
                        title="Formato: XXX.XXX.XXX-XX" />
            <x-input-error :messages="$errors->get('cpf')" class="mt-2" />
        </div>

        {{-- E‑mail --}}
        <div class="mb-4">
            <x-input-label for="email" :value="__('E‑mail')" />
            <x-text-input id="email" name="email" type="email"
                          :value="old('email')" required autocapitalize="off"
                          class="block w-full mt-1
                                 @error('email') border-red-500 @enderror" />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        {{-- Senha --}}
        <div class="mb-4">
            <x-input-label for="password" :value="__('Senha')" />
            <x-text-input id="password" name="password" type="password"
                          required autocomplete="new-password"
                          class="block w-full mt-1
                                 @error('password') border-red-500 @enderror" />
            <x-input-error :messages="$errors->get('password')" />
        </div>

        {{-- Confirmar Senha --}}
        <div class="mb-6">
            <x-input-label for="password_confirmation" :value="__('Confirmar Senha')" />
            <x-text-input id="password_confirmation" name="password_confirmation"
                          type="password" required autocomplete="new-password"
                          class="block w-full mt-1
                                 @error('password_confirmation') border-red-500 @enderror" />
            <x-input-error :messages="$errors->get('password_confirmation')" />
        </div>

        <x-primary-button class="w-full justify-center">Registrar</x-primary-button>
    </form>

    <p class="mt-6 text-center text-sm text-gray-600 dark:text-gray-400">
        Já tem conta?
        <a href="{{ route('login') }}" class="underline text-blue-600 hover:text-blue-800">
            Faça login
        </a>
    </p>

    <!-- Máscara CPF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <script>
        $(function(){
            $('#cpf').mask('000.000.000-00');
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

    $('#cpf').on('blur', function () {
        const input = this;
        const cpf = input.value;

        if (!cpf) return; // ignora se estiver vazio (deixa o required tratar)

        if (!validarCPF(cpf)) {
            input.setCustomValidity('CPF inválido.');
            input.reportValidity();
        } else {
            input.setCustomValidity('');
        }
    });
    </script>

</x-guest-layout>
