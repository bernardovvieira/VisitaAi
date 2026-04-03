<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} · Página não encontrada</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen items-center justify-center bg-gray-100 px-4 font-sans antialiased dark:bg-gray-900">
    <div class="w-full max-w-md text-center">
        <div class="mb-8">
            <span class="text-8xl font-black tabular-nums text-gray-200 dark:text-gray-700">404</span>
        </div>
        <div class="v-card p-6 text-center dark:border-slate-600 dark:bg-slate-800">
            <h1 class="text-xl font-bold text-gray-900 dark:text-gray-100">Página não encontrada</h1>
            <p class="mt-3 text-sm leading-relaxed text-gray-600 dark:text-gray-400">
                O endereço que você acessou não existe ou foi movido.
            </p>
            <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row">
                @auth
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center rounded-md bg-blue-600 px-4 py-2 text-[13px] font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500/40 dark:focus-visible:ring-offset-gray-800">
                        Ir para o painel
                    </a>
                @else
                    <a href="{{ url('/') }}" class="inline-flex items-center justify-center rounded-md bg-blue-600 px-4 py-2 text-[13px] font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500/40">
                        Voltar ao início
                    </a>
                @endauth
                <a href="javascript:history.back()" class="inline-flex items-center justify-center rounded-md border border-slate-300 bg-white px-4 py-2 text-[13px] font-semibold text-slate-800 shadow-sm transition hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500/35 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 dark:hover:bg-slate-700">
                    Voltar
                </a>
            </div>
        </div>
    </div>
</body>
</html>
