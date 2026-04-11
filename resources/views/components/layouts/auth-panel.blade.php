{{--
  Painel principal das páginas guest (login, registro, etc.): uma única superfície alinhada ao produto.
--}}
<main id="main-content" tabindex="-1" {{ $attributes->class([
    'auth-guest-panel mt-8 w-full max-w-md overflow-hidden rounded-2xl border border-blue-200/80 bg-white/95 px-6 py-6 shadow-md shadow-blue-500/10 ring-1 ring-blue-500/[0.06] backdrop-blur-md dark:border-slate-600/50 dark:bg-slate-900 dark:shadow-[0_8px_32px_-12px_rgb(0_0_0/0.45)] dark:ring-slate-500/15 sm:mt-10 sm:rounded-2xl',
]) }}>
    {{ $slot }}
</main>
