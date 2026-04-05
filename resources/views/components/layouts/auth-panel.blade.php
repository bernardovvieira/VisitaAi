{{--
  Painel principal das páginas guest (login, registro, etc.) — uma única superfície alinhada ao produto.
--}}
<main id="main-content" tabindex="-1" {{ $attributes->class([
    'mt-8 w-full max-w-md overflow-hidden rounded-2xl border border-slate-200/80 bg-white/90 px-6 py-6 shadow-sm backdrop-blur-md dark:border-slate-700/80 dark:bg-slate-900/80 dark:shadow-[0_8px_30px_rgb(0_0_0/0.35)] sm:mt-10 sm:rounded-2xl',
]) }}>
    {{ $slot }}
</main>
