<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} · {{ __('Página não encontrada') }}</title>
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
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-gray-900 dark:text-gray-100">
    <a href="#error-main" class="fixed left-4 top-0 z-[200] -translate-y-full rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg outline-none ring-2 ring-blue-400 ring-offset-2 transition-transform focus:translate-y-4 dark:ring-offset-slate-900">{{ __('Ir para o conteúdo') }}</a>
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
                        <a href="{{ route('dashboard') }}" class="v-btn-primary inline-flex items-center justify-center px-4 py-2.5 text-[13px] font-semibold">
                            {{ __('Ir para o painel') }}
                        </a>
                    @else
                        <a href="{{ url('/') }}" class="v-btn-primary inline-flex items-center justify-center px-4 py-2.5 text-[13px] font-semibold">
                            {{ __('Voltar ao início') }}
                        </a>
                    @endauth
                    <a href="javascript:history.back()" class="v-btn-secondary inline-flex items-center justify-center px-4 py-2.5 text-[13px] font-semibold">
                        {{ __('Voltar…') }}
                    </a>
                </div>
            </div>
        </main>
    </div>
    <x-theme-toggle :floating="true" />
</body>
</html>
