{{-- Painel com dados identificáveis e socioeconômicos; apenas gestor, ficha do imóvel após busca em Locais. --}}
@php
    $cfg = config('visitaai_municipio.ocupantes', []);
    $escL = config('visitaai_municipio.escolaridade_opcoes', []);
    $rendaL = config('visitaai_municipio.renda_faixa_opcoes', []);
    $corL = config('visitaai_municipio.cor_raca_opcoes', []);
    $trabL = config('visitaai_municipio.situacao_trabalho_opcoes', []);
    $profile = $profile ?? (auth()->user()?->isGestor() ? 'gestor' : 'agente');
    $local->loadMissing(['moradores', 'visitas']);
    $porMorId = $local->moradores->keyBy('mor_id');
@endphp
<x-section-card class="border-slate-200/90 bg-slate-50/70 text-sm dark:border-slate-700/80 dark:bg-slate-900/35">
    <h2 class="text-base font-semibold text-slate-900 dark:text-slate-100">{{ $cfg['painel_sensivel_gestor_titulo'] ?? '' }}</h2>
    @if(filled(trim((string) ($cfg['painel_sensivel_gestor_texto'] ?? ''))))
        <x-ui.disclosure variant="lead-mt">
            <x-slot name="summary">
                <span class="border-b border-dotted border-slate-500/45 pb-px dark:border-slate-400/45">{{ __('Orientação de uso e confidencialidade deste painel') }}</span>
            </x-slot>
            <p class="text-xs leading-relaxed">{{ $cfg['painel_sensivel_gestor_texto'] }}</p>
        </x-ui.disclosure>
    @endif


    @if($local->moradores->isEmpty())
        <p class="mt-3 text-sm text-slate-600 dark:text-slate-400">{{ __('Nenhum ocupante cadastrado neste imóvel.') }}</p>
    @else
        <div x-data="{ query: '' }" class="mt-4 space-y-3">
            <div class="v-list-toolbar !p-3 sm:!p-4">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                    <div class="min-w-0 flex-1">
                        <label for="moradores-search-local" class="v-toolbar-label">{{ __('Pesquisar ocupantes') }}</label>
                        <input id="moradores-search-local" type="text" x-model="query" class="v-input" placeholder="{{ __('Nome, escolaridade, renda, trabalho...') }}">
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        @if(! empty($fichaPdfUrl))
                            <a href="{{ $fichaPdfUrl }}" class="v-btn-export v-btn-export--pdf inline-flex no-underline">
                                <x-heroicon-o-document-arrow-down class="h-4 w-4 shrink-0" aria-hidden="true" />
                                {{ __('Baixar ficha socioeconômica (imóvel)') }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="v-table-wrap rounded-lg border border-slate-200/90 dark:border-slate-700/80">
            <table class="v-data-table">
                <thead>
                    <tr class="bg-slate-100/95 text-left dark:bg-slate-800/70">
                        <th class="whitespace-nowrap px-3 py-2.5 text-left text-[10px] font-semibold uppercase tracking-[0.06em] text-slate-700 dark:text-slate-300">ID</th>
                        <th class="px-3 py-2.5 text-left text-[10px] font-semibold uppercase tracking-[0.06em] text-slate-700 dark:text-slate-300">{{ __('Nome') }}</th>
                        <th class="px-3 py-2.5 text-left text-[10px] font-semibold uppercase tracking-[0.06em] text-slate-700 dark:text-slate-300">{{ __('Nascimento') }}</th>
                        <th class="px-3 py-2.5 text-left text-[10px] font-semibold uppercase tracking-[0.06em] text-slate-700 dark:text-slate-300">{{ __('Escolaridade') }}</th>
                        <th class="px-3 py-2.5 text-left text-[10px] font-semibold uppercase tracking-[0.06em] text-slate-700 dark:text-slate-300">{{ __('Renda') }}</th>
                        <th class="px-3 py-2.5 text-left text-[10px] font-semibold uppercase tracking-[0.06em] text-slate-700 dark:text-slate-300">{{ __('Cor/raça') }}</th>
                        <th class="px-3 py-2.5 text-left text-[10px] font-semibold uppercase tracking-[0.06em] text-slate-700 dark:text-slate-300">{{ __('Trabalho') }}</th>
                        <th class="px-3 py-2.5 text-left text-[10px] font-semibold uppercase tracking-[0.06em] text-slate-700 dark:text-slate-300">{{ __('Obs.') }}</th>
                        <th class="px-3 py-2.5 text-right text-[10px] font-semibold uppercase tracking-[0.06em] text-slate-700 dark:text-slate-300">{{ __('Ficha') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                    @foreach($local->moradores->sortBy('mor_id') as $m)
                        @php
                            $search = 
                                mb_strtolower(trim(implode(' ', [
                                    $m->mor_nome ?? '',
                                    $escL[$m->mor_escolaridade] ?? $m->mor_escolaridade ?? '',
                                    $rendaL[$m->mor_renda_faixa] ?? $m->mor_renda_faixa ?? '',
                                    $trabL[$m->mor_situacao_trabalho] ?? $m->mor_situacao_trabalho ?? '',
                                    $m->mor_observacao ?? '',
                                ])));
                        @endphp
                        <tr class="bg-white/90 dark:bg-slate-900/40" data-search="{{ $search }}" x-show="!query || ($el.dataset.search && $el.dataset.search.includes(query.toLowerCase()))">
                            <td class="whitespace-nowrap px-3 py-2.5 font-mono text-xs text-slate-800 dark:text-slate-200">{{ $m->mor_id }}</td>
                            <td class="max-w-[12rem] truncate px-3 py-2.5 text-slate-900 dark:text-slate-100" title="{{ $m->mor_nome }}">{{ $m->mor_nome ?: __('N/D') }}</td>
                            <td class="whitespace-nowrap px-3 py-2.5 text-slate-800 dark:text-slate-200">{{ $m->mor_data_nascimento?->format('d/m/Y') ?? __('N/D') }}</td>
                            <td class="max-w-[10rem] px-3 py-2.5 text-slate-800 dark:text-slate-200">{{ $escL[$m->mor_escolaridade] ?? ($m->mor_escolaridade ?? __('N/D')) }}</td>
                            <td class="max-w-[10rem] px-3 py-2.5 text-slate-800 dark:text-slate-200">{{ $rendaL[$m->mor_renda_faixa] ?? ($m->mor_renda_faixa ?? __('N/D')) }}</td>
                            <td class="max-w-[8rem] px-3 py-2.5 text-slate-800 dark:text-slate-200">{{ $corL[$m->mor_cor_raca] ?? ($m->mor_cor_raca ?? __('N/D')) }}</td>
                            <td class="max-w-[10rem] px-3 py-2.5 text-slate-800 dark:text-slate-200">{{ $trabL[$m->mor_situacao_trabalho] ?? ($m->mor_situacao_trabalho ?? __('N/D')) }}</td>
                            <td class="max-w-[14rem] whitespace-pre-wrap px-3 py-2.5 text-xs text-slate-800 dark:text-slate-200">{{ $m->mor_observacao ?: __('N/D') }}</td>
                            <td class="whitespace-nowrap px-3 py-2.5 text-right">
                                <a href="{{ route($profile . '.locais.moradores.ficha-socioeconomica-pdf', [$local, $m]) }}"
                                   class="inline-flex items-center gap-1 rounded-md border border-slate-300 bg-white px-2 py-1 text-[11px] font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
                                    <x-heroicon-o-document-arrow-down class="h-3.5 w-3.5" />
                                    {{ __('PDF') }}
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
        </div>
    @endif

    @php
        $visitasComObs = $local->visitas->filter(fn ($v) => is_array($v->vis_ocupantes_observacoes) && count($v->vis_ocupantes_observacoes) > 0);
    @endphp
    @if($visitasComObs->isNotEmpty())
        <h3 class="mt-6 text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $cfg['visitas_ocupantes_titulo'] ?? __('Visitas') }}</h3>
        <ul class="mt-2 space-y-4 text-xs text-slate-800 dark:text-slate-200 sm:text-sm">
            @foreach($visitasComObs as $vis)
                <li class="rounded-lg border border-slate-200/80 bg-white/70 p-3 dark:border-slate-700/80 dark:bg-slate-900/40">
                    <p class="font-medium text-slate-900 dark:text-slate-100">
                        {{ __('Visita') }} #{{ $vis->vis_id }}, {{ $vis->vis_data ? \Carbon\Carbon::parse($vis->vis_data)->format('d/m/Y') : __('N/D') }}
                    </p>
                    <ul class="mt-2 space-y-1.5 border-t border-slate-100 pt-2 dark:border-slate-700/80">
                        @foreach($vis->vis_ocupantes_observacoes as $mid => $texto)
                            @php $midInt = (int) $mid; $nomeMor = optional($porMorId->get($midInt))->mor_nome; @endphp
                            <li class="text-slate-800 dark:text-slate-200">
                                <span class="font-mono text-[11px] text-slate-500 dark:text-slate-400">#{{ $midInt }}</span>
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
