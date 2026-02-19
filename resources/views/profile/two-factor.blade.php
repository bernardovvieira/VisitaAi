@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Ativar autenticação em dois fatores (2FA)</h1>

    @if($errors->any())
        <x-alert type="error" :message="implode(' ', $errors->all())" />
    @endif

    <div class="p-6 bg-white dark:bg-gray-700 rounded-lg shadow space-y-4">
        <h2 class="flex items-center text-lg font-semibold text-gray-800 dark:text-gray-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-amber-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
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
            <div class="flex flex-col-reverse sm:flex-row gap-3">
                <a href="{{ route('profile.edit') }}"
                   class="inline-flex justify-center items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-600 hover:bg-gray-300 dark:hover:bg-gray-500 rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Voltar ao perfil
                </a>
                <button type="submit" id="btn-enable-2fa" name="enable_2fa" value="1"
                        class="inline-flex justify-center items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-gray-600 hover:bg-gray-700 dark:bg-gray-600 dark:hover:bg-gray-500 rounded-lg shadow transition disabled:opacity-70">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
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
