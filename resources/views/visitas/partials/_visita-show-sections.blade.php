@php
    $locTipoLabels = ['R' => __('Residencial'), 'C' => __('Comercial'), 'T' => __('Terreno baldio')];
    $locZonaLabels = ['U' => __('Urbana'), 'R' => __('Rural')];
    $na = __('N/I');
@endphp
{{-- Local --}}
<x-section-card class="space-y-5">
    <div class="flex items-center justify-between gap-3">
        <h2 class="v-section-title">{{ __('Local visitado') }}</h2>
        <span class="inline-flex rounded-full bg-blue-50 px-3 py-1 text-[11px] font-semibold uppercase tracking-wide text-blue-700 dark:bg-blue-500/15 dark:text-blue-300">#{{ $visita->local->loc_codigo_unico }}</span>
    </div>
    <div class="grid grid-cols-1 gap-5 xl:grid-cols-3">
        <div class="space-y-5 xl:col-span-2">
            <dl class="grid grid-cols-1 gap-x-4 gap-y-3 text-sm text-gray-700 dark:text-gray-300 sm:grid-cols-3">
                <div class="sm:col-span-3">
                    <dt class="text-xs font-medium uppercase tracking-wide text-slate-600 dark:text-slate-300">{{ __('Código único do imóvel') }}</dt>
                    <dd class="mt-1 flex flex-wrap items-center gap-x-4 gap-y-1">
                        <span class="inline-block rounded bg-slate-100 px-2 py-1 font-mono text-xs font-semibold tracking-tight text-slate-800 dark:bg-slate-700 dark:text-slate-200">#{{ $visita->local->loc_codigo_unico }}</span>
                    </dd>
                </div>
                <div class="space-y-3">
                    <div>
                        <dt class="font-medium text-slate-700 dark:text-slate-200">{{ __('Tipo') }}</dt>
                        <dd class="mt-1">{{ $locTipoLabels[$visita->local->loc_tipo] ?? $na }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-slate-700 dark:text-slate-200">{{ __('Zona') }}</dt>
                        <dd class="mt-1">
                            @if ($visita->local->loc_zona === 'U')
                                <span class="inline-flex rounded-md bg-violet-100 px-2 py-0.5 text-xs font-semibold text-violet-900 dark:bg-violet-900/60 dark:text-violet-200">{{ __('Urbana') }}</span>
                            @elseif ($visita->local->loc_zona === 'R')
                                <span class="inline-flex rounded-md bg-amber-100 px-2 py-0.5 text-xs font-semibold text-amber-900 dark:bg-amber-950/60 dark:text-amber-200">{{ __('Rural') }}</span>
                            @else
                                {{ $locZonaLabels[$visita->local->loc_zona] ?? $na }}
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="font-medium text-slate-700 dark:text-slate-200">{{ __('Quarteirão') }}</dt>
                        <dd class="mt-1">{{ $visita->local->loc_quarteirao ?? $na }}</dd>
                    </div>
                </div>
                <div class="space-y-3">
                    <div>
                        <dt class="font-medium text-slate-700 dark:text-slate-200">{{ __('Código da localidade') }}</dt>
                        <dd class="mt-1">{{ $visita->local->loc_codigo !== null && $visita->local->loc_codigo !== '' ? '#'.$visita->local->loc_codigo : $na }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-slate-700 dark:text-slate-200">{{ __('Sequência') }}</dt>
                        <dd class="mt-1">{{ $visita->local->loc_sequencia ?? $na }}</dd>
                    </div>
                </div>
                <div class="space-y-3">
                    <div>
                        <dt class="font-medium text-slate-700 dark:text-slate-200">{{ __('Categoria') }}</dt>
                        <dd class="mt-1">{{ $visita->local->loc_categoria ?? $na }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-slate-700 dark:text-slate-200">{{ __('Lado') }}</dt>
                        <dd class="mt-1">{{ $visita->local->loc_lado ?? $na }}</dd>
                    </div>
                </div>
                <div class="sm:col-span-3 border-t border-slate-200/80 pt-3 dark:border-slate-700/70">
                    <dt class="font-medium text-slate-700 dark:text-slate-200">{{ __('Endereço completo') }}</dt>
                    <dd class="mt-1 text-gray-900 dark:text-gray-100">
                        {{ $visita->local->loc_endereco }}, {{ $visita->local->loc_numero ?? __('S/N') }}, {{ $visita->local->loc_bairro }}, {{ $visita->local->loc_cidade }}/{{ $visita->local->loc_estado }}, {{ $visita->local->loc_pais }}
                        <span class="text-slate-500 dark:text-slate-400"> · </span>{{ __('CEP') }}: {{ $visita->local->loc_cep }}
                        @if($visita->local->loc_complemento)
                            <span class="text-slate-500 dark:text-slate-400"> · </span>{{ __('Complemento') }}: {{ $visita->local->loc_complemento }}
                        @endif
                    </dd>
                </div>
            </dl>
        </div>

        <aside class="space-y-3 xl:col-span-1">
            <div class="space-y-2">
                <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ __('Mapa') }}</h3>
                <div id="map" class="h-48 rounded-lg border border-gray-300 shadow-sm dark:border-gray-600 dark:shadow-none"></div>
            </div>
            <dl class="grid grid-cols-2 gap-3 rounded-lg border border-slate-200/80 bg-slate-50/70 p-3 text-[11px] dark:border-slate-700/70 dark:bg-slate-900/45">
                <div class="min-w-0">
                    <dt class="font-medium text-slate-500 dark:text-slate-400">{{ __('Latitude') }}</dt>
                    <dd class="tabular-nums text-slate-800 dark:text-slate-100">{{ $visita->local->loc_latitude }}</dd>
                </div>
                <div class="min-w-0">
                    <dt class="font-medium text-slate-500 dark:text-slate-400">{{ __('Longitude') }}</dt>
                    <dd class="tabular-nums text-slate-800 dark:text-slate-100">{{ $visita->local->loc_longitude }}</dd>
                </div>
            </dl>
        </aside>

        <div class="xl:col-span-3">
            <a href="{{ route('gestor.locais.show', $visita->local) }}" class="inline-flex w-full items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
                <x-heroicon-o-arrow-top-right-on-square class="h-3.5 w-3.5" aria-hidden="true" />
                {{ __('Ver todos os dados') }}
            </a>
        </div>
    </div>
</x-section-card>

{{-- Dados da Visita --}}
<x-section-card class="space-y-5 rounded-2xl border border-slate-200/80 bg-white/95 p-5 shadow-sm dark:border-slate-700/70 dark:bg-slate-900/50">
    <div class="border-b border-slate-200/80 pb-3 dark:border-slate-700/70">
        <h2 class="text-lg font-semibold text-slate-800 dark:text-slate-100">{{ __('Dados da visita') }}</h2>
    </div>
    <dl class="grid grid-cols-1 gap-4 text-sm text-slate-700 dark:text-slate-300 sm:grid-cols-3">
        <div>
            <dt class="text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">{{ __('Código da visita') }}</dt>
            <dd class="mt-1">
                <span class="inline-block rounded bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-800 dark:bg-slate-700 dark:text-slate-200">
                    #{{ $visita->vis_id }}
                </span>
            </dd>
        </div>
        <div>
            <dt class="text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">{{ __('Data') }}</dt>
            <dd>{{ \Carbon\Carbon::parse($visita->vis_data)->format('d/m/Y') }}</dd>
        </div>
        <div>
            <dt class="text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">{{ __('Ciclo/ano') }}</dt>
            <dd>{{ $visita->vis_ciclo ?? $na }}</dd>
        </div>
        <div>
            <dt class="text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">{{ __('Atividade') }}</dt>
            <dd>
                @if(\App\Helpers\MsTerminologia::atividadeCodigo($visita->vis_atividade))
                    {{ \App\Helpers\MsTerminologia::atividadeCodigo($visita->vis_atividade) }} · {{ \App\Helpers\MsTerminologia::atividadeNome($visita->vis_atividade) }}
                @else
                    {{ __('Não informado') }}
                @endif
            </dd>
        </div>
        <div>
            <dt class="text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">{{ __('Tipo de visita') }}</dt>
            <dd>
                @if ($visita->vis_visita_tipo === 'N')
                    <span class="inline-block rounded bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-800 dark:bg-slate-700 dark:text-slate-200">{{ \App\Helpers\MsTerminologia::visitaTipoLabel('N') }}</span>
                @elseif ($visita->vis_visita_tipo === 'R')
                    <span class="inline-block bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-200 px-2 py-1 rounded text-xs font-semibold">{{ \App\Helpers\MsTerminologia::visitaTipoLabel('R') }}</span>
                @else
                    <span class="text-gray-500 dark:text-gray-400 italic">{{ __('Não informado') }}</span>
                @endif
            </dd>
        </div>
        <div>
            <dt class="text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">{{ __('Pendências') }}</dt>
            <dd>
                @if ($visita->vis_pendencias)
                    <span class="inline-block bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-200 px-2 py-1 rounded text-xs font-semibold">{{ __('Houve pendência') }}</span>
                @else
                    <span class="inline-block rounded bg-emerald-100 px-2 py-1 text-xs font-semibold text-emerald-900 dark:bg-emerald-900/45 dark:text-emerald-200">{{ __('Nenhuma pendência') }}</span>
                @endif
            </dd>
        </div>
    </dl>
</x-section-card>

{{-- Observações --}}
<x-section-card class="space-y-3 rounded-2xl border border-slate-200/80 bg-white/95 p-5 shadow-sm dark:border-slate-700/70 dark:bg-slate-900/50">
    <div class="border-b border-slate-200/80 pb-3 dark:border-slate-700/70">
        <h2 class="text-lg font-semibold text-slate-800 dark:text-slate-100">{{ __('Observações') }}</h2>
    </div>
    <p class="text-sm text-slate-900 dark:text-slate-100">{{ $visita->vis_observacoes ?: __('Nenhuma observação registrada.') }}</p>
</x-section-card>

@php
    $obsOcup = $visita->vis_ocupantes_observacoes;
    $obsOcup = is_array($obsOcup) ? $obsOcup : [];
@endphp
@if(count($obsOcup) > 0)
    <x-section-card class="space-y-4 rounded-2xl border border-slate-200/80 bg-white/95 p-5 shadow-sm dark:border-slate-700/70 dark:bg-slate-900/50">
        <div class="border-b border-slate-200/80 pb-3 dark:border-slate-700/70">
            <h2 class="text-lg font-semibold text-slate-800 dark:text-slate-100">{{ __('Ocupantes (nesta visita)') }}</h2>
        </div>
        <ul class="space-y-3">
            @foreach($obsOcup as $mid => $txt)
                @if(trim((string) $txt) === '')
                    @continue
                @endif
                @php $mor = $visita->local->moradores->firstWhere('mor_id', (int) $mid); @endphp
                <li class="rounded-xl border border-slate-200/80 bg-slate-50/70 p-3 dark:border-slate-700/70 dark:bg-slate-900/40">
                    <p class="text-xs font-semibold text-slate-600 dark:text-slate-300">{{ $mor && $mor->mor_nome ? $mor->mor_nome : __('Ocupante #:id', ['id' => $mid]) }}</p>
                    <p class="mt-1 whitespace-pre-wrap text-sm text-slate-900 dark:text-slate-100">{{ $txt }}</p>
                </li>
            @endforeach
        </ul>
    </x-section-card>
@endif

{{-- Doenças --}}
<x-section-card class="space-y-4 rounded-2xl border border-slate-200/80 bg-white/95 p-5 shadow-sm dark:border-slate-700/70 dark:bg-slate-900/50">
    <div class="border-b border-slate-200/80 pb-3 dark:border-slate-700/70">
        <h2 class="text-lg font-semibold text-slate-800 dark:text-slate-100">{{ __('Doenças monitoradas na visita') }}</h2>
    </div>

    @if ($visita->doencas->count())
        @foreach ($visita->doencas as $doenca)
            <div class="space-y-3 rounded-2xl border border-slate-200/80 bg-slate-50/70 p-4 dark:border-slate-700/70 dark:bg-slate-900/40">
                <h3 class="text-base font-semibold text-slate-900 dark:text-slate-100">{{ $doenca->doe_nome }}</h3>

                @if (!empty($doenca->doe_sintomas))
                    <div class="rounded-lg border border-amber-200/90 bg-amber-50/55 p-3 dark:border-amber-800/45 dark:bg-amber-950/25">
                        <p class="text-xs font-semibold uppercase tracking-wide text-amber-900/90 dark:text-amber-100/90">{{ __('Sintomas') }}</p>
                        <div class="mt-2 flex flex-wrap gap-2">
                            @foreach ($doenca->doe_sintomas as $sintoma)
                                <span class="inline-block rounded-md border border-amber-300/80 bg-amber-100/90 px-2 py-0.5 text-xs font-medium text-amber-950 dark:border-amber-600/60 dark:bg-amber-900/40 dark:text-amber-50">
                                    {{ $sintoma }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if (!empty($doenca->doe_transmissao))
                    <div class="rounded-lg border border-sky-200/90 bg-sky-50/55 p-3 dark:border-sky-800/45 dark:bg-sky-950/25">
                        <p class="text-xs font-semibold uppercase tracking-wide text-sky-900/90 dark:text-sky-100/90">{{ __('Modos de transmissão') }}</p>
                        <div class="mt-2 flex flex-wrap gap-2">
                            @foreach ($doenca->doe_transmissao as $transmissao)
                                <span class="inline-block rounded-md border border-sky-300/80 bg-sky-100/90 px-2 py-0.5 text-xs font-medium text-sky-950 dark:border-sky-600/60 dark:bg-sky-900/40 dark:text-sky-50">
                                    {{ $transmissao }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if (!empty($doenca->doe_medidas_controle))
                    <div class="rounded-lg border border-violet-200/90 bg-violet-50/55 p-3 dark:border-violet-800/45 dark:bg-violet-950/25">
                        <p class="text-xs font-semibold uppercase tracking-wide text-violet-900/90 dark:text-violet-100/90">{{ __('Medidas de controle') }}</p>
                        <div class="mt-2 flex flex-wrap gap-2">
                            @foreach ($doenca->doe_medidas_controle as $medida)
                                <span class="inline-block rounded-md border border-violet-300/80 bg-violet-100/90 px-2 py-0.5 text-xs font-medium text-violet-950 dark:border-violet-600/60 dark:bg-violet-900/40 dark:text-violet-50">
                                    {{ $medida }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @endforeach
    @else
        <p class="text-sm italic text-slate-500 dark:text-slate-400">
            {{ __('Nenhuma doença foi identificada ou registrada durante esta visita.') }}
        </p>
    @endif
</x-section-card>

{{-- Depósitos Inspecionados --}}
<x-section-card class="space-y-5 rounded-2xl border border-slate-200/80 bg-white/95 p-5 shadow-sm dark:border-slate-700/70 dark:bg-slate-900/50">
    <div class="border-b border-slate-200/80 pb-3 dark:border-slate-700/70">
        <h2 class="text-lg font-semibold text-slate-800 dark:text-slate-100">{{ __('Depósitos inspecionados') }}</h2>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        @foreach (['a1','a2','b','c','d1','d2','e'] as $tipo)
            <div class="rounded-2xl border border-slate-200/80 bg-slate-50/70 p-4 text-center shadow-sm dark:border-slate-700/70 dark:bg-slate-900/40">
                <p class="text-xs font-semibold uppercase text-slate-500 dark:text-slate-400">{{ strtoupper($tipo) }}</p>
                <p class="text-xl font-bold text-slate-900 dark:text-slate-100">{{ $visita->{'insp_'.$tipo} ?? 0 }}</p>
            </div>
        @endforeach
        <div class="col-span-2 rounded-2xl border border-emerald-200/80 bg-emerald-50/80 p-4 text-center text-lg font-bold text-emerald-900 shadow-sm dark:border-emerald-700/60 dark:bg-emerald-950/35 dark:text-emerald-100 sm:col-span-4">
            {{ __('Eliminados: :n', ['n' => $visita->vis_depositos_eliminados ?? 0]) }}
        </div>
    </div>
</x-section-card>

{{-- Coleta de Amostra --}}
@if ($visita->vis_coleta_amostra)
<x-section-card class="space-y-4 rounded-2xl border border-slate-200/80 bg-white/95 p-5 shadow-sm dark:border-slate-700/70 dark:bg-slate-900/50">
    <div class="border-b border-slate-200/80 pb-3 dark:border-slate-700/70">
        <h2 class="text-lg font-semibold text-slate-800 dark:text-slate-100">{{ __('Coleta de amostra') }}</h2>
    </div>
    <dl class="grid grid-cols-1 gap-4 text-sm text-slate-700 dark:text-slate-300 sm:grid-cols-3">
        <div>
            <dt class="text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">{{ __('Nº inicial') }}</dt>
            <dd>{{ $visita->vis_amos_inicial }}</dd>
        </div>
        <div>
            <dt class="text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">{{ __('Nº final') }}</dt>
            <dd>{{ $visita->vis_amos_final }}</dd>
        </div>
        <div>
            <dt class="text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">{{ __('Tubitos') }}</dt>
            <dd>{{ $visita->vis_qtd_tubitos }}</dd>
        </div>
    </dl>
</x-section-card>
@endif

<x-section-card class="space-y-4 rounded-2xl border border-slate-200/80 bg-white/95 p-5 shadow-sm dark:border-slate-700/70 dark:bg-slate-900/50">
    <div class="border-b border-slate-200/80 pb-3 dark:border-slate-700/70">
        <h2 class="text-lg font-semibold text-slate-800 dark:text-slate-100">{{ __('Tratamentos realizados') }}</h2>
    </div>

    @if ($visita->tratamentos && count($visita->tratamentos))
        @foreach ($visita->tratamentos as $t)
            <div class="space-y-1 rounded-2xl border border-slate-200/80 bg-slate-50/70 p-4 text-sm text-slate-800 shadow-sm dark:border-slate-700/70 dark:bg-slate-900/40 dark:text-slate-100">
                @if (!empty($t->trat_forma))
                    <p><strong>{{ __('Forma') }}:</strong> {{ $t->trat_forma }}</p>
                @endif
                @if (!empty($t->trat_tipo))
                    <p><strong>{{ __('Tipo') }}:</strong> {{ $t->trat_tipo }}</p>
                @endif
                @if (!is_null($t->linha))
                    <p><strong>{{ __('Linha') }}:</strong> {{ $t->linha }}</p>
                @endif
                @if (!is_null($t->qtd_gramas))
                    <p><strong>{{ __('Gramas') }}:</strong> {{ $t->qtd_gramas }}</p>
                @endif
                @if (!is_null($t->qtd_depositos_tratados))
                    <p><strong>{{ __('Depósitos tratados') }}:</strong> {{ $t->qtd_depositos_tratados }}</p>
                @endif
                @if (!is_null($t->qtd_cargas))
                    <p><strong>{{ __('Cargas') }}:</strong> {{ $t->qtd_cargas }}</p>
                @endif
            </div>
        @endforeach
    @else
        <p class="text-sm italic text-slate-500 dark:text-slate-400">
            {{ __('Nenhum tratamento foi realizado durante esta visita.') }}
        </p>
    @endif
</x-section-card>
