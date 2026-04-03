@extends('layouts.app')

@section('public')
@endsection

@section('og_title', config('app.name') . ' · ' . __('Consulta pública'))
@section('og_description', __('Consulte, de graça e sem cadastro, as datas das visitas de vigilância feitas no seu imóvel. Use só o código numérico que o ACE ou ACS entregou no endereço.'))

@section('content')
<div class="max-w-4xl mx-auto space-y-10">

    {{-- Cabeçalho --}}
    <div class="pt-8">
        <div class="flex flex-wrap items-center gap-3">
            <img src="{{ asset('images/visitaai_rembg.png') }}" alt="{{ config('app.name') }}" class="h-12 w-auto" />
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-blue-600 dark:text-blue-400">{{ __('Consulta pública') }}</p>
                <h1 class="text-xl font-bold tracking-tight text-slate-900 dark:text-slate-100 sm:text-2xl">{{ __('Verificar visitas no imóvel') }}</h1>
            </div>
        </div>
        <p class="mt-4 max-w-2xl text-sm leading-relaxed text-gray-600 dark:text-gray-400">
            {{ __('Digite abaixo o código único do imóvel (apenas números). Você verá o endereço cadastrado, um mapa quando houver localização e o histórico de visitas — sem dados clínicos ou informações pessoais sensíveis.') }}
        </p>
        <a href="{{ url('/') }}" class="inline-flex items-center gap-1.5 mt-4 text-sm font-medium text-blue-600 dark:text-blue-400 hover:underline">
            <x-heroicon-o-arrow-left class="h-4 w-4 shrink-0" aria-hidden="true" />
            {{ __('Voltar à página inicial') }}
        </a>
    </div>

    {{-- Busca por código --}}
    <form action="{{ route('consulta.codigo') }}" method="GET" id="consulta-codigo-form"
          class="v-card space-y-4">
        <h2 class="v-section-title">{{ __('Código do imóvel') }}</h2>
        <label for="codigo" class="v-toolbar-label">
            {!! __('Informe o <strong>código numérico</strong> que consta no comprovante ou foi passado pelo ACE ou ACS na visita.') !!} <span class="text-red-500" aria-hidden="true">*</span>
        </label>
        <div class="flex gap-4 flex-col md:flex-row">
            <input
                type="text"
                id="codigo"
                name="codigo"
                value="{{ old('codigo') }}"
                placeholder="{{ __('Ex.: 12345678') }}"
                required
                inputmode="numeric"
                pattern="[0-9]*"
                autocomplete="off"
                class="v-input md:flex-1"
                aria-describedby="codigo-ajuda">
            <button type="submit" id="consulta-codigo-btn"
                    class="btn-acesso-principal inline-flex min-w-[120px] items-center justify-center rounded-md px-3 py-1.5 text-[13px] font-semibold text-white shadow-sm transition">
                {{ __('Consultar agora') }}
            </button>
        </div>
        @error('codigo')
            <p class="mt-1 text-sm text-red-600 dark:text-red-400" role="alert">{{ $message }}</p>
        @enderror
        <p id="codigo-ajuda" class="text-sm text-gray-500 dark:text-gray-400 mt-2">
            {{ __('Cada imóvel cadastrado recebe um código exclusivo. Se não tiver o número em mãos, peça orientação na Secretaria Municipal de Saúde ou ao agente no próximo contato.') }}
        </p>
    </form>
    <script>
    (function(){
        var form = document.getElementById('consulta-codigo-form');
        var btn = document.getElementById('consulta-codigo-btn');
        if (form && btn) {
            var labelConsultando = @json(__('Buscando…'));
            form.addEventListener('submit', function() {
                btn.disabled = true;
                btn.innerHTML = '<span class="inline-flex items-center"><svg class="animate-spin -ml-1 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>' + labelConsultando + '</span>';
            });
        }
    })();
    </script>

    @if (session('erro'))
        <div class="bg-red-100 dark:bg-red-900/40 border border-red-400 dark:border-red-600 text-red-800 dark:text-red-100 px-4 py-3 rounded-lg flex items-start gap-3" role="alert">
            <x-heroicon-o-x-circle class="mt-0.5 h-5 w-5 shrink-0 text-red-600 dark:text-red-300" aria-hidden="true" />
            <div>
                <strong class="font-semibold text-red-900 dark:text-red-50">{{ __('Não foi possível concluir a consulta') }}</strong>
                <p class="mt-1 text-sm text-red-800 dark:text-red-100">{{ session('erro') }}</p>
            </div>
        </div>
    @endif

    {{-- Doenças monitoradas --}}
    <section class="v-card space-y-4" aria-labelledby="consulta-doencas-titulo">
        <h2 id="consulta-doencas-titulo" class="text-xl font-semibold text-gray-800 dark:text-gray-200">{{ __('Doenças monitoradas no município') }}</h2>
        <p class="text-sm text-gray-600 dark:text-gray-400">
            @if($doencas->isEmpty())
                {{ __('Aqui serão listadas as doenças que o gestor cadastrou para vigilância municipal — com sintomas, transmissão e o que fazer para se proteger. Enquanto não houver cadastro, a tabela fica vazia.') }}
            @else
                {{ __('Informações de apoio à população: sintomas comuns, como a doença pode ser transmitida e medidas de controle recomendadas pelo município.') }}
            @endif
        </p>

        @if($doencas->isEmpty())
            <div class="my-6 rounded-2xl border border-gray-200 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 px-8 py-12 text-center">
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Nenhuma doença cadastrada no momento') }}</p>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Volte mais tarde ou procure a Secretaria Municipal de Saúde para orientações gerais sobre dengue e outras arboviroses.') }}</p>
            </div>
        @else
            <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                <table class="min-w-full text-sm divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-semibold">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left">{{ __('Doença') }}</th>
                            <th scope="col" class="px-4 py-3 text-left">{{ __('Sintomas') }}</th>
                            <th scope="col" class="px-4 py-3 text-left">{{ __('Transmissão') }}</th>
                            <th scope="col" class="px-4 py-3 text-left">{{ __('Medidas de controle') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700 text-gray-800 dark:text-gray-100">
                        @foreach($doencas as $doenca)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-600 transition">
                                <td class="px-4 py-3 font-medium">{{ $doenca->doe_nome }}</td>
                                <td class="px-4 py-3">
                                    {{ is_array($doenca->doe_sintomas) ? implode(', ', $doenca->doe_sintomas) : $doenca->doe_sintomas }}
                                </td>
                                <td class="px-4 py-3">
                                    {{ is_array($doenca->doe_transmissao) ? implode(', ', $doenca->doe_transmissao) : $doenca->doe_transmissao }}
                                </td>
                                <td class="px-4 py-3">
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
