<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="manifest" href="{{ asset('manifest.json') }}">

        <title>{{ config('app.name', 'Visita Aí - Local - Sistema de Apoio à Vigilância Entomológica e Controle Vetorial Municipal') }}</title>

        @php
            $ogTitle = trim((string) ($__env->yieldContent('og_title') ?? ''));
            $ogTitle = $ogTitle ?: config('app.name');
            $ogDescription = trim((string) ($__env->yieldContent('og_description') ?? ''));
            $ogDescription = $ogDescription ?: 'Sistema de apoio à vigilância entomológica e controle vetorial municipal. Acompanhe, consulte e controle visitas de forma ágil e segura.';
            $ogImage = trim((string) ($__env->yieldContent('og_image') ?? ''));
            $ogImage = $ogImage ?: rtrim(config('app.url'), '/') . '/images/visitaai.png';
            $ogUrl = url()->current();
            $isHttps = str_starts_with(config('app.url'), 'https');
        @endphp
        <!-- Open Graph (Facebook, WhatsApp, etc.) - URLs absolutas obrigatórias -->
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ $ogUrl }}">
        <meta property="og:title" content="{{ $ogTitle }}">
        <meta property="og:description" content="{{ $ogDescription }}">
        <meta property="og:image" content="{{ $ogImage }}">
        @if($isHttps)
        <meta property="og:image:secure_url" content="{{ $ogImage }}">
        @endif
        <meta property="og:image:width" content="1145">
        <meta property="og:image:height" content="722">
        <meta property="og:image:type" content="image/png">
        <meta property="og:site_name" content="Visita Aí">
        <meta property="og:locale" content="pt_BR">
        <!-- Twitter Card -->
        <meta name="twitter:card" content="summary">
        <meta name="twitter:url" content="{{ $ogUrl }}">
        <meta name="twitter:title" content="{{ $ogTitle }}">
        <meta name="twitter:description" content="{{ $ogDescription }}">
        <meta name="twitter:image" content="{{ $ogImage }}">
        <meta name="twitter:image:alt" content="{{ $ogTitle }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Páginas públicas (home, consulta): padrão sempre modo claro quando não há preferência salva -->
        @if(View::hasSection('public'))
        <script>window.VisitaPublicPage = true;</script>
        @endif
        <!-- Preferência de tema do usuário (quando logado); ao criar conta o padrão é modo claro -->
        @auth
        @php
            $u = auth()->user();
            if ($u->isAgenteEndemias()) {
                $visitaOfflineRedirect = route('agente.visitas.index');
                $visitaOfflineAllowed = [
                    parse_url(route('agente.visitas.index'), PHP_URL_PATH),
                    parse_url(route('agente.visitas.create'), PHP_URL_PATH),
                    parse_url(route('agente.locais.index'), PHP_URL_PATH),
                    parse_url(route('agente.locais.create'), PHP_URL_PATH),
                ];
            } elseif ($u->isAgenteSaude()) {
                $visitaOfflineRedirect = route('saude.visitas.index');
                $visitaOfflineAllowed = [parse_url(route('saude.visitas.index'), PHP_URL_PATH), parse_url(route('saude.visitas.create'), PHP_URL_PATH)];
            } elseif ($u->isGestor()) {
                $visitaOfflineRedirect = route('gestor.visitas.index');
                $visitaOfflineAllowed = [parse_url(route('gestor.visitas.index'), PHP_URL_PATH)];
            } else {
                $visitaOfflineRedirect = route('dashboard');
                $visitaOfflineAllowed = [parse_url(route('dashboard'), PHP_URL_PATH)];
            }
            $visitaOfflineAllowed = array_values(array_map(function ($p) { return rtrim($p ?: '', '/') ?: '/'; }, $visitaOfflineAllowed));
        @endphp
        <script>
            window.VisitaThemePreference = @json($u->use_tema ?? 'light');
            window.VisitaThemeSyncUrl = @json(route('profile.tema.update'));
            @if($u->isAgenteEndemias() || $u->isAgenteSaude())
            window.VisitaOfflineSyncPageUrl = @json($u->isAgenteSaude() ? route('saude.sincronizar') : route('agente.sincronizar'));
            window.VisitaOfflineProfile = @json($u->isAgenteSaude() ? 'saude' : 'agente');
            @endif
            window.visitaOfflineRedirect = @json($visitaOfflineRedirect);
            window.visitaOfflineAllowedPaths = @json($visitaOfflineAllowed);
        </script>
        @endauth
        <!-- Tema claro/escuro: aplicado antes da pintura para evitar flash. Em todas as páginas (incl. públicas) respeita localStorage; fallback: preferência do sistema ou claro. -->
        <script>
            (function(){
                var t;
                if (typeof window.VisitaThemePreference !== 'undefined' && window.VisitaThemePreference) {
                    t = window.VisitaThemePreference;
                    try { localStorage.setItem('theme', t); } catch (e) {}
                } else {
                    try {
                        t = (localStorage.getItem('theme') || '').trim();
                    } catch (e) { t = ''; }
                    if (t !== 'dark' && t !== 'light') {
                        t = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
                    }
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
        (function() {
            var pingUrl = "{{ url(route('ping')) }}";
            var pingTimeoutMs = 1200;
            var lastOnline;

            function showConnectionToast(msg, isError) {
                var el = document.createElement('div');
                el.setAttribute('role', 'alert');
                el.className = 'fixed top-4 left-1/2 transform -translate-x-1/2 z-[100] px-4 py-3 rounded-lg shadow-lg text-sm font-medium text-white ' + (isError ? 'bg-amber-600' : 'bg-green-600');
                el.textContent = msg;
                document.body.appendChild(el);
                setTimeout(function() { if (el.parentNode) el.parentNode.removeChild(el); }, 7000);
            }

            function setConnectionStatus(o) {
                if (lastOnline === o) return;
                var wasOnline = lastOnline === true;
                var wasOffline = lastOnline === false;
                lastOnline = o;
                window.visitaConnectionOnline = o;
                if (!o && window.visitaOfflineAllowedPaths && window.visitaOfflineRedirect) {
                    var p = (window.location.pathname || '').replace(/\/$/, '') || '/';
                    var allowed = window.visitaOfflineAllowedPaths;
                    var ok = allowed.some(function(a) { return a === p; });
                    if (!ok && p.indexOf('/agente/locais') === 0) ok = true;
                    if (!ok) {
                        if (wasOnline) showConnectionToast('Conexão perdida. Redirecionando para Visitas.', true);
                        window.location.href = window.visitaOfflineRedirect;
                        return;
                    }
                    if (wasOnline) showConnectionToast('Conexão perdida.', true);
                } else if (o && wasOffline) {
                    showConnectionToast('Conexão reestabelecida.', false);
                }
                var ev = new CustomEvent('visita-connection-change', { detail: { online: o }, bubbles: true });
                document.dispatchEvent(ev);
                window.dispatchEvent(ev);
            }

            function checkConnection() {
                if (typeof navigator !== 'undefined' && !navigator.onLine) {
                    setConnectionStatus(false);
                    return;
                }
                var controller = new AbortController();
                var timeoutId = setTimeout(function() { controller.abort(); }, pingTimeoutMs);
                fetch(pingUrl + '?t=' + Date.now(), { method: 'GET', cache: 'no-store', signal: controller.signal })
                    .then(function() {
                        clearTimeout(timeoutId);
                        setConnectionStatus(true);
                    })
                    .catch(function() {
                        clearTimeout(timeoutId);
                        setConnectionStatus(false);
                    });
            }

            setConnectionStatus(typeof navigator !== 'undefined' ? navigator.onLine : true);
            window.addEventListener('online', function() { checkConnection(); });
            window.addEventListener('offline', function() { setConnectionStatus(false); });
            document.addEventListener('visibilitychange', function() {
                if (document.visibilityState === 'visible') checkConnection();
            });
            window.addEventListener('focus', function() { checkConnection(); });
            function startPingInterval() {
                checkConnection();
                setInterval(checkConnection, 800);
            }
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', startPingInterval);
            } else {
                startPingInterval();
            }
        })();
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('[data-alert-autodismiss]').forEach(function(el) {
                var ms = parseInt(el.getAttribute('data-alert-autodismiss'), 10) || 5000;
                setTimeout(function() { el.style.display = 'none'; }, ms);
            });
            document.querySelectorAll('input[data-live-url]').forEach(function(input) {
                var url = input.getAttribute('data-live-url');
                var param = input.getAttribute('data-live-param') || 'search';
                var loadingId = input.getAttribute('data-live-loading-id');
                var loadingEl = loadingId ? document.getElementById(loadingId) : null;
                var timer;
                var debounceMs = 600;
                function doSearch() {
                    var val = (input.value || '').trim();
                    if (loadingEl) loadingEl.classList.remove('hidden');
                    var target;
                    if (val) {
                        target = url + (url.indexOf('?') >= 0 ? '&' : '?') + param + '=' + encodeURIComponent(val);
                    } else {
                        var params = new URLSearchParams(window.location.search);
                        params.delete(param);
                        target = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
                    }
                    if (target !== (window.location.pathname + window.location.search)) window.location.href = target;
                }
                input.addEventListener('input', function() {
                    clearTimeout(timer);
                    if (loadingEl) loadingEl.classList.add('hidden');
                    var val = (input.value || '').trim();
                    timer = setTimeout(doSearch, debounceMs);
                });
                input.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        clearTimeout(timer);
                        doSearch();
                    }
                });
            });
            // Restaurar foco no campo de busca após recarregar a página (busca dinâmica); cursor no fim do texto
            var searchParams = new URLSearchParams(window.location.search);
            document.querySelectorAll('input[data-live-url]').forEach(function(input) {
                var param = input.getAttribute('data-live-param') || 'search';
                if (searchParams.has(param)) {
                    input.focus();
                    var len = (input.value || '').length;
                    input.setSelectionRange(len, len);
                    return;
                }
            });
        });
        </script>
    </body>
</html>
