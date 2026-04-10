@php
    $copyrightYearStart = 2025;
    $copyrightYearNow = (int) now()->format('Y');
    $copyrightYears = $copyrightYearNow > $copyrightYearStart
        ? $copyrightYearStart.' a '.$copyrightYearNow
        : (string) $copyrightYearNow;
    $footerClass = trim($footerClass ?? '');
    $footerId = $footerId ?? null;
@endphp
<footer
    @if($footerId) id="{{ $footerId }}" @endif
    class="mt-10 border-t border-slate-200/50 pt-7 dark:border-slate-800/70{{ $footerClass !== '' ? ' '.$footerClass : '' }}"
>
    <div class="mx-auto flex max-w-2xl flex-col items-center gap-5 text-center sm:max-w-none sm:flex-row sm:items-center sm:justify-between sm:text-left">
        <div class="flex flex-col items-center gap-3 sm:flex-row sm:items-center sm:gap-3.5">
            <img
                src="{{ asset('images/visitaai_rembg.png') }}"
                alt=""
                width="72"
                height="72"
                class="h-12 w-12 shrink-0 object-contain opacity-90 sm:h-14 sm:w-14 dark:opacity-100"
                aria-hidden="true"
                decoding="async" />
            <div class="min-w-0 space-y-0.5">
                <p class="text-xs font-medium tracking-wide text-slate-600 dark:text-slate-400">
                    <span class="text-slate-400 dark:text-slate-500" aria-hidden="true">&copy;</span>
                    {{ $copyrightYears }}
                    {{ config('app.brand') }}
                </p>
                <p class="text-[11px] leading-relaxed text-slate-400 dark:text-slate-500">
                    {{ __('Solução de apoio à vigilância em saúde e controle de vetores.') }}
                </p>
            </div>
        </div>
        <div class="shrink-0 border-t border-slate-200/50 pt-4 text-xs text-slate-500 dark:border-slate-700/60 dark:text-slate-400 sm:border-t-0 sm:pt-0 sm:text-right">
            <p class="font-medium tracking-tight text-slate-700 dark:text-slate-300">
                <a
                    href="https://bitwise.dev.br"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="transition hover:text-slate-900 dark:hover:text-slate-100"
                    title="bitwise.dev.br"
                >{{ __('Bitwise Technologies') }}</a>
            </p>
            <p class="mt-0.5 text-[11px] font-normal text-slate-400 dark:text-slate-500">
                {{ __('Todos os direitos reservados.') }}
            </p>
        </div>
    </div>
</footer>
