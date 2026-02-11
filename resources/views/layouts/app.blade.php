<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Visita Aí - Sistema de Visitas Epidemiológicas') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Tema claro -->
        <meta name="theme-color" content="#ffffff" media="(prefers-color-scheme: light)">
        <!-- Tema escuro -->
        <meta name="theme-color" content="#111827" media="(prefers-color-scheme: dark)">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            @if (! View::hasSection('public'))
                @include('layouts.navigation')
            @endif

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content (mesmo alinhamento e margens do menu) -->
            <main>
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                    @yield('content')
                </div>
            </main>
        </div>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('input[data-live-url]').forEach(function(input) {
                var url = input.getAttribute('data-live-url');
                var param = input.getAttribute('data-live-param') || 'search';
                var timer;
                input.addEventListener('input', function() {
                    clearTimeout(timer);
                    timer = setTimeout(function() {
                        var val = (input.value || '').trim();
                        var target = val ? url + (url.indexOf('?') >= 0 ? '&' : '?') + param + '=' + encodeURIComponent(val) : url;
                        if (target !== (window.location.pathname + window.location.search)) window.location.href = target;
                    }, 380);
                });
            });
        });
        </script>
    </body>
</html>
