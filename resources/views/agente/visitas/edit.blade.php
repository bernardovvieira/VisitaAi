@extends('layouts.app')

@section('head')
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
@endsection

@section('content')
<div class="container mx-auto p-6 max-w-4xl space-y-6">
    <div>
        <a href="{{ route('agente.visitas.index') }}"
           class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-semibold text-sm rounded-lg shadow transition">
            <svg class="h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Voltar
        </a>
    </div>

    <section class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Editar Visita</h2>
        <p class="mt-2 text-gray-600 dark:text-gray-400">
            Atualize os dados da visita, incluindo local, data e doenças detectadas.
        </p>
    </section>

    <section class="p-6 bg-white dark:bg-gray-700 rounded-lg shadow space-y-6">
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('agente.visitas.update', $visita) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="vis_data" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Data da Visita <span class="text-red-500">*</span></label>
                    <input id="vis_data" name="vis_data" type="date" value="{{ old('vis_data', $visita->vis_data) }}" required
                           class="mt-1 block w-full rounded-md bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm">
                </div>

                <div
                    x-data="{
                        open: false,
                        search: 'Cód. {{ $visita->local->loc_codigo_unico }} - {{ $visita->local->loc_endereco }}, {{ $visita->local->loc_numero }} - {{ $visita->local->loc_bairro }}, {{ $visita->local->loc_cidade }}/{{ $visita->local->loc_estado }}',
                        selectedId: '{{ old('fk_local_id', $visita->fk_local_id) }}',
                        locais: {{ Js::from($locais) }},
                        limparSelecao() {
                            if (!this.selectedId) return;
                            this.selectedId = '';
                            this.search = '';
                            this.open = true;
                            this.$nextTick(() => this.$refs.input.focus());
                        }
                    }"
                    x-init="$watch('search', () => open = true)">

                    <label for="fk_local_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Local Visitado <span class="text-red-500">*</span></label>
                    <div class="relative mt-1">
                        <input type="text" x-model="search" @click="limparSelecao" x-ref="input" placeholder="Buscar local..."
                               class="block w-full px-3 py-2 rounded-md bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm">

                        <ul x-show="open" @click.away="open = false" class="absolute z-10 mt-1 w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md shadow max-h-60 overflow-auto">
                        <template x-for="local in locais.filter(l => {
                            const query = search.toLowerCase();
                            return (
                                l.loc_endereco.toLowerCase().includes(query) ||
                                l.loc_bairro.toLowerCase().includes(query) ||
                                l.loc_codigo_unico.toString().includes(query)
                            );
                        })" :key="local.loc_id">
                            <li>
                                <button type="button"
                                        @click="selectedId = local.loc_id; search = 'Cód. ' + local.loc_codigo_unico + ' - ' + local.loc_endereco + ', ' + local.loc_numero + ' - ' + local.loc_bairro + ', ' + local.loc_cidade + '/' + local.loc_estado; open = false"
                                        class="block text-left w-full px-3 py-2 text-sm text-gray-700 dark:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <span x-text="'Cód. ' + local.loc_codigo_unico + ' - ' + local.loc_endereco + ', ' + local.loc_numero + ' - ' + local.loc_bairro + ', ' + local.loc_cidade + '/' + local.loc_estado">
                                    </span>
                                </button>
                            </li>
                        </template>
                        </ul>
                    </div>
                    <input type="hidden" name="fk_local_id" :value="selectedId">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Doenças Detectadas</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    @foreach ($doencas as $doenca)
                        <label class="flex items-start gap-2">
                            <input type="checkbox" name="doencas[]" value="{{ $doenca->doe_id }}"
                                   @checked(in_array($doenca->doe_id, old('doencas', $visita->doencas->pluck('doe_id')->toArray())))
                                   class="form-checkbox h-4 w-4 text-green-600 dark:text-green-400 mt-1">
                            @php
                                $sintomas = is_array($doenca->doe_sintomas) ? $doenca->doe_sintomas : explode(',', $doenca->doe_sintomas);
                                $transmissao = is_array($doenca->doe_transmissao) ? $doenca->doe_transmissao : explode(',', $doenca->doe_transmissao);
                                $medidas = is_array($doenca->doe_medidas_controle) ? $doenca->doe_medidas_controle : explode(',', $doenca->doe_medidas_controle);

                                $formatar = fn($label, $itens) => $label . ":\n" . collect($itens)->map(fn($i) => '- ' . trim($i))->implode("\n");

                                $title = implode("\n\n", [
                                    $formatar('Sintomas', $sintomas),
                                    $formatar('Transmissão', $transmissao),
                                    $formatar('Medidas de Controle', $medidas),
                                ]);
                            @endphp
                            <div class="flex items-center gap-2">
                                <span class="text-gray-800 dark:text-gray-100 text-sm font-medium">
                                    {{ $doenca->doe_nome }}
                                </span>
                                <div title="{{ $title }}">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                         class="h-4 w-4 text-blue-600 hover:text-blue-800 cursor-help"
                                         fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20 10 10 0 000-20z" />
                                    </svg>
                                </div>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            <div>
                <label for="vis_observacoes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Observações</label>
                <textarea id="vis_observacoes" name="vis_observacoes" rows="6"
                          class="mt-1 block w-full rounded-md bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm">{{ old('vis_observacoes', $visita->vis_observacoes) }}</textarea>
            </div>

            <div class="flex justify-end">
                <button type="submit"
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-lg shadow-md transition">
                    Atualizar Visita
                </button>
            </div>
        </form>
    </section>
</div>
@endsection