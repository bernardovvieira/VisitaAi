@extends('layouts.app')

@section('public')
@endsection

@section('og_title', config('app.name') . ' · ' . __('Consulta pública'))
@section('og_description', __('Consulta pública pelo código do imóvel: transparência alimentada pelos mesmos registros usados nos indicadores municipais. Gratuita, sem cadastro e sem dados clínicos. Visitas de campo e vigilância em saúde conforme a operação local. Cadastro complementar do imóvel não aparece aqui.'))

@section('content')
<div class="welcome-public welcome-public--extend w-full min-w-0">
    <div class="public-page__stack">
        <header class="welcome-public__hero">
            <div class="welcome-public__hero-row">
                <div class="public-page__hero-aside">
                    <img
                        src="{{ asset('images/visitaai_rembg.png') }}"
                        alt="{{ __('Marca do aplicativo') }}, {{ config('app.brand') }}"
                        width="80"
                        height="80"
                        class="welcome-public__logo"
                        decoding="async" />
                </div>
                <div class="welcome-public__hero-content">
                    <p class="welcome-public__kicker">
                        {{ __('Indicadores municipais · transparência ao cidadão') }}
                    </p>
                    <div class="flex flex-wrap items-center justify-between gap-x-3 gap-y-2">
                        <h1 class="welcome-public__title min-w-0 shrink">
                            {{ __('Verificar visitas no imóvel') }}
                        </h1>
                        <x-public-municipality-pill :local="$localPrimario ?? null" class="shrink-0" />
                    </div>
                    <p class="welcome-public__lead">
                        {{ __('Grátis e sem cadastro: informe o código do comprovante ou o que o ACE ou ACS passou na visita.') }}
                    </p>
                    <p class="public-section-lead">
                        {{ __('Na consulta pública aparecem apenas endereço vinculado ao código, datas e status das visitas de campo e informações já divulgadas sobre doenças. Não são exibidos dados clínicos, nome de ocupantes nem cadastro socioeconômico do imóvel.') }}
                    </p>
                </div>
            </div>
            <div class="public-hero-actions">
                <a href="{{ url('/') }}" class="welcome-public__link">
                    <x-heroicon-o-arrow-left class="h-3.5 w-3.5 shrink-0 opacity-80" aria-hidden="true" />
                    {{ __('Voltar à página inicial') }}
                </a>
            </div>
        </header>

        <x-flash-alerts />

        <section class="consulta-public-search" aria-labelledby="consulta-codigo-titulo">
            <form action="{{ route('consulta.codigo') }}" method="GET" id="consulta-codigo-form" class="space-y-4">
                <h2 id="consulta-codigo-titulo" class="public-section-title">{{ __('Código do imóvel') }}</h2>
                <label for="codigo" class="public-form-label">
                    {!! __('Informe o <strong>código numérico</strong> que consta no comprovante ou foi passado pelo ACE ou ACS na visita.') !!}
                    <span class="text-red-500" aria-hidden="true">*</span>
                </label>
                <div class="flex flex-col gap-3 sm:flex-row sm:items-stretch">
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
                    <button type="submit" id="consulta-codigo-btn" class="v-btn-primary inline-flex min-h-[2.75rem] shrink-0 items-center justify-center px-5 text-sm font-medium sm:w-auto">
                        {{ __('Consultar agora') }}
                    </button>
                </div>
                @error('codigo')
                    <p class="text-sm text-red-600 dark:text-red-400" role="alert">{{ $message }}</p>
                @enderror
                <p id="codigo-ajuda" class="public-help-text">
                    {{ __('Cada imóvel cadastrado recebe um código exclusivo. Se não tiver o número em mãos, peça orientação na Secretaria Municipal de Saúde ou ao agente no próximo contato.') }}
                </p>
            </form>
        </section>

        <section class="consulta-public-panel" aria-labelledby="consulta-doencas-titulo">
            <div class="consulta-public-panel__head">
                <h2 id="consulta-doencas-titulo" class="public-section-title">{{ __('Doenças monitoradas no município') }}</h2>
                @if($doencasIndisponivel ?? false)
                    <p class="public-section-lead">{{ __('Lista de doenças indisponível agora; use o formulário acima para consultar visitas.') }}</p>
                @elseif($doencas->isEmpty())
                    <p class="public-section-lead">{{ __('Ainda não há doenças públicas cadastradas pelo município.') }}</p>
                @else
                    <p class="public-section-lead">{{ __('Sintomas, transmissão e medidas de controle informados pelo município.') }}</p>
                @endif
            </div>

            @if($doencasIndisponivel ?? false)
                <div class="public-empty public-empty--amber" role="status">
                    <p class="text-sm font-medium text-amber-900 dark:text-amber-100">{{ __('Serviço temporariamente indisponível') }}</p>
                    <p class="public-section-lead mx-auto mt-2 max-w-lg">{{ __('As orientações sobre doenças não puderam ser exibidas. Use o formulário acima para verificar visitas no seu imóvel.') }}</p>
                </div>
            @elseif($doencas->isEmpty())
                <div class="public-empty">
                    <p class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ __('Nenhuma doença cadastrada no momento') }}</p>
                    <p class="public-section-lead mx-auto mt-2 max-w-lg">{{ __('Volte mais tarde ou procure a Secretaria Municipal de Saúde para orientações gerais sobre dengue e outras arboviroses.') }}</p>
                </div>
            @else
                <div class="v-table-meta border-t border-slate-200/80 dark:border-slate-700/80">
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
            @endif
        </section>

        @include('partials.public-copyright-footer', ['footerClass' => 'pt-4'])
    </div>
</div>
@endsection

@push('scripts')
<script type="application/json" id="consulta-index-config">
@json(['searching' => __('Buscando…')])
</script>
@vite(['resources/js/consulta-index.js'])
@endpush
