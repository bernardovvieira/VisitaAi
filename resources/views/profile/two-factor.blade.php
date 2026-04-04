@extends('layouts.app')

@section('content')
<div class="v-page space-y-5">
    <x-page-header title="Ativar autenticação em dois fatores (2FA)" />

    @if($errors->any())
        <x-alert type="error" :message="implode(' ', $errors->all())" />
    @endif

    <div class="v-card space-y-4">
        <h2 class="v-section-title flex items-center gap-2">
            <x-heroicon-o-lock-closed class="h-5 w-5 shrink-0 text-amber-500" />
            O que você precisa saber
        </h2>
        <p class="text-gray-600 dark:text-gray-400">
            Ao ativar, você precisará de um aplicativo autenticador (Google Authenticator, Microsoft Authenticator ou similar) no celular para gerar códigos ao entrar.
        </p>
        <p class="text-gray-600 dark:text-gray-400">
            <strong class="text-gray-800 dark:text-gray-200">Por que ativar?</strong> A 2FA aumenta a segurança da sua conta, exigindo o código do celular além da senha no login.
        </p>

        <form id="form-enable-2fa" method="POST" action="{{ url(route('two-factor.enable')) }}" class="pt-2" accept-charset="UTF-8">
            @csrf
            <div class="flex gap-3">
                <button type="submit" id="btn-enable-2fa" name="enable_2fa" value="1"
                        class="v-btn-slate flex-1 !px-2.5 !py-1 !text-xs gap-1.5 disabled:opacity-70">
                    <x-heroicon-o-lock-closed class="h-3.5 w-3.5 shrink-0" />
                    Ativar 2FA
                </button>
            </div>
        </form>
        <script>
        (function(){
            var form = document.getElementById('form-enable-2fa');
            var btn = document.getElementById('btn-enable-2fa');
            if (form && btn) form.addEventListener('submit', function(){ btn.disabled = true; btn.innerHTML = '<svg class="animate-spin -ml-1 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Ativando…'; });
        })();
        </script>
    </div>
</div>
@endsection
