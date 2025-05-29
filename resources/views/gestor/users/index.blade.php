<!-- resources/views/gestor/users/index.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6 space-y-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Gerenciar Usuários</h1>

    @if(session('status'))
        <x-alert type="success" :message="session('status')" />
    @endif

    @if(session('error'))
        <x-alert type="error" :message="session('error')" />    
    @endif

    <!-- Card introdutório -->
    <section class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Informações</h2>
        <p class="mt-2 text-gray-600 dark:text-gray-400">
            Nesta seção você pode visualizar, editar e excluir usuários cadastrados no sistema.
            Para adicionar novos usuários, é necessário que eles se registrem diretamente pela tela de login.
        </p>
    </section>

    <!-- Campo de Busca de Usuários -->
    <section x-data="{ search: '{{ request('search') }}' }" class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow">
        <div class="flex flex-col sm:flex-row sm:items-center gap-4">
            <div class="flex-1">
                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Buscar Usuário</label>
                <input type="text" id="search" x-model="search" x-init="$el.focus()"
                    @input.debounce.500ms="window.location.href = '{{ route('gestor.users.index') }}' + '?search=' + encodeURIComponent(search)"
                    placeholder="Digite o nome ou e-mail..."
                    class="w-full rounded-md bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm px-4 py-2">
            </div>
        </div>
    </section>

    <!-- Tabela de Usuários -->
    <section class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow">
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
            Exibindo {{ $usuarios->count() }} de {{ $usuarios->total() }} usuário(s) cadastrado(s).
            @if(request('search'))
                <span class="text-gray-500">Resultados para: <strong>{{ request('search') }}</strong></span>
            @endif
        </p>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white dark:bg-gray-800 rounded-lg shadow">
                <thead class="bg-gray-100 dark:bg-gray-700">
                    <tr>
                        <th class="p-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">ID</th>
                        <th class="p-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Nome</th>
                        <th class="p-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">CPF</th>
                        <th class="p-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">E-mail</th>
                        <th class="p-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Perfil</th>
                        <th class="p-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Data Cadastro</th>
                        <th class="p-4 text-center text-sm font-semibold text-gray-700 dark:text-gray-300">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($usuarios as $usuario)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-600">
                            <td class="p-4 text-gray-800 dark:text-gray-100">
                                <span class="inline-block bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-gray-200 text-xs font-semibold px-2 py-1 rounded">
                                    #{{ $usuario->use_id }}
                                </span>
                            </td>
                            <td class="p-4 text-gray-800 dark:text-gray-100">{{ $usuario->use_nome }}</td>
                            <td class="p-4 text-gray-800 dark:text-gray-100">{{ preg_replace('/\d(?=(?:.*\d){2})/', '*', $usuario->use_cpf) }}</td>
                            <td class="p-4 text-gray-800 dark:text-gray-100"><a href="mailto:{{ $usuario->use_email }}" class="text-gray-600 dark:text-gray-400 hover:underline">{{ $usuario->use_email }}</a></td>
                            <td class="p-4 text-gray-800 dark:text-gray-100">
                                @php
                                    switch ($usuario->use_perfil) {
                                        case 'gestor':
                                            $perfil = 'Gestor Municipal';
                                            break;
                                        case 'agente_endemias':
                                            $perfil = 'Agente de Endemias';
                                            break;
                                        case 'agente_saude':
                                            $perfil = 'Agente de Saúde';
                                            break;
                                        default:
                                            $perfil = ucfirst($usuario->use_perfil);
                                    }
                                @endphp
                                {{ $perfil }}
                            </td>
                            <td class="p-4 text-gray-800 dark:text-gray-100">{{ $usuario->use_data_criacao->format('d/m/Y') }}</td>
                            <td class="p-4 text-center">
                            @if($usuario->use_data_anonimizacao)
    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-gray-400 text-white dark:bg-gray-500 dark:text-white">
        Anonimizado em {{ $usuario->use_data_anonimizacao->format('d/m/Y') }}
    </span>
@elseif(!$usuario->use_aprovado)
    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-yellow-400 text-white dark:bg-yellow-600 dark:text-white">
        Usuário com Aprovação Pendente
    </span>
@else
    <div class="flex justify-center gap-3">
        <!-- Botão Editar -->
        <a href="{{ route('gestor.users.edit', $usuario) }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-semibold text-sm rounded-lg shadow-md transition">
            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 20h9M16.5 3.5a2.121 2.121 0 113 3L7 19l-4 1 1-4 12.5-12.5z" />
            </svg>
            Editar
        </a>

        <!-- Botão Anonimizar -->
        <form method="POST" action="{{ route('gestor.users.destroy', $usuario) }}">
            @csrf
            @method('DELETE')
            <button type="submit"
                    onclick="return confirm('Deseja realmente anonimizar este usuário?')"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold text-sm rounded-lg shadow-md transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                </svg>
                Anonimizar
            </button>
        </form>
    </div>
@endif

                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-6 text-center text-gray-600 dark:text-gray-400">Nenhum usuário cadastrado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <!-- Card de Acesso a Pendentes -->
    <section class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow space-y-2">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Usuários Pendentes</h2>
        <p class="text-sm text-gray-600 dark:text-gray-400">
            Visualize e aprove usuários que aguardam liberação para acessar o sistema.
            <a href="{{ route('gestor.pendentes') }}" class="text-yellow-500 hover:underline">Clique aqui para acessar</a>.
        </p>
    </section>


    <div class="mt-4">
        {{ $usuarios->links() }}
    </div>
</div>
@endsection