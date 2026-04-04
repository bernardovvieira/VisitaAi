@extends('layouts.app')

@section('public')
@endsection

@section('og_title', config('app.name') . ' · ' . __('Consulta pública'))
@section('og_description', __('Consulte, de graça e sem cadastro, as datas das visitas de vigilância feitas no seu imóvel. Use só o código numérico que o ACE ou ACS entregou no endereço.'))

@section('content')
<div class="v-page space-y-6">
    <x-page-header :eyebrow="__('Transparência para a população')" :title="__('Verificar visitas no imóvel')">
        <x-slot name="lead">
            <p>{{ __('Consulte pelo código de oito dígitos do imóvel — o mesmo formato usado na gestão interna do sistema.') }}</p>
            <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">{{ __('Digite abaixo o código único do imóvel (apenas números). Você verá o endereço cadastrado, um mapa quando houver localização e o histórico de visitas — sem dados clínicos ou informações pessoais sensíveis.') }}</p>
            <a href="{{ url('/') }}" class="mt-3 inline-flex items-center gap-1.5 text-sm font-medium text-blue-600 hover:underline dark:text-blue-400">
                <x-heroicon-o-arrow-left class="h-4 w-4 shrink-0" aria-hidden="true" />
                {{ __('Voltar à página inicial') }}
            </a>
        </x-slot>
    </x-page-header>

    @if (session('erro'))
        <x-alert type="error" :message="session('erro')" :autodismiss="false" />
    @endif

    <form action="{{ route('consulta.codigo') }}" method="GET" id="consulta-codigo-form"
          class="v-card v-card--muted space-y-4">
        <h2 class="v-section-title">{{ __('Código do imóvel') }}</h2>
        <label for="codigo" class="v-toolbar-label">
            {!! __('Informe o <strong>código numérico</strong> que consta no comprovante ou foi passado pelo ACE ou ACS na visita.') !!}
            <span class="text-red-500" aria-hidden="true">*</span>
        </label>
        <div class="flex flex-col gap-3 md:flex-row md:items-center">
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
                class="v-input md:max-w-md"
                aria-describedby="codigo-ajuda"
            />
            <button type="submit" id="consulta-codigo-btn" class="v-btn-primary inline-flex shrink-0 items-center justify-center px-3.5 py-1.5 text-[13px] font-semibold leading-snug">
                {{ __('Consultar agora') }}
            </button>
        </div>
        @error('codigo')
            <p class="text-sm text-red-600 dark:text-red-400" role="alert">{{ $message }}</p>
        @enderror
        <p id="codigo-ajuda" class="text-sm text-slate-500 dark:text-slate-400">
            {{ __('Cada imóvel cadastrado recebe um código exclusivo. Se não tiver o número em mãos, peça orientação na Secretaria Municipal de Saúde ou ao agente no próximo contato.') }}
        </p>
    </form>

    <section class="v-card v-card--flush overflow-hidden" aria-labelledby="consulta-doencas-titulo">
        <div class="p-4 sm:p-5">
            <h2 id="consulta-doencas-titulo" class="v-section-title">{{ __('Doenças monitoradas no município') }}</h2>
            <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">
                @if($doencasIndisponivel ?? false)
                    {{ __('Não foi possível carregar a lista de doenças neste momento. A consulta pelo código do imóvel acima continua disponível. Se o problema persistir, tente mais tarde ou procure a Secretaria Municipal de Saúde.') }}
                @elseif($doencas->isEmpty())
                    {{ __('Aqui serão listadas as doenças que o gestor cadastrou para vigilância municipal — com sintomas, transmissão e o que fazer para se proteger. Enquanto não houver cadastro, a tabela fica vazia.') }}
                @else
                    {{ __('Informações de apoio à população: sintomas comuns, como a doença pode ser transmitida e medidas de controle recomendadas pelo município.') }}
                @endif
            </p>
        </div>

        @if($doencasIndisponivel ?? false)
            <div class="border-t border-amber-200/80 bg-amber-50/60 px-4 py-10 text-center dark:border-amber-900/40 dark:bg-amber-950/25 sm:px-5" role="status">
                <p class="text-sm font-medium text-amber-900 dark:text-amber-100">{{ __('Serviço temporariamente indisponível') }}</p>
                <p class="mt-2 text-sm text-amber-800/90 dark:text-amber-200/90">{{ __('As orientações sobre doenças não puderam ser exibidas. Use o formulário acima para verificar visitas no seu imóvel.') }}</p>
            </div>
        @elseif($doencas->isEmpty())
            <div class="border-t border-slate-200/80 px-4 py-10 text-center dark:border-slate-700/80 sm:px-5">
                <p class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ __('Nenhuma doença cadastrada no momento') }}</p>
                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">{{ __('Volte mais tarde ou procure a Secretaria Municipal de Saúde para orientações gerais sobre dengue e outras arboviroses.') }}</p>
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
</div>
@endsection

@push('scripts')
<script type="application/json" id="consulta-index-config">
@json(['searching' => __('Buscando…')])
</script>
@vite(['resources/js/consulta-index.js'])
@endpush
