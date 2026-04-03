<x-guest-layout>
    <div class="w-full max-w-md mx-auto mt-8 sm:mt-12 px-4">
        <div class="v-card">
            <h1 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-2">Código de autenticação</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                Digite o código de 6 dígitos gerado pelo seu aplicativo autenticador.
            </p>

            @if($errors->any())
                <div class="mb-4 p-3 rounded-md bg-red-100 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-200 text-sm font-medium" role="alert">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('two-factor.login.store') }}" id="two-factor-form">
                @csrf
                <div class="mb-4">
                    <label for="code" class="v-toolbar-label">Código <span class="text-red-500">*</span></label>
                    <input type="text"
                           id="code"
                           name="code"
                           inputmode="numeric"
                           pattern="[0-9]*"
                           maxlength="6"
                           autocomplete="one-time-code"
                           placeholder="000000"
                           class="v-input mt-1"
                           required
                           autofocus>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('login') }}" class="inline-flex items-center rounded-md border border-slate-300 bg-white px-3 py-1.5 text-[13px] font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
                        Voltar ao login
                    </a>
                    <x-primary-button type="submit" id="two-factor-submit-btn">Verificar</x-primary-button>
                </div>
            </form>
            <script>
            (function(){
                var form = document.getElementById('two-factor-form');
                var btn = document.getElementById('two-factor-submit-btn');
                if (form && btn) form.addEventListener('submit', function(){ btn.disabled = true; btn.innerHTML = '<span class="inline-flex items-center"><svg class="animate-spin -ml-1 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Verificando…</span>'; });
            })();
            </script>
        </div>
    </div>
</x-guest-layout>
