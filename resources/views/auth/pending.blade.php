<x-guest-layout>
    <div class="v-card mx-auto mt-6 max-w-md">
        <div class="flex flex-col items-center text-center">
            <div class="w-16 h-16 rounded-full bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h1 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-2">Cadastro enviado!</h1>
            <p class="text-gray-600 dark:text-gray-400 mb-2">
                Sua conta está aguardando aprovação de um gestor.
            </p>
            <p class="text-sm text-gray-500 dark:text-gray-500 mb-6">
                Você receberá um e-mail quando ela for liberada. Enquanto isso, você pode sair e voltar depois.
            </p>
            <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                <a href="{{ route('login') }}"
                   class="inline-flex items-center justify-center px-4 py-2 bg-gray-800 dark:bg-gray-200 text-white dark:text-gray-800 font-semibold rounded-md hover:bg-gray-700 dark:hover:bg-gray-300 transition">
                    Entrar
                </a>
                <form method="POST" action="{{ route('logout') }}" class="sm:inline">
                    @csrf
                    <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        Sair
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
