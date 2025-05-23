@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6 space-y-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Gerenciar Locais</h1>

    @if(session('success'))
        <x-alert type="success" :message="session('success')" />
    @endif
    @if(session('error'))
        <x-alert type="error" :message="session('error')" />
    @endif

    <!-- Card introdutório -->
    <section class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Informações</h2>
        <p class="mt-2 text-gray-600 dark:text-gray-400">
            Aqui você pode visualizar, cadastrar ou editar locais de visitação epidemiológica.
        </p>
        <a href="{{ route('agente.locais.create') }}"
           class="inline-flex items-center px-4 py-2 mt-4 bg-green-600 hover:bg-green-700 text-white font-semibold text-sm rounded-lg shadow-md transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Cadastrar Local
        </a>
    </section>

    <!-- Campo de Busca -->
    <section x-data="{ search: '{{ request('search') }}' }" class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow">
        <div class="flex flex-col sm:flex-row sm:items-center gap-4">
            <div class="flex-1">
                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Buscar Local</label>
                <input type="text" id="search" name="search" x-model="search" x-init="$el.focus()"
                       @input.debounce.500ms="window.location.href = '{{ route('agente.locais.index') }}' + '?search=' + encodeURIComponent(search)"
                       placeholder="Filtrar por endereço, bairro ou cidade..."
                       class="w-full rounded-md bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm px-4 py-2">
            </div>
        </div>
    </section>

    <!-- Tabela de Locais -->
    <section class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow">
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
            Exibindo {{ $locais->count() }} de {{ $locais->total() }} local(is) cadastrados.
            @if(request('search'))
                <span class="text-gray-500">Resultados para: <strong>{{ request('search') }}</strong></span>
            @endif
        </p>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white dark:bg-gray-800 rounded-lg shadow">
                <thead class="bg-gray-100 dark:bg-gray-700">
                    <tr>
                        <th class="p-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Código</th>
                        <th class="p-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Endereço</th>
                        <th class="p-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Bairro</th>
                        <th class="p-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Cidade</th>
                        <th class="p-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Coordenadas</th>
                        <th class="p-4 text-center text-sm font-semibold text-gray-700 dark:text-gray-300">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($locais as $local)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-600">
                            <td class="p-4 text-gray-800 dark:text-gray-100">{{ $local->loc_codigo_unico }}</td>
                            <td class="p-4 text-gray-800 dark:text-gray-100">{{ $local->loc_endereco }}, {{ $local->loc_numero }}</td>
                            <td class="p-4 text-gray-800 dark:text-gray-100">{{ $local->loc_bairro }}</td>
                            <td class="p-4 text-gray-800 dark:text-gray-100">{{ $local->loc_cidade }}</td>
                            <td class="p-4 text-gray-800 dark:text-gray-100">{{ $local->loc_latitude }}, {{ $local->loc_longitude }}</td>
                            <td class="p-4 text-center">
                                <div class="flex justify-center gap-3">
                                    <a href="{{ route('agente.locais.show', $local) }}"
                                       class="px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg shadow transition">
                                       Visualizar
                                    </a>                                    
                                    <a href="{{ route('agente.locais.edit', $local) }}"
                                       class="px-3 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm rounded-lg shadow transition">
                                       Editar
                                    </a>
                                    <form method="POST" action="{{ route('agente.locais.destroy', $local) }}">
                                        @csrf 
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('Excluir este local?')"
                                                class="px-3 py-2 bg-red-600 hover:bg-red-700 text-white text-sm rounded-lg shadow transition">
                                            Excluir
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-6 text-center text-gray-600 dark:text-gray-400">Nenhum local cadastrado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $locais->links() }}
        </div>
    </section>
</div>
@endsection