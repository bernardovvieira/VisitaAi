<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} — Página não encontrada</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100 dark:bg-gray-900 min-h-screen flex items-center justify-center px-4">
    <div class="max-w-md w-full text-center">
        <div class="mb-6">
            <span class="text-8xl font-bold text-gray-300 dark:text-gray-600">404</span>
        </div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2">Página não encontrada</h1>
        <p class="text-gray-600 dark:text-gray-400 mb-8">
            O endereço que você acessou não existe ou foi movido.
        </p>
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            @auth
                <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center px-6 py-3 bg-gray-800 dark:bg-gray-200 text-white dark:text-gray-800 font-semibold rounded-lg shadow hover:bg-gray-700 dark:hover:bg-gray-300 transition">
                    Ir para o painel
                </a>
            @else
                <a href="{{ url('/') }}" class="inline-flex items-center justify-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg shadow hover:bg-blue-700 transition">
                    Voltar ao início
                </a>
            @endauth
            <a href="javascript:history.back()" class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                Voltar
            </a>
        </div>
    </div>
</body>
</html>
