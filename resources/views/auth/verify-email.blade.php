<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-slate-100">Verificar e-mail</h1>
        <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">
            Confirme seu endereço de e-mail para concluir a ativação da conta.
        </p>
    </div>

    <div class="mb-4 text-sm text-slate-600 dark:text-slate-400">
        {{ __('Obrigado por se cadastrar! Antes de começar, verifique seu e-mail clicando no link que enviamos.') }}
    </div>

    @if (session('status') === 'verification-link-sent')
        <div class="v-alert v-alert--success mb-4" role="status">
            {{ __('Enviamos um novo link de verificação para o seu e-mail.') }}
        </div>
    @endif

    <div class="flex items-center justify-between gap-4 flex-wrap">
        <form method="POST" action="{{ route('verification.send') }}" id="verify-email-resend-form"
              data-label-sending="{{ __('Enviando…') }}">
            @csrf
            <x-primary-button id="verify-email-resend-btn">
                {{ __('Reenviar e-mail de verificação') }}
            </x-primary-button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-sm text-slate-700 underline hover:text-slate-900 dark:text-slate-300 dark:hover:text-slate-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500/35 rounded-sm">
                {{ __('Sair') }}
            </button>
        </form>
    </div>
    <script>
    (function(){
        var form = document.getElementById('verify-email-resend-form');
        var btn = document.getElementById('verify-email-resend-btn');
        if (form && btn) form.addEventListener('submit', function(){ btn.disabled = true; btn.textContent = form.getAttribute('data-label-sending') || '…'; });
    })();
    </script>
</x-guest-layout>
