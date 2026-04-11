<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="color-scheme" content="light dark">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="icon" type="image/png" href="{{ asset('images/visitaai_rembg.png') }}" sizes="512x512">
        <link rel="apple-touch-icon" href="{{ asset('images/visitaai_rembg.png') }}" sizes="512x512">

        @php
            $routeName = request()->route()?->getName();
            $authMeta = match (true) {
                in_array($routeName, ['login', 'login.store'], true) => [
                    'title' => config('app.name').' · '.__('Login'),
                    'desc' => __('Acesse o sistema Visita Aí com CPF ou e-mail. Área de gestores, ACE e ACS.'),
                ],
                $routeName === 'register' => [
                    'title' => config('app.name').' · '.__('Cadastro'),
                    'desc' => __('Cadastre-se no Visita Aí para atuar como ACE (Agente de Combate às Endemias) ou ACS (Agente Comunitário de Saúde), conforme Lei 11.350/2006.'),
                ],
                $routeName === 'password.request' => [
                    'title' => config('app.name').' · '.__('Esqueci a Senha'),
                    'desc' => __('Recupere o acesso ao sistema Visita Aí pelo e-mail.'),
                ],
                $routeName === 'password.reset' => [
                    'title' => config('app.name').' · '.__('Redefinir Senha'),
                    'desc' => __('Defina uma nova senha para sua conta no Visita Aí.'),
                ],
                $routeName === 'verification.notice' => [
                    'title' => config('app.name').' · '.__('Verificar E-mail'),
                    'desc' => __('Confirme seu e-mail para ativar sua conta no Visita Aí.'),
                ],
                in_array($routeName, ['two-factor.login', 'two-factor.login.store'], true) => [
                    'title' => config('app.name').' · '.__('Código de autenticação'),
                    'desc' => __('Confirme o login com o código de 6 dígitos do aplicativo autenticador.'),
                ],
                $routeName === 'pendente' => [
                    'title' => config('app.name').' · '.__('Conta Pendente'),
                    'desc' => __('Sua conta está aguardando aprovação do gestor municipal.'),
                ],
                default => [
                    'title' => config('app.name').' · '.__('Acesso'),
                    'desc' => __('Acesse o sistema Visita Aí. Painéis e indicadores municipais; operação em campo; transparência por código. Vigilância entomológica e PNCD quando o município adota.'),
                ],
            };
            $ogImage = rtrim(config('app.url'), '/') . '/images/visitaai_rembg.png';
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
        <meta property="og:locale" content="{{ app()->getLocale() === 'en' ? 'en_US' : 'pt_BR' }}">
        <!-- Twitter Card -->
        <meta name="twitter:card" content="summary">
        <meta name="twitter:title" content="{{ $authMeta['title'] }}">
        <meta name="twitter:description" content="{{ $authMeta['desc'] }}">
        <meta name="twitter:image" content="{{ $ogImage }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Tema: localStorage se existir; senão prefers-color-scheme no carregamento + ao mudar no sistema -->
        <script>
            (function () {
                function applyDark(on) {
                    document.documentElement.classList.toggle('dark', !!on);
                }
                var stored = '';
                try { stored = (localStorage.getItem('theme') || '').trim(); } catch (e) { stored = ''; }
                if (stored === 'dark' || stored === 'light') {
                    applyDark(stored === 'dark');
                    return;
                }
                var mq = window.matchMedia ? window.matchMedia('(prefers-color-scheme: dark)') : null;
                function syncFromOsOrStorage() {
                    try {
                        var s = (localStorage.getItem('theme') || '').trim();
                        if (s === 'dark') { applyDark(true); return; }
                        if (s === 'light') { applyDark(false); return; }
                    } catch (e) {}
                    applyDark(mq && mq.matches);
                }
                syncFromOsOrStorage();
                if (mq && mq.addEventListener) {
                    mq.addEventListener('change', syncFromOsOrStorage);
                }
            })();
        </script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-slate-900 antialiased dark:bg-slate-950 dark:text-slate-100">
        <a href="#main-content" class="visita-skip-link">{{ __('Ir para o conteúdo') }}</a>
        <div class="auth-guest-layout flex min-h-screen flex-col items-center px-4 pt-6 sm:justify-center sm:px-6 sm:pt-0">
            <div class="flex flex-col items-center gap-3">
                <x-application-logo />
                <a href="{{ url('/') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-blue-600 hover:underline dark:text-blue-400">
                    <x-heroicon-o-arrow-left class="h-4 w-4 shrink-0" />
                    {{ __('Voltar para o início') }}
                </a>
            </div>

            <x-layouts.auth-panel>
                <x-flash-alerts />
                {{ $slot }}
            </x-layouts.auth-panel>
        </div>
        <x-theme-toggle :floating="true" />
    </body>
</html>
