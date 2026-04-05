@php
    $copyrightYearStart = 2025;
    $copyrightYearNow = (int) now()->format('Y');
    $copyrightYears = $copyrightYearNow > $copyrightYearStart
        ? $copyrightYearStart.'–'.$copyrightYearNow
        : (string) $copyrightYearNow;
    $footerClass = trim($footerClass ?? '');
    $footerId = $footerId ?? null;
@endphp
<footer
    @if($footerId) id="{{ $footerId }}" @endif
    class="mt-10 border-t border-slate-200/90 pt-8 dark:border-slate-700/80{{ $footerClass !== '' ? ' '.$footerClass : '' }}"
>
    <div class="mx-auto flex max-w-2xl flex-col items-center gap-6 text-center sm:max-w-none sm:flex-row sm:items-center sm:justify-between sm:text-left">
        <div class="flex flex-col items-center gap-3 sm:flex-row sm:items-center sm:gap-4">
            <img
                src="{{ asset('images/visitaai_rembg.png') }}"
                alt=""
                width="44"
                height="44"
                class="h-11 w-11 shrink-0 object-contain opacity-95 dark:opacity-100"
                aria-hidden="true"
                decoding="async" />
            <div class="min-w-0 space-y-1">
                <p class="text-sm font-semibold tracking-tight text-slate-800 dark:text-slate-100">
                    <span class="text-slate-400 dark:text-slate-500" aria-hidden="true">&copy;</span>
                    {{ $copyrightYears }}
                    {{ config('app.brand') }}
                </p>
                <p class="text-xs leading-relaxed text-slate-500 dark:text-slate-400">
                    {{ __('Solução de apoio à vigilância em saúde e controle de vetores.') }}
                </p>
            </div>
        </div>
        <div class="shrink-0 border-t border-slate-200/80 pt-5 text-sm text-slate-600 dark:border-slate-600/80 dark:text-slate-300 sm:border-t-0 sm:pt-0 sm:text-right">
            <p class="font-semibold tracking-tight text-slate-900 dark:text-white">
                <a
                    href="https://bitwise.dev.br"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="transition hover:text-blue-700 dark:hover:text-blue-400"
                    title="bitwise.dev.br"
                >{{ __('Bitwise Technologies') }}</a>
            </p>
            <p class="mt-1 text-xs font-medium text-slate-500 dark:text-slate-400">
                {{ __('Todos os direitos reservados.') }}
            </p>
        </div>
    </div>
</footer>
