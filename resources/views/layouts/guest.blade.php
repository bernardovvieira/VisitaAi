<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Visita Aí - Local - Sistema de Apoio à Vigilância Epidemiológica Municipal') }}</title>

        @php
            $authMeta = match (request()->route()?->getName()) {
                'login' => ['title' => config('app.name') . ' — Login', 'desc' => 'Acesse o sistema Visita Aí com CPF ou e-mail. Área de agentes e gestores.'],
                'register' => ['title' => config('app.name') . ' — Cadastro', 'desc' => 'Cadastre-se no Visita Aí para atuar como agente de endemias ou agente de saúde.'],
                'password.request' => ['title' => config('app.name') . ' — Esqueci a Senha', 'desc' => 'Recupere o acesso ao sistema Visita Aí pelo e-mail.'],
                'password.reset' => ['title' => config('app.name') . ' — Redefinir Senha', 'desc' => 'Defina uma nova senha para sua conta no Visita Aí.'],
                'verification.notice' => ['title' => config('app.name') . ' — Verificar E-mail', 'desc' => 'Confirme seu e-mail para ativar sua conta no Visita Aí.'],
                'pendente' => ['title' => config('app.name') . ' — Conta Pendente', 'desc' => 'Sua conta está aguardando aprovação do gestor municipal.'],
                default => ['title' => config('app.name') . ' — Acesso', 'desc' => 'Acesse o sistema Visita Aí. Apoio à vigilância epidemiológica municipal.'],
            };
            $ogImage = rtrim(config('app.url'), '/') . '/images/visitaai_rembg.png';
            $ogUrl = url()->current();
        @endphp
        <!-- Open Graph (Facebook, WhatsApp) -->
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ $ogUrl }}">
        <meta property="og:title" content="{{ $authMeta['title'] }}">
        <meta property="og:description" content="{{ $authMeta['desc'] }}">
        <meta property="og:image" content="{{ $ogImage }}">
        <meta property="og:site_name" content="Visita Aí">
        <!-- Twitter Card -->
        <meta name="twitter:card" content="summary">
        <meta name="twitter:title" content="{{ $authMeta['title'] }}">
        <meta name="twitter:description" content="{{ $authMeta['desc'] }}">
        <meta name="twitter:image" content="{{ $ogImage }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Página de login/registro: sempre modo claro (ignora localStorage) -->
        <script>
            (function(){
                document.documentElement.classList.remove('dark');
            })();
        </script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 dark:text-gray-100 antialiased bg-gray-100 dark:bg-gray-900">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900 px-4 sm:px-6">
            <div class="flex flex-col items-center gap-3">
                <a href="{{ url('/') }}">
                    <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
                </a>
                <a href="{{ url('/') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-blue-600 dark:text-blue-400 hover:underline">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                    Voltar para o início
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
        </div>
        <x-theme-toggle :floating="true" />
    </body>
</html>
