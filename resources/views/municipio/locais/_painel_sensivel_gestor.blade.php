{{-- Painel com dados identificáveis e socioeconômicos; apenas gestor, ficha do imóvel após busca em Locais. --}}
@php
    $cfg = config('visitaai_municipio.ocupantes', []);
    $escL = config('visitaai_municipio.escolaridade_opcoes', []);
    $rendaL = config('visitaai_municipio.renda_faixa_opcoes', []);
    $corL = config('visitaai_municipio.cor_raca_opcoes', []);
    $trabL = config('visitaai_municipio.situacao_trabalho_opcoes', []);
    $local->loadMissing(['moradores', 'visitas']);
    $porMorId = $local->moradores->keyBy('mor_id');
@endphp
<x-section-card class="border-rose-200/90 bg-rose-50/80 text-sm dark:border-rose-900/80 dark:bg-rose-950/40">
    <h2 class="text-base font-semibold text-rose-950 dark:text-rose-100">{{ $cfg['painel_sensivel_gestor_titulo'] ?? '' }}</h2>
    @if(filled(trim((string) ($cfg['painel_sensivel_gestor_texto'] ?? ''))))
        <x-ui.disclosure variant="rose" class="mt-2">
            <x-slot name="summary">
                <span class="border-b border-dotted border-rose-700/50 pb-px dark:border-rose-400/50">{{ __('Orientação de uso e confidencialidade deste painel') }}</span>
            </x-slot>
            <p class="text-xs leading-relaxed">{{ $cfg['painel_sensivel_gestor_texto'] }}</p>
        </x-ui.disclosure>
    @endif


    @if($local->moradores->isEmpty())
        <p class="mt-3 text-sm text-rose-800/80 dark:text-rose-200/80">{{ __('Nenhum ocupante cadastrado neste imóvel.') }}</p>
    @else
        <div class="mt-4 overflow-x-auto rounded-lg ring-1 ring-rose-200/80 dark:ring-rose-800/80">
            <table class="min-w-full border-collapse text-[13px] leading-snug text-rose-950 dark:text-rose-50">
                <thead>
                    <tr class="bg-rose-100/90 text-left dark:bg-rose-950/80">
                        <th class="whitespace-nowrap px-3 py-2.5 text-left text-[10px] font-semibold uppercase tracking-[0.06em] text-rose-900 dark:text-rose-200">ID</th>
                        <th class="px-3 py-2.5 text-left text-[10px] font-semibold uppercase tracking-[0.06em] text-rose-900 dark:text-rose-200">{{ __('Nome') }}</th>
                        <th class="px-3 py-2.5 text-left text-[10px] font-semibold uppercase tracking-[0.06em] text-rose-900 dark:text-rose-200">{{ __('Nascimento') }}</th>
                        <th class="px-3 py-2.5 text-left text-[10px] font-semibold uppercase tracking-[0.06em] text-rose-900 dark:text-rose-200">{{ __('Escolaridade') }}</th>
                        <th class="px-3 py-2.5 text-left text-[10px] font-semibold uppercase tracking-[0.06em] text-rose-900 dark:text-rose-200">{{ __('Renda') }}</th>
                        <th class="px-3 py-2.5 text-left text-[10px] font-semibold uppercase tracking-[0.06em] text-rose-900 dark:text-rose-200">{{ __('Cor/raça') }}</th>
                        <th class="px-3 py-2.5 text-left text-[10px] font-semibold uppercase tracking-[0.06em] text-rose-900 dark:text-rose-200">{{ __('Trabalho') }}</th>
                        <th class="px-3 py-2.5 text-left text-[10px] font-semibold uppercase tracking-[0.06em] text-rose-900 dark:text-rose-200">{{ __('Obs.') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-rose-100 dark:divide-rose-900/50">
                    @foreach($local->moradores->sortBy('mor_id') as $m)
                        <tr class="bg-white/90 dark:bg-gray-900/50">
                            <td class="whitespace-nowrap px-3 py-2.5 font-mono text-xs text-rose-900 dark:text-rose-100/90">{{ $m->mor_id }}</td>
                            <td class="max-w-[12rem] truncate px-3 py-2.5 text-rose-950 dark:text-rose-50" title="{{ $m->mor_nome }}">{{ $m->mor_nome ?: __('N/D') }}</td>
                            <td class="whitespace-nowrap px-3 py-2.5 text-rose-900 dark:text-rose-100/90">{{ $m->mor_data_nascimento?->format('d/m/Y') ?? __('N/D') }}</td>
                            <td class="max-w-[10rem] px-3 py-2.5 text-rose-900 dark:text-rose-100/90">{{ $escL[$m->mor_escolaridade] ?? ($m->mor_escolaridade ?? __('N/D')) }}</td>
                            <td class="max-w-[10rem] px-3 py-2.5 text-rose-900 dark:text-rose-100/90">{{ $rendaL[$m->mor_renda_faixa] ?? ($m->mor_renda_faixa ?? __('N/D')) }}</td>
                            <td class="max-w-[8rem] px-3 py-2.5 text-rose-900 dark:text-rose-100/90">{{ $corL[$m->mor_cor_raca] ?? ($m->mor_cor_raca ?? __('N/D')) }}</td>
                            <td class="max-w-[10rem] px-3 py-2.5 text-rose-900 dark:text-rose-100/90">{{ $trabL[$m->mor_situacao_trabalho] ?? ($m->mor_situacao_trabalho ?? __('N/D')) }}</td>
                            <td class="max-w-[14rem] whitespace-pre-wrap px-3 py-2.5 text-xs text-rose-900 dark:text-rose-100/85">{{ $m->mor_observacao ?: __('N/D') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    @php
        $visitasComObs = $local->visitas->filter(fn ($v) => is_array($v->vis_ocupantes_observacoes) && count($v->vis_ocupantes_observacoes) > 0);
    @endphp
    @if($visitasComObs->isNotEmpty())
        <h3 class="mt-6 text-sm font-semibold text-rose-950 dark:text-rose-100">{{ $cfg['visitas_ocupantes_titulo'] ?? __('Visitas') }}</h3>
        <ul class="mt-2 space-y-4 text-xs text-rose-900 dark:text-rose-100/90 sm:text-sm">
            @foreach($visitasComObs as $vis)
                <li class="rounded-lg border border-rose-200/70 bg-white/60 p-3 dark:border-rose-800/60 dark:bg-gray-900/40">
                    <p class="font-medium text-rose-950 dark:text-rose-50">
                        {{ __('Visita') }} #{{ $vis->vis_id }}, {{ $vis->vis_data ? \Carbon\Carbon::parse($vis->vis_data)->format('d/m/Y') : __('N/D') }}
                    </p>
                    <ul class="mt-2 space-y-1.5 border-t border-rose-100/80 pt-2 dark:border-rose-800/50">
                        @foreach($vis->vis_ocupantes_observacoes as $mid => $texto)
                            @php $midInt = (int) $mid; $nomeMor = optional($porMorId->get($midInt))->mor_nome; @endphp
                            <li class="text-rose-900/95 dark:text-rose-100/90">
                                <span class="font-mono text-[11px] text-rose-700 dark:text-rose-300">#{{ $midInt }}</span>
                                @if(filled($nomeMor))
                                    <span class="font-medium">: {{ $nomeMor }}</span>
                                @endif
                                <span class="block mt-0.5 whitespace-pre-wrap text-xs">{{ $texto }}</span>
                            </li>
                        @endforeach
                    </ul>
                </li>
            @endforeach
        </ul>
    @endif
</x-section-card>
