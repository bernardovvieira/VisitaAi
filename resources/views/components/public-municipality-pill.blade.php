@props(['local' => null])

@if ($local)
    <div
        role="status"
        aria-label="{{ __('Município') }}: {{ $local->loc_cidade }}, {{ $local->loc_estado }}"
        {{ $attributes->class([
            'inline-flex w-fit max-w-full flex-wrap items-center gap-x-3 gap-y-1 rounded-full border border-blue-200/95 bg-blue-50/95 py-2 pl-3 pr-4 shadow-sm ring-1 ring-blue-500/10 dark:border-blue-700/60 dark:bg-blue-950/55 dark:ring-blue-400/10 sm:pl-4 sm:pr-5',
        ]) }}
    >
        <span class="flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-[0.14em] text-blue-700 dark:text-blue-300">
            <svg class="h-3.5 w-3.5 opacity-90" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            {{ __('Município') }}
        </span>
        <span class="hidden h-3 w-px shrink-0 bg-blue-300/90 sm:block dark:bg-blue-500/50" aria-hidden="true"></span>
        <span class="text-[15px] font-bold leading-tight tracking-tight text-slate-900 dark:text-white sm:text-base">
            {{ $local->loc_cidade }}<span class="font-semibold text-slate-600 dark:text-slate-300"> · {{ $local->loc_estado }}</span>
        </span>
    </div>
@endif
