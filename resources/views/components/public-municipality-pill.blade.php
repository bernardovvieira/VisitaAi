@props(['local' => null])

@if ($local)
    <div
        role="status"
        aria-label="{{ __('Município') }}: {{ $local->loc_cidade }}, {{ $local->loc_estado }}"
        {{ $attributes->class([
            'inline-flex w-fit max-w-full flex-wrap items-center gap-x-1.5 gap-y-0 rounded border border-slate-200/70 bg-slate-50/40 py-0.5 pl-1.5 pr-2 dark:border-slate-600/55 dark:bg-slate-800/30',
        ]) }}
    >
        <span class="inline-flex items-center gap-0.5 text-[9px] font-medium uppercase tracking-[0.18em] text-slate-400 dark:text-slate-500">
            <svg class="h-2.5 w-2.5 shrink-0 opacity-70" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            {{ __('Município') }}
        </span>
        <span class="text-[11px] font-normal leading-tight text-slate-600 dark:text-slate-400">
            {{ $local->loc_cidade }}<span class="text-slate-400 dark:text-slate-500"> · {{ $local->loc_estado }}</span>
        </span>
    </div>
@endif
