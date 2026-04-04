<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        @php
            $routeName = request()->route()?->getName();
            $authMeta = match (true) {
                in_array($routeName, ['login', 'login.store'], true) => ['title' => config('app.name') . ' · Login', 'desc' => 'Acesse o sistema Visita Aí com CPF ou e-mail. Área de gestores, ACE e ACS.'],
                $routeName === 'register' => ['title' => config('app.name') . ' · Cadastro', 'desc' => 'Cadastre-se no Visita Aí para atuar como ACE (Agente de Combate às Endemias) ou ACS (Agente Comunitário de Saúde), conforme Lei 11.350/2006.'],
                $routeName === 'password.request' => ['title' => config('app.name') . ' · Esqueci a Senha', 'desc' => 'Recupere o acesso ao sistema Visita Aí pelo e-mail.'],
                $routeName === 'password.reset' => ['title' => config('app.name') . ' · Redefinir Senha', 'desc' => 'Defina uma nova senha para sua conta no Visita Aí.'],
                $routeName === 'verification.notice' => ['title' => config('app.name') . ' · Verificar E-mail', 'desc' => 'Confirme seu e-mail para ativar sua conta no Visita Aí.'],
                $routeName === 'pendente' => ['title' => config('app.name') . ' · Conta Pendente', 'desc' => 'Sua conta está aguardando aprovação do gestor municipal.'],
                default => ['title' => config('app.name') . ' · Acesso', 'desc' => 'Acesse o sistema Visita Aí. Apoio à vigilância entomológica e controle vetorial municipal.'],
            };
            $ogImage = rtrim(config('app.url'), '/') . '/images/visitaai.png';
            $ogUrl = url()->current();
            $isHttps = str_starts_with(config('app.url'), 'https');
        @endphp

        <title>{{ $authMeta['title'] }}</title>

        <!-- Open Graph (Facebook, WhatsApp) -->
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ $ogUrl }}">
        <meta property="og:title" content="{{ $authMeta['title'] }}">
        <meta property="og:description" content="{{ $authMeta['desc'] }}">
        <meta property="og:image" content="{{ $ogImage }}">
        @if($isHttps)
        <meta property="og:image:secure_url" content="{{ $ogImage }}">
        @endif
        <meta property="og:image:width" content="1145">
        <meta property="og:image:height" content="722">
        <meta property="og:image:type" content="image/png">
        <meta property="og:site_name" content="Visita Aí">
        <!-- Twitter Card -->
        <meta name="twitter:card" content="summary">
        <meta name="twitter:title" content="{{ $authMeta['title'] }}">
        <meta name="twitter:description" content="{{ $authMeta['desc'] }}">
        <meta name="twitter:image" content="{{ $ogImage }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Tema: respeita localStorage (igual às páginas públicas); fallback preferência do sistema ou claro -->
        <script>
            (function(){
                var t = null;
                try { t = localStorage.getItem('theme'); } catch (e) {}
                if (t !== 'dark' && t !== 'light') {
                    t = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
                }
                document.documentElement.classList.toggle('dark', t === 'dark');
            })();
        </script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased dark:text-gray-100">
        <a href="#guest-main" class="fixed left-4 top-0 z-[200] -translate-y-full rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg outline-none ring-2 ring-blue-400 ring-offset-2 transition-transform focus:translate-y-4 dark:ring-offset-slate-900">{{ __('Ir para o conteúdo') }}</a>
        <div class="flex min-h-screen flex-col items-center bg-gradient-to-br from-slate-100 via-white to-blue-50/50 px-4 pt-6 sm:justify-center sm:px-6 sm:pt-0 dark:from-slate-950 dark:via-slate-900 dark:to-slate-950">
            <div class="flex flex-col items-center gap-3">
                <x-application-logo />
                <a href="{{ url('/') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-blue-600 hover:underline dark:text-blue-400">
                    <x-heroicon-o-arrow-left class="h-4 w-4 shrink-0" />
                    {{ __('Voltar para o início') }}
                </a>
            </div>

            <main id="guest-main" tabindex="-1" class="mt-8 w-full max-w-md overflow-hidden rounded-2xl border border-slate-200/90 bg-white/80 px-6 py-6 shadow-[0_8px_30px_rgb(15_23_42/0.08),0_1px_0_rgb(255_255_255/0.7)_inset] backdrop-blur-xl outline-none dark:border-slate-700/90 dark:bg-slate-900/75 dark:shadow-[0_8px_30px_rgb(0_0_0/0.45),inset_0_1px_0_rgb(255_255_255/0.06)] sm:mt-10 sm:rounded-[1.25rem]">
                {{ $slot }}
            </main>
        </div>
        <x-theme-toggle :floating="true" />
    </body>
</html>
