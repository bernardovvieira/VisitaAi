<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
        {{ __('Obrigado por se cadastrar! Antes de começar, verifique seu e-mail clicando no link que enviamos.') }}
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 p-3 rounded-lg bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 text-sm font-medium" role="alert">
            {{ __('Um novo link de verificação foi enviado para o e-mail informado.') }}
        </div>
    @endif

    <div class="flex items-center justify-between gap-4 flex-wrap">
        <form method="POST" action="{{ route('verification.send') }}" id="verify-email-resend-form">
            @csrf
            <x-primary-button id="verify-email-resend-btn">
                {{ __('Reenviar e-mail de verificação') }}
            </x-primary-button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white underline">
                {{ __('Sair') }}
            </button>
        </form>
    </div>
    <script>
    (function(){
        var form = document.getElementById('verify-email-resend-form');
        var btn = document.getElementById('verify-email-resend-btn');
        if (form && btn) form.addEventListener('submit', function(){ btn.disabled = true; btn.innerHTML = 'Enviando…'; });
    })();
    </script>
</x-guest-layout>
