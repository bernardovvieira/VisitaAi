{{-- Lista quantidade + % para painel de indicadores (ocupantes). --}}
@props([
    'titulo',
    'itens' => [],
    'labels' => [],
    'totalBase' => 0,
    'naoInformadoLabel' => null,
])
<x-section-card {{ $attributes->merge(['class' => 'v-card--tight shadow-md shadow-slate-200/20 dark:shadow-none']) }}>
    <h2 class="v-section-title">{{ $titulo }}</h2>
    <ul class="mt-2 space-y-1.5 text-sm">
        @foreach($itens as $codigo => $qtd)
            @php
                $pctLista = $totalBase > 0 ? (int) round(100 * $qtd / $totalBase) : 0;
                $rotulo = $labels[$codigo] ?? (($codigo === 'nao_informado' && filled($naoInformadoLabel)) ? $naoInformadoLabel : $codigo);
            @endphp
            <li class="flex flex-wrap items-baseline justify-between gap-x-2 gap-y-0.5 border-b border-slate-100 pb-1.5 dark:border-slate-700">
                <span class="text-gray-700 dark:text-gray-300">{{ $rotulo }}</span>
                <span class="shrink-0 text-right font-semibold tabular-nums text-gray-900 dark:text-gray-100">
                    {{ number_format($qtd, 0, ',', '.') }}
                    <span class="text-xs font-normal text-slate-500 dark:text-slate-400">({{ $pctLista }}%)</span>
                </span>
            </li>
        @endforeach
    </ul>
    @if(empty($itens))
        <p class="mt-2 text-sm text-gray-500">{{ __('Sem dados.') }}</p>
    @endif
</x-section-card>
