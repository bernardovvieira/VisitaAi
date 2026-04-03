<x-guest-layout>
    <div class="w-full max-w-md mx-auto mt-8 sm:mt-12 px-4">
        <div class="rounded-xl border border-gray-200/80 bg-white dark:bg-gray-800 p-6 shadow-sm">
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
                    <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Código <span class="text-red-500">*</span></label>
                    <input type="text"
                           id="code"
                           name="code"
                           inputmode="numeric"
                           pattern="[0-9]*"
                           maxlength="6"
                           autocomplete="one-time-code"
                           placeholder="000000"
                           class="mt-1 block w-full rounded-lg border-gray-300 bg-white text-gray-900 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 dark:focus:border-emerald-600 dark:focus:ring-emerald-600"
                           required
                           autofocus>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('login') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-600 hover:bg-gray-300 dark:hover:bg-gray-500 rounded-md transition">
                        Voltar ao login
                    </a>
                    <button type="submit" id="two-factor-submit-btn" class="inline-flex items-center rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                        Verificar
                    </button>
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
