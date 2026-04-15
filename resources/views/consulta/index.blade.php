@extends('layouts.app')

@section('public')
@endsection

@section('og_title', config('app.brand') . ' · ' . __('Consulta pública'))
@section('og_description', 'Consulta pública municipal para histórico de visitas, com acesso rápido por código da placa do imóvel.')

@section('content')
<div class="mx-auto max-w-5xl v-stack">

    {{-- Cabeçalho --}}
    <header class="v-page-header pt-8">
        <div class="flex items-center gap-3">
            <img src="{{ asset('images/visitaai.svg') }}" alt="{{ config('app.brand') }}" class="h-12 w-auto" />
            <h1 class="v-page-title">Consulta Pública</h1>
        </div>
        <p class="v-page-lead mt-2">Consulte o histórico público de visitas por código do imóvel.</p>
        <a href="{{ url('/') }}" class="mt-4 inline-flex items-center gap-1.5 text-sm font-medium text-blue-700 hover:text-blue-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500/35 rounded-sm dark:text-blue-400 dark:hover:text-blue-300">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
            Voltar para o início
        </a>
    </header>

    {{-- Busca por código --}}
    <form action="{{ route('consulta.codigo') }}" method="GET" id="consulta-codigo-form"
          class="v-card v-stack">
        <x-form-field name="codigo" :label="__('Digite o código único do imóvel fornecido pelo agente')" :required="true" :help="__('O código único é um identificador exclusivo para cada imóvel (apenas números), fornecido pelo agente durante a visita.')">
            <div class="flex flex-col gap-4 md:flex-row">
                <input
                    type="text"
                    id="codigo"
                    name="codigo"
                    value="{{ old('codigo') }}"
                    placeholder="Ex: 12345678 (apenas números)"
                    required
                    inputmode="numeric"
                    pattern="[0-9]{8}"
                    minlength="8"
                    maxlength="8"
                    class="v-input w-full">
                <button type="submit" id="consulta-codigo-btn"
                    class="v-btn-primary inline-flex min-w-[120px] items-center justify-center px-4 py-2">
                    Consultar
                </button>
            </div>
        </x-form-field>
        <p class="text-sm text-gray-500 dark:text-gray-400">
            {{ __('Código do imóvel') }}
        </p>
        <p class="text-sm text-gray-500 dark:text-gray-400">
            {{ __('Na consulta pública aparecem apenas endereço vinculado ao código, datas e status das visitas de campo e informações já divulgadas sobre doenças. Não são exibidos dados clínicos, nome de ocupantes nem cadastro socioeconômico do imóvel.') }}
        </p>
    </form>
    {{-- Alerta de erro (código não encontrado ou inválido) --}}
    @if (session('error') || session('erro'))
        <div id="consulta-alerta-erro" class="v-alert v-alert--error flex items-start gap-3" role="alert" tabindex="-1" aria-live="assertive">
            <svg class="w-5 h-5 flex-shrink-0 mt-0.5 text-red-600 dark:text-red-300" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <div>
                <strong class="font-semibold text-red-900 dark:text-red-50">Não foi possível consultar</strong>
                <p class="mt-1 text-sm text-red-800 dark:text-red-100 opacity-100">{{ session('error') ?? session('erro') }}</p>
            </div>
        </div>
    @endif

    {{-- Seção de doenças monitoradas --}}
    <section class="v-card v-stack">
        <h2 class="v-section-title">Doenças Monitoradas</h2>
        <p class="text-sm text-gray-600 dark:text-gray-400">
            @if($doencas->isEmpty())
                Lista de doenças do sistema municipal. Quando houver doenças cadastradas, elas aparecerão abaixo com sintomas, formas de transmissão e medidas de controle.
            @else
                Essas são as doenças cadastradas no sistema municipal, com seus sintomas, formas de transmissão e medidas de controle.
            @endif
        </p>

        @if($doencas->isEmpty())
            <x-empty-state
                title="Nenhuma doença cadastrada no momento"
                description="As informações serão exibidas aqui quando o gestor municipal cadastrar as doenças monitoradas."
                icon="heroicon-o-beaker"
                class="border-slate-200 bg-slate-50 dark:border-slate-600 dark:bg-slate-800/50"
            />
        @else
            <div class="v-table-wrap rounded-lg border border-slate-200/90 dark:border-slate-700/80">
                <table class="v-data-table">
                    <thead>
                        <tr>
                            <th>Doença</th>
                            <th>Sintomas</th>
                            <th>Transmissão</th>
                            <th>Medidas de Controle</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($doencas as $doenca)
                            <tr>
                                <td class="font-medium">{{ $doenca->doe_nome }}</td>
                                <td>
                                    {{ is_array($doenca->doe_sintomas) ? implode(', ', $doenca->doe_sintomas) : $doenca->doe_sintomas }}
                                </td>
                                <td>
                                    {{ is_array($doenca->doe_transmissao) ? implode(', ', $doenca->doe_transmissao) : $doenca->doe_transmissao }}
                                </td>
                                <td>
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
<script type="application/json" id="consulta-index-config">@json(['searching' => __('Consultando…')])</script>
@if (session('error') || session('erro'))
<script>
document.addEventListener('DOMContentLoaded', function () {
    var alerta = document.getElementById('consulta-alerta-erro');
    if (alerta && typeof alerta.focus === 'function') alerta.focus();
});
</script>
@endif
@vite(['resources/js/consulta-index.js'])
@endpush