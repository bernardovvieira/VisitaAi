@extends('layouts.app')

@section('public')
@endsection

@section('og_title', config('app.name') . ' — Consulta Pública')
@section('og_description', 'Consulte o histórico de visitas epidemiológicas do seu imóvel pelo código único fornecido pelo profissional (ACE/ACS).')

@section('content')
<div class="max-w-4xl mx-auto space-y-10">

    {{-- Cabeçalho --}}
    <div style="padding-top: 2rem;">
        <div class="flex items-center gap-3">
            <img src="{{ asset('images/visitaai_rembg.png') }}" alt="{{ config('app.name') }}" class="h-12 w-auto" />
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Consulta Pública</h1>
        </div>
        <a href="{{ url('/') }}" class="inline-flex items-center gap-1.5 mt-3 text-sm font-medium text-blue-600 dark:text-blue-400 hover:underline">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
            Voltar para o início
        </a>
    </div>

    {{-- Busca por código --}}
    <form action="{{ route('consulta.codigo') }}" method="GET" id="consulta-codigo-form"
          class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow space-y-4">
        <label for="codigo" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            Digite o <strong>código único do imóvel</strong> fornecido pelo profissional (ACE/ACS) <span class="text-red-500">*</span>
        </label>
        <div class="flex gap-4 flex-col md:flex-row">
            <input
                type="text"
                id="codigo"
                name="codigo"
                value="{{ old('codigo') }}"
                placeholder="Ex: 12345678 (apenas números)"
                required
                inputmode="numeric"
                pattern="[0-9]*"
                class="w-full rounded-md p-2 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100 border border-gray-300 dark:border-gray-600">
            <button type="submit" id="consulta-codigo-btn"
                    class="btn-acesso-principal px-4 py-2 text-white font-semibold rounded-md shadow transition inline-flex items-center justify-center min-w-[120px]">
                Consultar
            </button>
        </div>
        @error('codigo')
            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
            O código único é um identificador exclusivo para cada imóvel (apenas números), fornecido pelo profissional (ACE/ACS) durante a visita.
        </p>
    </form>
    <script>
    (function(){
        var form = document.getElementById('consulta-codigo-form');
        var btn = document.getElementById('consulta-codigo-btn');
        if (form && btn) {
            form.addEventListener('submit', function() {
                btn.disabled = true;
                btn.innerHTML = '<span class="inline-flex items-center"><svg class="animate-spin -ml-1 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Consultando…</span>';
            });
        }
    })();
    </script>

    {{-- Alerta de erro (código não encontrado ou inválido) --}}
    @if (session('erro'))
        <div class="bg-red-100 dark:bg-red-900/40 border border-red-400 dark:border-red-600 text-red-800 dark:text-red-100 px-4 py-3 rounded-lg flex items-start gap-3" role="alert">
            <svg class="w-5 h-5 flex-shrink-0 mt-0.5 text-red-600 dark:text-red-300" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <div>
                <strong class="font-semibold text-red-900 dark:text-red-50">Não foi possível consultar</strong>
                <p class="mt-1 text-sm text-red-800 dark:text-red-100 opacity-100">{{ session('erro') }}</p>
            </div>
        </div>
    @endif

    {{-- Seção de doenças monitoradas --}}
    <section class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow space-y-4">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Doenças Monitoradas</h2>
        <p class="text-sm text-gray-600 dark:text-gray-400">
            @if($doencas->isEmpty())
                Lista de doenças do sistema municipal. Quando houver doenças cadastradas, elas aparecerão abaixo com sintomas, formas de transmissão e medidas de controle.
            @else
                Essas são as doenças cadastradas no sistema municipal, com seus sintomas, formas de transmissão e medidas de controle.
            @endif
        </p>

        @if($doencas->isEmpty())
            <div class="my-6 rounded-2xl border border-gray-200 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 px-8 py-12 text-center">
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Nenhuma doença cadastrada no momento</p>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">As informações serão exibidas aqui quando o gestor municipal cadastrar as doenças monitoradas.</p>
            </div>
        @else
            <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                <table class="min-w-full text-sm divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-semibold">
                        <tr>
                            <th class="px-4 py-3 text-left">Doença</th>
                            <th class="px-4 py-3 text-left">Sintomas</th>
                            <th class="px-4 py-3 text-left">Transmissão</th>
                            <th class="px-4 py-3 text-left">Medidas de Controle</th>
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