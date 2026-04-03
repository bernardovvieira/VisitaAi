@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Confirme sua senha</h1>

    <div class="rounded-xl border border-gray-200/80 bg-white p-6 shadow-sm dark:border-gray-600 dark:bg-gray-800 space-y-6">
        <h3 class="flex items-center text-lg font-semibold text-gray-800 dark:text-gray-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-amber-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
            Área segura do aplicativo
        </h3>
        <p class="text-gray-600 dark:text-gray-400">
            Por favor, digite sua senha para continuar.
        </p>

        <form method="POST" action="{{ route('password.confirm') }}" class="space-y-4" id="confirm-password-form">
            @csrf
            @if(request('return_action'))
                <input type="hidden" name="return_action" value="{{ request('return_action') }}">
            @endif
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Senha <span class="text-red-500">*</span></label>
                <input id="password"
                       type="password"
                       name="password"
                       required
                       autocomplete="current-password"
                       class="mt-1 block w-full max-w-xs rounded-lg border-gray-300 bg-white text-gray-900 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:focus:border-emerald-600 dark:focus:ring-emerald-600">
                @error('password')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('dashboard') }}"
                   class="inline-flex items-center px-4 py-2 text-sm font-semibold text-white bg-red-600 hover:bg-red-700 dark:bg-red-600 dark:hover:bg-red-700 rounded-md shadow transition">
                    Voltar ao painel
                </a>
                <button type="submit" id="confirm-password-submit-btn"
                        class="inline-flex items-center rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                    Confirmar
                </button>
            </div>
        </form>
    </div>
</div>
<script>
(function () {
    var form = document.getElementById('confirm-password-form');
    var btn = document.getElementById('confirm-password-submit-btn');
    if (form && btn) {
        form.addEventListener('submit', function () {
            btn.disabled = true;
            btn.innerHTML = '<span class="inline-flex items-center"><svg class="animate-spin -ml-1 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Confirmando…</span>';
        });
    }
})();
</script>
@endsection
