@php
    $locTipoLabels = ['R' => __('Residencial'), 'C' => __('Comercial'), 'T' => __('Terreno baldio')];
    $locZonaLabels = ['U' => __('Urbana'), 'R' => __('Rural')];
    $na = __('N/I');
@endphp
{{-- Local --}}
<x-section-card class="dark:bg-gray-800 space-y-6">
    <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 border-b pb-2">{{ __('Local visitado') }}</h2>
    <dl class="grid grid-cols-1 sm:grid-cols-4 gap-x-6 gap-y-4 text-sm text-gray-700 dark:text-gray-300">
        <div>
            <dt class="font-medium">{{ __('Código único do imóvel') }}</dt>
            <dd class="mt-1">
                <span class="inline-block rounded bg-slate-100 px-2 py-1 font-mono text-xs font-semibold tracking-tight text-slate-800 dark:bg-slate-700 dark:text-slate-200">
                    {{ $visita->local->loc_codigo_unico }}
                </span>
            </dd>
        </div>
        <div>
            <dt class="font-medium">{{ __('Código da localidade') }}</dt>
            <dd class="mt-1">{{ $visita->local->loc_codigo ?? $na }}</dd>
        </div>
        <div>
            <dt class="font-medium">{{ __('Categoria da localidade') }}</dt>
            <dd class="mt-1">{{ $visita->local->loc_categoria ?? $na }}</dd>
        </div>
        <div>
            <dt class="font-medium">{{ __('Tipo') }}</dt>
            <dd class="mt-1">{{ $locTipoLabels[$visita->local->loc_tipo] ?? $na }}</dd>
        </div>
        <div>
            <dt class="font-medium">{{ __('Zona') }}</dt>
            <dd class="mt-1">{{ $locZonaLabels[$visita->local->loc_zona] ?? $na }}</dd>
        </div>
        <div>
            <dt class="font-medium">{{ __('Quarteirão') }}</dt>
            <dd class="mt-1">{{ $visita->local->loc_quarteirao ?? $na }}</dd>
        </div>
        <div>
            <dt class="font-medium">{{ __('Sequência') }}</dt>
            <dd class="mt-1">{{ $visita->local->loc_sequencia ?? $na }}</dd>
        </div>
        <div>
            <dt class="font-medium">{{ __('Lado') }}</dt>
            <dd class="mt-1">{{ $visita->local->loc_lado ?? $na }}</dd>
        </div>
        <div class="sm:col-span-4" style="padding-top: 0.5rem;">
            <dt class="font-medium">{{ __('Endereço completo') }}</dt>
            <dd class="mt-1 text-gray-900 dark:text-gray-100">
                {{ $visita->local->loc_endereco }}, {{ $visita->local->loc_numero ?? __('S/N') }} - {{ $visita->local->loc_bairro }}, {{ $visita->local->loc_cidade }}/{{ $visita->local->loc_estado }} - {{ $visita->local->loc_pais }} | {{ __('CEP') }}: {{ $visita->local->loc_cep }}<br>
                <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('Complemento') }}: {{ $visita->local->loc_complemento ?? $na }}</span>
            </dd>
        </div>
        <div class="sm:col-span-4">
            <dt class="font-medium">{{ __('Responsável pelo imóvel') }}</dt>
            <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ $visita->local->loc_responsavel_nome ?? __('Não informado') }}</dd>
        </div>
    </dl>

    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-sm text-gray-700 dark:text-gray-300">
        <div>
            <dt class="font-medium">{{ __('Latitude') }}</dt>
            <dd class="mt-1">{{ $visita->local->loc_latitude }}</dd>
        </div>
        <div>
            <dt class="font-medium">{{ __('Longitude') }}</dt>
            <dd class="mt-1">{{ $visita->local->loc_longitude }}</dd>
        </div>
    </dl>

    <div>
        <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-2">{{ __('Localização no mapa') }}</h3>
        <div id="map" class="h-72 rounded-md shadow border border-gray-300"></div>
        <p class="text-sm mt-2 text-gray-600 dark:text-gray-400 italic">
            {{ __('A posição exibida é baseada nas coordenadas fornecidas.') }}
        </p>
    </div>
</x-section-card>

{{-- Dados da Visita --}}
<x-section-card class="dark:bg-gray-800 space-y-6">
    <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 border-b pb-2">{{ __('Dados da visita') }}</h2>
    <dl class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm text-gray-700 dark:text-gray-300">
        <div>
            <dt class="font-medium">{{ __('Código da visita') }}</dt>
            <dd class="mt-1">
                <span class="inline-block rounded bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-800 dark:bg-slate-700 dark:text-slate-200">
                    #{{ $visita->vis_id }}
                </span>
            </dd>
        </div>
        <div>
            <dt class="font-medium">{{ __('Data') }}</dt>
            <dd>{{ \Carbon\Carbon::parse($visita->vis_data)->format('d/m/Y') }}</dd>
        </div>
        <div>
            <dt class="font-medium">{{ __('Ciclo/ano') }}</dt>
            <dd>{{ $visita->vis_ciclo ?? $na }}</dd>
        </div>
        <div>
            <dt class="font-medium">{{ __('Atividade') }}</dt>
            <dd>
                @if(\App\Helpers\MsTerminologia::atividadeCodigo($visita->vis_atividade))
                    {{ \App\Helpers\MsTerminologia::atividadeCodigo($visita->vis_atividade) }} · {{ \App\Helpers\MsTerminologia::atividadeNome($visita->vis_atividade) }}
                @else
                    {{ __('Não informado') }}
                @endif
            </dd>
        </div>
        <div>
            <dt class="font-medium">{{ __('Tipo de visita') }}</dt>
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
            <dt class="font-medium">{{ __('Pendências') }}</dt>
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
<x-section-card class="dark:bg-gray-800 space-y-2">
    <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 border-b pb-2">{{ __('Observações') }}</h2>
    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $visita->vis_observacoes ?: __('Nenhuma observação registrada.') }}</p>
