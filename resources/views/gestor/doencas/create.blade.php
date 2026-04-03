@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <x-breadcrumbs :items="[['label' => 'Página Inicial', 'url' => route('dashboard')], ['label' => 'Doenças', 'url' => route('gestor.doencas.index')], ['label' => 'Cadastrar']]" />

    <!-- Card introdutório -->
    <section class="rounded-xl border border-gray-200/80 bg-white p-5 shadow-sm dark:border-gray-600 dark:bg-gray-800">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Cadastrar Doença</h2>
        <p class="mt-2 text-gray-600 dark:text-gray-400">
            Selecione as opções relevantes para esta doença.
        </p>
    </section>

    <!-- Formulário de Cadastro -->
    <section class="rounded-xl border border-gray-200/80 bg-white p-6 shadow-sm dark:border-gray-600 dark:bg-gray-800 space-y-6">
        @if(session('success'))
            <x-alert type="success" :message="session('success')" />
        @endif

        <form method="POST" action="{{ route('gestor.doencas.store') }}" class="space-y-6" id="doenca-create-form">
            @csrf

            <!-- Nome -->
            <div>
                <label for="doe_nome" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Nome <span class="text-red-500">*</span>
                </label>
                <input id="doe_nome" name="doe_nome" type="text" value="{{ old('doe_nome') }}"
                       required autofocus
                       class="mt-1 block w-full rounded-lg border border-gray-200 bg-gray-50 text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 dark:focus:border-blue-600 dark:focus:ring-blue-600">
                @error('doe_nome')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <!-- Sintomas (checkbox grid) -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Sintomas <span class="text-red-500">*</span>
                </label>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2">
                    @foreach($optionsSintomas as $opt)
                        <label class="inline-flex items-center space-x-2">
                            <input
                                type="checkbox"
                                name="doe_sintomas[]"
                                value="{{ $opt }}"
                                {{ in_array($opt, old('doe_sintomas', [])) ? 'checked' : '' }}
                                class="form-checkbox h-4 w-4 text-blue-600 dark:text-blue-400"
                            />
                            <span class="text-gray-700 dark:text-gray-300 text-sm">{{ $opt }}</span>
                        </label>
                    @endforeach
                </div>
                @error('doe_sintomas')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Modos de Transmissão -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Modos de Transmissão <span class="text-red-500">*</span>
                </label>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2">
                    @foreach($optionsTransmissao as $opt)
                        <label class="inline-flex items-center space-x-2">
                            <input
                                type="checkbox"
                                name="doe_transmissao[]"
                                value="{{ $opt }}"
                                {{ in_array($opt, old('doe_transmissao', [])) ? 'checked' : '' }}
                                class="form-checkbox h-4 w-4 text-blue-600 dark:text-blue-400"
                            />
                            <span class="text-gray-700 dark:text-gray-300 text-sm">{{ $opt }}</span>
                        </label>
                    @endforeach
                </div>
                @error('doe_transmissao')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Medidas de Controle -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Medidas de Controle <span class="text-red-500">*</span>
                </label>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2">
                    @foreach($optionsMedidas as $opt)
                        <label class="inline-flex items-center space-x-2">
                            <input
                                type="checkbox"
                                name="doe_medidas_controle[]"
                                value="{{ $opt }}"
                                {{ in_array($opt, old('doe_medidas_controle', [])) ? 'checked' : '' }}
                                class="form-checkbox h-4 w-4 text-blue-600 dark:text-blue-400"
                            />
                            <span class="text-gray-700 dark:text-gray-300 text-sm">{{ $opt }}</span>
                        </label>
                    @endforeach
                </div>
                @error('doe_medidas_controle')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end">
                <button type="submit" id="doenca-create-btn"
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-lg shadow-md transition inline-flex items-center">
                    Cadastrar
                </button>
            </div>
        </form>
    </section>
</div>
<script>
(function(){
    var form = document.getElementById('doenca-create-form');
    var btn = document.getElementById('doenca-create-btn');
    if (form && btn) form.addEventListener('submit', function(){ btn.disabled = true; btn.innerHTML = '<svg class="animate-spin -ml-1 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Salvando…'; });
})();
</script>
@endsection