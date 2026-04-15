<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light dark">
    <meta name="theme-color" content="#f8fafc" id="theme-color-dynamic">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} · {{ __('Página não encontrada') }}</title>
    <script>
        (function () {
            var themeColorMeta = document.getElementById('theme-color-dynamic');
            function applyThemeColor(on) {
                var color = '#f8fafc';
                var metas = document.querySelectorAll('meta[name="theme-color"]');
                metas.forEach(function (m) { try { m.setAttribute('content', color); } catch (e) {} });
            }
            function applyDark(on) {
                document.documentElement.classList.toggle('dark', !!on);
                applyThemeColor(!!on);
            }
            function syncThemeColorFromClass() {
                applyThemeColor(document.documentElement.classList.contains('dark'));
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
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-gray-900 bg-white">
    <a href="#error-main" class="visita-skip-link">{{ __('Ir para o conteúdo') }}</a>
    <div class="flex min-h-screen flex-col items-center justify-center bg-gradient-to-br from-slate-100 via-white to-blue-50/50 px-4 py-12 dark:from-slate-950 dark:via-slate-900 dark:to-slate-950">
        <main id="error-main" tabindex="-1" class="w-full max-w-md text-center outline-none">
            <div class="mb-6">
                <span class="text-7xl font-black tabular-nums text-slate-200 dark:text-slate-700 sm:text-8xl" aria-hidden="true">404</span>
            </div>
            <div class="rounded-2xl border border-slate-200/90 bg-white/80 px-6 py-8 shadow-[0_8px_30px_rgb(15_23_42/0.08)] backdrop-blur-xl dark:border-slate-700/90 dark:bg-slate-900/75 dark:shadow-[0_8px_30px_rgb(0_0_0/0.45)]">
                <h1 class="text-xl font-bold text-slate-900 dark:text-slate-100">{{ __('Página não encontrada') }}</h1>
                <p class="mt-3 text-sm leading-relaxed text-slate-600 dark:text-slate-400">
                    {{ __('O endereço que você acessou não existe ou foi movido.') }}
                </p>
                <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row sm:flex-wrap">
                    @auth
                        <a href="{{ route('dashboard') }}" class="v-btn-primary">
                            {{ __('Ir para o painel') }}
                        </a>
                    @else
                        <a href="{{ url('/') }}" class="v-btn-primary">
                            {{ __('Voltar ao início') }}
                        </a>
                    @endauth
                    <a href="javascript:history.back()" class="v-btn-secondary">
                        {{ __('Voltar…') }}
                    </a>
                </div>
            </div>
        </main>
    </div>
    <x-theme-toggle :floating="true" />
</body>
</html>
