<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="color-scheme" content="light dark">
        <meta name="theme-color" media="(prefers-color-scheme: light)" content="#f8fafc">
        <meta name="theme-color" media="(prefers-color-scheme: dark)" content="#030712">
        <meta name="theme-color" content="#f8fafc" id="theme-color-dynamic">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="manifest" href="{{ asset('manifest.json') }}">
        <link rel="icon" type="image/svg+xml" href="{{ asset('images/visitaai.svg') }}">
        <link rel="apple-touch-icon" href="{{ asset('images/visitaai.svg') }}">

        @php
            $ogTitle = trim((string) ($__env->yieldContent('og_title') ?? ''));
            $ogTitle = $ogTitle ?: config('app.brand');
            $ogDescription = trim((string) ($__env->yieldContent('og_description') ?? ''));
            $ogDescription = $ogDescription ?: __('Plataforma municipal: indicadores e painéis com abrangência ampla, alimentados por cadastro de imóveis e visitas de campo. Vigilância entomológica, LIRAa e PNCD são funções especializadas quando o município adota. Consulta pública por código, sem dados clínicos.');
            $ogImage = trim((string) ($__env->yieldContent('og_image') ?? ''));
            $ogImage = $ogImage ?: rtrim(config('app.url'), '/') . '/images/visitaai.svg';
            $ogUrl = url()->current();
            $isHttps = str_starts_with(config('app.url'), 'https');
        @endphp

        <title>{{ $ogTitle }}</title>

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
        <meta property="og:image:type" content="image/svg+xml">
        <meta property="og:site_name" content="{{ config('app.brand') }}">
        <meta property="og:locale" content="{{ app()->getLocale() === 'en' ? 'en_US' : 'pt_BR' }}">
        <!-- Twitter Card -->
        <meta name="twitter:card" content="summary">
        <meta name="twitter:url" content="{{ $ogUrl }}">
        <meta name="twitter:title" content="{{ $ogTitle }}">
        <meta name="twitter:description" content="{{ $ogDescription }}">
        <meta name="twitter:image" content="{{ $ogImage }}">
        <meta name="twitter:image:alt" content="{{ $ogTitle }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Páginas públicas (home, consulta): usadas apenas para contexto; tema segue sistema se não houver escolha salva -->
        @if(View::hasSection('public'))
        <script>window.VisitaPublicPage = true;</script>
        @endif
        <!-- Preferência de tema do usuário (quando logado); ao criar conta o padrão é modo claro -->
        @auth
        @php
            $u = auth()->user();
            $dashPath = rtrim(parse_url(route('dashboard'), PHP_URL_PATH) ?: '', '/') ?: '/';
            if ($u->isAgenteEndemias()) {
                $visitaOfflineRedirect = route('dashboard');
                $visitaOfflineAllowed = [
                    $dashPath,
                    parse_url(route('agente.visitas.index'), PHP_URL_PATH),
                    parse_url(route('agente.visitas.create'), PHP_URL_PATH),
                    parse_url(route('agente.locais.index'), PHP_URL_PATH),
                    parse_url(route('agente.locais.create'), PHP_URL_PATH),
                ];
            } elseif ($u->isAgenteSaude()) {
                $visitaOfflineRedirect = route('dashboard');
                $visitaOfflineAllowed = [
                    $dashPath,
                    parse_url(route('saude.visitas.index'), PHP_URL_PATH),
                    parse_url(route('saude.visitas.create'), PHP_URL_PATH),
                    parse_url(route('saude.locais.index'), PHP_URL_PATH),
                    parse_url(route('saude.locais.create'), PHP_URL_PATH),
                ];
            } elseif ($u->isGestor()) {
                $visitaOfflineRedirect = route('dashboard');
                $visitaOfflineAllowed = [
                    $dashPath,
                    parse_url(route('gestor.visitas.index'), PHP_URL_PATH),
                ];
            } else {
                $visitaOfflineRedirect = route('dashboard');
                $visitaOfflineAllowed = [$dashPath];
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
        <!-- Tema: antes da pintura (evita flash). Logado → perfil; senão → localStorage; senão → prefers-color-scheme. Ouve mudanças do SO até o usuário fixar tema no botão. -->
        <script>
            (function () {
                var themeColorMeta = document.getElementById('theme-color-dynamic');
                function applyThemeColor(on) {
                    if (!themeColorMeta) return;
                    themeColorMeta.setAttribute('content', on ? '#030712' : '#f8fafc');
                }
                function applyDark(on) {
                    document.documentElement.classList.toggle('dark', !!on);
                    applyThemeColor(!!on);
                }
                function syncThemeColorFromClass() {
                    applyThemeColor(document.documentElement.classList.contains('dark'));
                }
                if (typeof window.VisitaThemePreference !== 'undefined' && window.VisitaThemePreference) {
                    var tp = window.VisitaThemePreference;
                    try { localStorage.setItem('theme', tp); } catch (e) {}
                    applyDark(tp === 'dark');
                    return;
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
                syncThemeColorFromClass();
                new MutationObserver(syncThemeColorFromClass).observe(document.documentElement, {
                    attributes: true,
                    attributeFilter: ['class'],
                });
            })();
        </script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            .nav-link-active { border-color: #3b82f6 !important; }
            .dropdown-link-active { background-color: rgba(59, 130, 246, 0.12) !important; color: #1d4ed8 !important; border-left: 3px solid #3b82f6; font-weight: 600; }
            .dark .dropdown-link-active { background-color: rgba(59, 130, 246, 0.2) !important; color: #93c5fd !important; }
            .responsive-nav-link-active { border-color: #3b82f6 !important; color: #1d4ed8 !important; background-color: rgba(59, 130, 246, 0.1) !important; }
            .dark .responsive-nav-link-active { color: #93c5fd !important; background-color: rgba(59, 130, 246, 0.2) !important; }
        </style>
    </head>
    <body class="font-sans antialiased text-[14px] leading-relaxed sm:text-[15px] {{ View::hasSection('public') ? 'bg-white' : 'bg-gradient-to-br from-slate-50 via-white to-blue-50/35 dark:from-slate-950 dark:via-slate-950 dark:to-slate-900' }}">
        <a href="#main-content" class="visita-skip-link">{{ __('Ir para o conteúdo') }}</a>
        @if (View::hasSection('public'))
            <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-blue-50/35 dark:from-gray-900 dark:via-gray-900 dark:to-slate-950">
                @isset($header)
                    <header class="bg-white shadow dark:bg-gray-800">
                        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endisset
                <main id="main-content" class="w-full" tabindex="-1">
                    @if (View::hasSection('public_full_bleed'))
                        <div class="w-full min-h-screen">
                            @yield('content')
                        </div>
                    @else
                        <div class="mx-auto w-full max-w-[min(100%,90rem)] px-5 py-8 sm:px-8 lg:px-12 xl:px-16 2xl:px-20">
                            @yield('content')
                        </div>
                    @endif
                </main>
            </div>
        @else
            @auth
                <div id="authenticated-shell"
                     class="flex min-h-screen bg-transparent"
                     x-data="{
                        sidebarOpen: false,
                        sidebarDesktop: (function () {
                            try {
                                var v = localStorage.getItem('visita-sidebar-desktop');
                                if (v === 'collapsed' || v === 'expanded') return v;
                                if (v === 'hidden') return 'expanded';
                            } catch (e) {}
                            return 'expanded';
                        })(),
                        isLg: false,
                        online: typeof navigator !== 'undefined' ? navigator.onLine : true,
                     }"
                     x-init="
                        isLg = typeof window !== 'undefined' && window.matchMedia('(min-width: 1024px)').matches;
                        if (typeof window !== 'undefined') {
                            var _mqLg = window.matchMedia('(min-width: 1024px)');
                            _mqLg.addEventListener('change', function () { isLg = _mqLg.matches; });
                        }
                        function updateOnline(e) {
                            var d = e && e.detail;
                            online = (d && typeof d.online === 'boolean') ? d.online : (typeof window.visitaConnectionOnline !== 'undefined' ? window.visitaConnectionOnline : (typeof navigator !== 'undefined' ? navigator.onLine : true));
                        }
                        document.addEventListener('visita-connection-change', updateOnline);
                        window.addEventListener('visita-connection-change', updateOnline);
                        $nextTick(function () {
                            online = typeof window.visitaConnectionOnline !== 'undefined' ? window.visitaConnectionOnline : (typeof navigator !== 'undefined' ? navigator.onLine : true);
                        });
                        setInterval(function () {
                            if (typeof window.visitaConnectionOnline === 'boolean' && online !== window.visitaConnectionOnline) {
                                online = window.visitaConnectionOnline;
                            }
                        }, 400);
                        function closeSidebarIfDesktop() {
                            if (window.innerWidth >= 1024) sidebarOpen = false;
                        }
                        window.addEventListener('resize', closeSidebarIfDesktop);
                     "
                     x-effect="
                        if (typeof window.visitaConnectionOnline === 'boolean') online = window.visitaConnectionOnline;
                        try { localStorage.setItem('visita-sidebar-desktop', sidebarDesktop); } catch (e) {}
                     "
                     @keydown.escape.window="sidebarOpen = false">
                    @include('layouts.partials.sidebar')
                    <div class="fixed inset-0 z-40 bg-slate-950/55 backdrop-blur-sm transition-opacity lg:hidden"
                         x-show="sidebarOpen"
                         x-cloak
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         @click="sidebarOpen = false"
                         role="button"
                         tabindex="-1"
                         aria-label="{{ __('Fechar menu') }}"></div>
                    <div class="flex min-h-screen min-w-0 flex-1 flex-col">
                        @include('layouts.partials.topbar')
                        @isset($header)
                            <header class="border-b border-slate-200/90 bg-white/90 shadow-sm backdrop-blur-md dark:border-slate-700 dark:bg-slate-900/85">
                                <div class="mx-auto max-w-7xl px-4 py-5 sm:px-6 lg:px-8">
                                    {{ $header }}
                                </div>
                            </header>
                        @endisset
                        <main id="main-content" class="v-app-main text-slate-800 dark:text-slate-100" tabindex="-1" x-bind:inert="sidebarOpen">
                            <div class="v-app-main-inner">
                                @yield('content')
                            </div>
                        </main>
                    </div>
                </div>
            @else
                <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
                    @isset($header)
                        <header class="bg-white shadow dark:bg-gray-800">
                            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                                {{ $header }}
                            </div>
                        </header>
                    @endisset
                    <main id="main-content" tabindex="-1">
                        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                            @yield('content')
                        </div>
                    </main>
                </div>
            @endauth
        @endif
        {{-- Toggle flutuante apenas em páginas públicas (home, consulta); nas restritas fica só no navbar --}}
        @if(View::hasSection('public'))
        <x-theme-toggle :floating="true" />
        @endif
        {{-- Aviso de cookies: apenas em páginas públicas (home, consulta) --}}
        @if(View::hasSection('public'))
            <x-cookie-banner />
        @endif
        @php
            $visitaI18n = [
                'connectionLostRedirect' => __('Conexão perdida. Redirecionando para a página inicial.'),
                'connectionLost' => __('Conexão perdida.'),
                'connectionRestored' => __('Conexão reestabelecida.'),
                'offlinePendingMsg' => __('Há pendências a serem sincronizadas para o sistema.'),
                'offlineSendNow' => __('Enviar agora'),
                'offlineCloseLabel' => __('Fechar'),
            ];
        @endphp
        <script>
        window.VisitaI18n = @json($visitaI18n);
        (function() {
            var pingUrl = "{{ url(route('ping')) }}";
            var pingTimeoutMs = 1200;
            var lastOnline;
            var i18n = window.VisitaI18n || {};

            function showConnectionToast(msg, isError) {
                var el = document.createElement('div');
                el.setAttribute('role', 'alert');
                el.className = 'v-toast-connection ' + (isError ? 'v-toast-connection--warn' : 'v-toast-connection--ok');
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
                    if (!ok && (p.indexOf('/agente/visitas') === 0 || p.indexOf('/agente/locais') === 0 || p.indexOf('/saude/visitas') === 0)) ok = true;
                    if (!ok) {
                        if (wasOnline) showConnectionToast(i18n.connectionLostRedirect || '', true);
                        window.location.href = window.visitaOfflineRedirect;
                        return;
                    }
                    if (wasOnline) showConnectionToast(i18n.connectionLost || '', true);
                } else if (o && wasOffline) {
                    showConnectionToast(i18n.connectionRestored || '', false);
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
        @stack('scripts')
    </body>
</html>
