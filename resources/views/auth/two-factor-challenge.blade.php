<x-guest-layout>
    <div class="w-full max-w-md mx-auto mt-8 sm:mt-12 px-4">
        <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow">
            <h1 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-2">Código de autenticação</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                Digite o código de 6 dígitos gerado pelo seu aplicativo autenticador.
            </p>

            @if($errors->any())
                <div class="mb-4 p-3 rounded-md bg-red-600 text-white text-sm font-medium">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('two-factor.login.store') }}">
                @csrf
                <div class="mb-4">
                    <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Código</label>
                    <input type="text"
                           id="code"
                           name="code"
                           inputmode="numeric"
                           pattern="[0-9]*"
                           maxlength="6"
                           autocomplete="one-time-code"
                           placeholder="000000"
                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-green-500 focus:border-green-500"
                           required
                           autofocus>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('login') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-600 hover:bg-gray-300 dark:hover:bg-gray-500 rounded-md transition">
                        Voltar ao login
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md focus:ring-2 focus:ring-green-500 transition">
                        Verificar
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
