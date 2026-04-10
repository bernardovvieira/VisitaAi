<x-guest-layout>
    <div class="flex flex-col items-center text-center">
        <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/40">
            <svg class="h-8 w-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <h1 class="mb-2 text-xl font-bold text-slate-900 dark:text-slate-100">{{ __('Cadastro enviado!') }}</h1>
        <p class="mb-2 text-slate-600 dark:text-slate-400">
            {{ __('Sua conta está aguardando aprovação de um gestor.') }}
        </p>
        <p class="mb-6 text-sm text-slate-500 dark:text-slate-400">
            {{ __('Você receberá um e-mail quando ela for liberada. Enquanto isso, você pode sair e voltar depois.') }}
        </p>
        <div class="flex w-full flex-col gap-3 sm:w-auto sm:flex-row">
            <a href="{{ route('login') }}"
               class="v-btn-primary inline-flex items-center justify-center px-4 py-2.5 text-[13px] font-semibold">
                {{ __('Entrar') }}
            </a>
            <form method="POST" action="{{ route('logout') }}" class="sm:inline">
                @csrf
                <button type="submit" class="v-btn-secondary inline-flex w-full items-center justify-center px-4 py-2.5 text-[13px] font-semibold sm:w-auto">
                    {{ __('Sair') }}
                </button>
            </form>
        </div>
    </div>
</x-guest-layout>
