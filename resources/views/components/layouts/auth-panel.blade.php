{{--
  Painel principal das páginas guest (login, registro, etc.): uma única superfície alinhada ao produto.
--}}
<main id="main-content" tabindex="-1" {{ $attributes->class([
  'auth-guest-panel mt-8 w-full max-w-md overflow-hidden rounded-2xl border border-slate-200/85 bg-white/95 px-6 py-6 shadow-sm shadow-slate-900/5 ring-1 ring-blue-500/[0.04] backdrop-blur-md dark:border-slate-700/70 dark:bg-slate-900/80 dark:shadow-[0_8px_28px_-14px_rgb(0_0_0/0.45)] dark:ring-blue-400/10 sm:mt-10 sm:rounded-2xl',
]) }}>
    {{ $slot }}
</main>
