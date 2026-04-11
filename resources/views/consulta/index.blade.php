@extends('layouts.app')

@section('public')
@endsection

@section('og_title', config('app.name') . ' · ' . __('Consulta pública'))
@section('og_description', __('Consulta pública pelo código do imóvel: transparência alimentada pelos mesmos registros usados nos indicadores municipais. Gratuita, sem cadastro e sem dados clínicos. Visitas de campo e vigilância em saúde conforme a operação local. Cadastro complementar do imóvel não aparece aqui.'))

@section('content')
<div class="welcome-public welcome-public--extend w-full min-w-0">
    <div class="public-page__stack">
        <x-public.page-hero :kicker="__('Indicadores municipais · transparência ao cidadão')">
            <x-slot name="heading">
                <div class="flex flex-wrap items-center justify-between gap-x-3 gap-y-2">
                    <h1 class="welcome-public__title min-w-0 shrink">
                        {{ __('Verificar visitas no imóvel') }}
                    </h1>
                    <x-public-municipality-pill :local="$localPrimario ?? null" class="shrink-0" />
                </div>
            </x-slot>
            <x-slot name="leadFull">
                <p class="welcome-public__lead welcome-public__lead--full">
                    {{ __('Na consulta pública aparecem apenas endereço vinculado ao código, datas e status das visitas de campo e informações já divulgadas sobre doenças. Não são exibidos dados clínicos, nome de ocupantes nem cadastro socioeconômico do imóvel.') }}
                </p>
            </x-slot>
            <x-slot name="actions">
                <a href="{{ url('/') }}" class="welcome-public__link">
                    <x-heroicon-o-arrow-left class="h-3.5 w-3.5 shrink-0 opacity-80" aria-hidden="true" />
                    {{ __('Voltar à página inicial') }}
                </a>
            </x-slot>
        </x-public.page-hero>

        <x-flash-alerts />

        <section class="welcome-public__surface public-surface-form flex flex-col p-6" aria-labelledby="consulta-codigo-titulo">
            <h2 id="consulta-codigo-titulo" class="public-card-eyebrow">{{ __('Código do imóvel') }}</h2>
            <form action="{{ route('consulta.codigo') }}" method="GET" id="consulta-codigo-form" class="mt-3 flex flex-col gap-4">
                <div>
                    <label for="codigo" class="public-form-label">
                        {!! __('Informe o <strong>código numérico</strong> que consta no comprovante ou foi passado pelo ACE ou ACS na visita.') !!}
                        <span class="text-red-500" aria-hidden="true">*</span>
                    </label>
                    <div class="mt-2 flex flex-col gap-3 sm:flex-row sm:items-stretch">
                        <input
                            type="text"
                            id="codigo"
                            name="codigo"
                            value="{{ old('codigo') }}"
                            placeholder="{{ __('Ex.: 12345678') }}"
                            required
                            inputmode="numeric"
                            pattern="[0-9]{8}"
                            maxlength="8"
                            autocomplete="off"
                            class="v-input max-w-md w-full"
                            aria-describedby="codigo-ajuda"
                        />
                        <button type="submit" id="consulta-codigo-btn" class="v-btn-primary inline-flex min-h-[2.875rem] shrink-0 items-center justify-center px-5 text-sm font-medium sm:w-auto">
                            {{ __('Consultar agora') }}
                        </button>
                    </div>
                </div>
                @error('codigo')
                    <p class="text-sm text-red-600 dark:text-red-400" role="alert">{{ $message }}</p>
                @enderror
                <p id="codigo-ajuda" class="text-sm leading-relaxed text-slate-600 dark:text-slate-400">
                    {{ __('Cada imóvel cadastrado recebe um código exclusivo. Se não tiver o número em mãos, peça orientação na Secretaria Municipal de Saúde ou ao agente no próximo contato.') }}
                </p>
            </form>
        </section>

        <section class="welcome-public__surface flex flex-col overflow-hidden" aria-labelledby="consulta-doencas-titulo">
            <div class="p-6">
                <h2 id="consulta-doencas-titulo" class="public-card-eyebrow">{{ __('Doenças monitoradas no município') }}</h2>
                @if($doencasIndisponivel ?? false)
                    <div class="mt-3 space-y-2 text-sm leading-relaxed">
                        <p class="font-medium text-amber-900 dark:text-amber-100">{{ __('Serviço temporariamente indisponível') }}</p>
                        <p class="text-slate-600 dark:text-slate-400">{{ __('As orientações sobre doenças não puderam ser exibidas. Use o formulário acima para verificar visitas no seu imóvel.') }}</p>
                    </div>
                @elseif($doencas->isEmpty())
                    <div class="mt-3 space-y-2 text-sm leading-relaxed">
                        <p class="font-medium text-slate-800 dark:text-slate-200">{{ __('Nenhuma doença cadastrada no momento') }}</p>
                        <p class="text-slate-600 dark:text-slate-400">{{ __('Volte mais tarde ou procure a Secretaria Municipal de Saúde para orientações gerais sobre dengue e outras arboviroses.') }}</p>
                    </div>
                @else
                    <p class="public-card-text">{{ __('Sintomas, transmissão e medidas de controle informados pelo município.') }}</p>
                @endif
            </div>

            @if(!($doencasIndisponivel ?? false) && !$doencas->isEmpty())
                <div class="border-t border-slate-200/50 dark:border-slate-700/50">
                    <div class="v-table-meta border-t-0 border-slate-200/80 dark:border-slate-700/80">
                        <span>{{ __(':n doença(s) cadastrada(s) para consulta.', ['n' => $doencas->count()]) }}</span>
                    </div>
                    <div class="v-table-wrap">
                        <table class="v-data-table">
                            <thead>
                                <tr>
                                    <th scope="col" class="text-left">{{ __('Doença') }}</th>
                                    <th scope="col" class="text-left">{{ __('Sintomas') }}</th>
                                    <th scope="col" class="text-left">{{ __('Transmissão') }}</th>
                                    <th scope="col" class="text-left">{{ __('Medidas de controle') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($doencas as $doenca)
                                    <tr>
                                        <td class="font-medium text-slate-900 dark:text-slate-100">{{ $doenca->doe_nome }}</td>
                                        <td class="text-slate-600 dark:text-slate-300">
                                            {{ is_array($doenca->doe_sintomas) ? implode(', ', $doenca->doe_sintomas) : $doenca->doe_sintomas }}
                                        </td>
                                        <td class="text-slate-600 dark:text-slate-300">
                                            {{ is_array($doenca->doe_transmissao) ? implode(', ', $doenca->doe_transmissao) : $doenca->doe_transmissao }}
                                        </td>
                                        <td class="text-slate-600 dark:text-slate-300">
                                            {{ is_array($doenca->doe_medidas_controle) ? implode(', ', $doenca->doe_medidas_controle) : $doenca->doe_medidas_controle }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </section>

        @include('partials.public-copyright-footer', ['footerClass' => 'welcome-public__footer'])
    </div>
</div>
@endsection

@push('scripts')
<script type="application/json" id="consulta-index-config">
@json(['searching' => __('Buscando…')])
</script>
@vite(['resources/js/consulta-index.js'])
@endpush
