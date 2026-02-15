<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Visita Aí - Local - Sistema de Apoio à Vigilância Epidemiológica Municipal') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Páginas públicas (home, consulta): padrão sempre modo claro quando não há preferência salva -->
        @if(View::hasSection('public'))
        <script>window.VisitaPublicPage = true;</script>
        @endif
        <!-- Preferência de tema do usuário (quando logado); ao criar conta o padrão é modo claro -->
        @auth
        <script>
            window.VisitaThemePreference = @json(auth()->user()->use_tema ?? 'light');
            window.VisitaThemeSyncUrl = @json(route('profile.tema.update'));
        </script>
        @endauth
        <!-- Tema claro/escuro: aplicado antes da pintura para evitar flash -->
        <script>
            (function(){
                var t;
                if (window.VisitaPublicPage) {
                    t = 'light';
                } else if (typeof window.VisitaThemePreference !== 'undefined' && window.VisitaThemePreference) {
                    t = window.VisitaThemePreference;
                    try { localStorage.setItem('theme', t); } catch (e) {}
                } else {
                    t = localStorage.getItem('theme');
                    if (!t) t = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
                }
                document.documentElement.classList.toggle('dark', t === 'dark');
            })();
        </script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            /* Botão principal azul – inline para garantir que sempre aplique (#3b82f6 / rgb(59,130,246)) */
            .btn-acesso-principal {
                background-color: #3b82f6 !important;
                color: #ffffff !important;
            }
            .btn-acesso-principal:hover {
                background-color: #2563eb !important;
                color: #ffffff !important;
            }
            .nav-link-active { border-color: #3b82f6 !important; }
            .dropdown-link-active { background-color: rgba(59, 130, 246, 0.1) !important; color: #1d4ed8 !important; border-left: 3px solid #3b82f6; font-weight: 600; }
            .dark .dropdown-link-active { background-color: rgba(59, 130, 246, 0.2) !important; color: #93c5fd !important; }
            .responsive-nav-link-active { border-color: #3b82f6 !important; color: #1d4ed8 !important; background-color: rgba(59, 130, 246, 0.1) !important; }
            .dark .responsive-nav-link-active { color: #93c5fd !important; background-color: rgba(59, 130, 246, 0.2) !important; }
        </style>
    </head>
    <body class="font-sans antialiased {{ View::hasSection('public') ? 'bg-white' : 'bg-gray-100' }} dark:bg-gray-900">
        <div class="min-h-screen {{ View::hasSection('public') ? 'bg-white' : 'bg-gray-100' }} dark:bg-gray-900">
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
        {{-- Toggle flutuante apenas em páginas públicas (home, consulta); nas restritas fica só no navbar --}}
        @if(View::hasSection('public'))
        <x-theme-toggle :floating="true" />
        @endif
        {{-- Aviso de cookies: apenas em páginas públicas (home, consulta) --}}
        @if(View::hasSection('public'))
            <x-cookie-banner />
        @endif
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
