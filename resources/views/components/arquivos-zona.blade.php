{{--
    Zona visual unificada para anexos do imóvel (posse) ou do ocupante — cadastro, edição e consulta.
    Props: titulo (obrigatório), descricao (opcional), variant: imovel | ocupante, accentBorder (borda lateral colorida)
--}}
@props([
    'titulo',
    'descricao' => null,
    'variant' => 'imovel',
    'accentBorder' => true,
])

@php
    $frame = 'overflow-hidden rounded-xl border border-slate-200/90 bg-white shadow-sm ring-1 ring-slate-900/5 dark:border-slate-700/90 dark:bg-slate-900/60 dark:ring-white/5';
    if ($accentBorder) {
        $accent = $variant === 'ocupante'
            ? 'border-l-teal-500 dark:border-l-teal-400'
            : 'border-l-amber-600 dark:border-l-amber-500';
        $frame .= ' border-l-4 '.$accent;
    }
@endphp

<div {{ $attributes->class($frame) }}>
    <div class="flex items-start gap-3 border-b border-slate-100 bg-slate-50/90 px-4 py-3 dark:border-slate-700/80 dark:bg-slate-800/50">
        <span class="mt-0.5 inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white text-slate-600 shadow-sm dark:bg-slate-900 dark:text-slate-300">
            <x-heroicon-o-document-text class="h-5 w-5" aria-hidden="true" />
        </span>
        <div class="min-w-0 flex-1">
            <h3 class="text-sm font-semibold leading-snug text-slate-900 dark:text-slate-100">{{ $titulo }}</h3>
            @if(filled($descricao))
                <p class="mt-1 text-xs leading-relaxed text-slate-600 dark:text-slate-400">{{ $descricao }}</p>
            @endif
        </div>
    </div>
    <div class="px-4 py-4">
        {{ $slot }}
    </div>
</div>