</x-section-card>

@php
    $obsOcup = $visita->vis_ocupantes_observacoes;
    $obsOcup = is_array($obsOcup) ? $obsOcup : [];
@endphp
@if(count($obsOcup) > 0)
    <x-section-card class="dark:bg-gray-800 space-y-3">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 border-b pb-2">{{ __('Ocupantes (nesta visita)') }}</h2>
        <ul class="space-y-3">
            @foreach($obsOcup as $mid => $txt)
                @if(trim((string) $txt) === '')
                    @continue
                @endif
                @php $mor = $visita->local->moradores->firstWhere('mor_id', (int) $mid); @endphp
                <li class="rounded-md border border-gray-200 p-3 dark:border-gray-600">
                    <p class="text-xs font-semibold text-gray-600 dark:text-gray-300">{{ $mor && $mor->mor_nome ? $mor->mor_nome : __('Ocupante #:id', ['id' => $mid]) }}</p>
                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-100 whitespace-pre-wrap">{{ $txt }}</p>
                </li>
            @endforeach
        </ul>
    </x-section-card>
@endif

{{-- Doenças --}}
<x-section-card class="dark:bg-gray-800 space-y-4">
    <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 border-b pb-2">{{ __('Doenças monitoradas na visita') }}</h2>

    @if ($visita->doencas->count())
        @foreach ($visita->doencas as $doenca)
            <div class="space-y-3 rounded-xl border border-slate-200/90 bg-white p-4 dark:border-slate-600 dark:bg-gray-800/80">
                <h3 class="text-md font-semibold text-slate-900 dark:text-slate-100">{{ $doenca->doe_nome }}</h3>

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
        <p class="italic text-gray-500 dark:text-gray-400 text-sm">
            {{ __('Nenhuma doença foi identificada ou registrada durante esta visita.') }}
        </p>
    @endif
</x-section-card>

{{-- Depósitos Inspecionados --}}
<x-section-card class="dark:bg-gray-800 space-y-6">
    <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 border-b pb-2">{{ __('Depósitos inspecionados') }}</h2>
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        @foreach (['a1','a2','b','c','d1','d2','e'] as $tipo)
            <div class="bg-gray-100 dark:bg-gray-800 p-4 rounded shadow text-center">
                <p class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">{{ strtoupper($tipo) }}</p>
                <p class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ $visita->{'insp_'.$tipo} ?? 0 }}</p>
            </div>
        @endforeach
        <div class="col-span-2 rounded-lg bg-emerald-100 p-4 text-center text-lg font-bold text-emerald-900 shadow dark:bg-emerald-900/45 dark:text-emerald-100 sm:col-span-4">
            {{ __('Eliminados: :n', ['n' => $visita->vis_depositos_eliminados ?? 0]) }}
        </div>
    </div>
</x-section-card>

{{-- Coleta de Amostra --}}
@if ($visita->vis_coleta_amostra)
<x-section-card class="dark:bg-gray-800 space-y-4">
    <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 border-b pb-2">{{ __('Coleta de amostra') }}</h2>
    <dl class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm text-gray-700 dark:text-gray-300">
        <div>
            <dt class="font-medium">{{ __('Nº inicial') }}</dt>
            <dd>{{ $visita->vis_amos_inicial }}</dd>
        </div>
        <div>
            <dt class="font-medium">{{ __('Nº final') }}</dt>
            <dd>{{ $visita->vis_amos_final }}</dd>
        </div>
        <div>
            <dt class="font-medium">{{ __('Tubitos') }}</dt>
            <dd>{{ $visita->vis_qtd_tubitos }}</dd>
        </div>
    </dl>
</x-section-card>
@endif

<x-section-card class="dark:bg-gray-800 space-y-4">
    <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 border-b pb-2">{{ __('Tratamentos realizados') }}</h2>

    @if ($visita->tratamentos && count($visita->tratamentos))
        @foreach ($visita->tratamentos as $t)
            <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-md border border-gray-300 dark:border-gray-600 text-sm text-gray-800 dark:text-gray-100 space-y-1">
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
        <p class="text-sm text-gray-600 dark:text-gray-300 italic">
            {{ __('Nenhum tratamento foi realizado durante esta visita.') }}
        </p>
    @endif
</x-section-card>
