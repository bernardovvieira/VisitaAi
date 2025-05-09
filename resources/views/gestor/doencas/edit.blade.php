@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6 max-w-4xl space-y-6">

    <!-- Botão Voltar -->
    <div>
        <a href="{{ route('gestor.doencas.index') }}" 
           class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-semibold text-sm rounded-lg shadow transition">
            <svg class="h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M15 19l-7-7 7-7"/>
            </svg>
            Voltar
        </a>
    </div>

    <!-- Card introdutório -->
    <section class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Editar Doença</h2>
        <p class="mt-2 text-gray-600 dark:text-gray-400">
            Atualize os dados da doença monitorada conforme necessário.
        </p>
    </section>

    <!-- Formulário de Edição -->
    <section class="p-6 bg-white dark:bg-gray-700 rounded-lg shadow space-y-6">
        @if(session('success'))
            <x-alert type="success" :message="session('success')" />
        @endif

        <form method="POST" action="{{ route('gestor.doencas.update', $doenca) }}" class="space-y-6">
            @csrf
            @method('PATCH')

            <div>
                <label for="doe_nome" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Nome <span class="text-red-500">*</span>
                </label>
                <input id="doe_nome" name="doe_nome" type="text"
                       value="{{ old('doe_nome', $doenca->doe_nome) }}"
                       required autofocus
                       class="mt-1 block w-full rounded-md bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm">
                @error('doe_nome')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <!-- Sintomas -->
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
                                {{   in_array($opt, old('doe_sintomas', $doenca->doe_sintomas)) ? 'checked' : '' }}
                                class="form-checkbox h-4 w-4 text-green-600 dark:text-green-400"
                            />
                            <span class="text-gray-700 dark:text-gray-300 text-sm">{{ $opt }}</span>
                        </label>
                    @endforeach
                </div>
                @error('doe_sintomas')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Transmissão -->
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
                                {{ in_array($opt, old('doe_transmissao', $doenca->doe_transmissao)) ? 'checked' : '' }}
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
                                {{ in_array($opt, old('doe_medidas_controle', $doenca->doe_medidas_controle)) ? 'checked' : '' }}
                                class="form-checkbox h-4 w-4 text-purple-600 dark:text-purple-400"
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
                <button type="submit"
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-lg shadow-md transition">
                    Salvar Alterações
                </button>
            </div>
        </form>
    </section>
</div>
@endsection