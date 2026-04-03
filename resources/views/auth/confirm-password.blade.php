@extends('layouts.app')

@section('content')
<div class="v-page space-y-5">
    <x-page-header title="Confirme sua senha" />

    <div class="v-card space-y-4">
        <h3 class="v-section-title flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
                <label for="password" class="v-toolbar-label">Senha <span class="text-red-500">*</span></label>
                <input id="password"
                       type="password"
                       name="password"
                       required
                       autocomplete="current-password"
                       class="v-input mt-1 max-w-xs">
                @error('password')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('dashboard') }}"
                   class="inline-flex items-center rounded-md border border-transparent bg-red-600 px-3 py-1.5 text-[13px] font-semibold text-white shadow-sm transition hover:bg-red-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500/40">
                    Voltar ao painel
                </a>
                <x-primary-button type="submit" id="confirm-password-submit-btn">Confirmar</x-primary-button>
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
