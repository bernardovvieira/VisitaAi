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
    class="border-t border-slate-200/80 pt-6 dark:border-slate-700/80{{ $footerClass !== '' ? ' '.$footerClass : '' }}"
>
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between sm:gap-6">
        <div class="flex items-start gap-3">
            <img
                src="{{ asset('images/visitaai_rembg.png') }}"
                alt=""
                width="40"
                height="40"
                class="mt-0.5 h-9 w-9 shrink-0 object-contain opacity-90 dark:opacity-95"
                aria-hidden="true"
                decoding="async" />
            <p class="text-xs leading-relaxed text-gray-500 dark:text-gray-400">
                <span class="text-gray-400 dark:text-gray-500">&copy;</span>
                {{ $copyrightYears }}
                <span class="font-semibold text-gray-700 dark:text-gray-300">{{ config('app.brand') }}</span>.
                {{ __('Todos os direitos reservados.') }}
            </p>
        </div>
        <p class="text-xs text-gray-500 dark:text-gray-400 sm:text-right">
            {{ __('Desenvolvido por') }}
            <a href="https://bitwise.dev.br" target="_blank" rel="noopener noreferrer" class="font-semibold text-gray-700 underline-offset-2 hover:text-blue-600 hover:underline dark:text-gray-300 dark:hover:text-blue-400" title="bitwise.dev.br">BIT</a>
        </p>
    </div>
</footer>
