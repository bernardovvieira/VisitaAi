@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6 max-w-4xl space-y-6">

    <!-- Botão Voltar -->
    <div>
        <a href="{{ route('gestor.users.index') }}" 
        class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-semibold text-sm rounded-lg shadow transition">
           <svg class="h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M15 19l-7-7 7-7"/>
            </svg>
            Voltar
        </a>
    </div>

    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Usuários Pendentes</h1>

    @if(session('status'))
        <x-alert type="success" :message="session('status')" />
    @endif

    <!-- Card introdutório -->
    <section class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow space-y-2">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Gerenciamento de Aprovação</h2>
        <p class="text-sm text-gray-600 dark:text-gray-400">
            Nesta tela, você pode visualizar usuários que aguardam aprovação para acessar o sistema.
            Clique no botão <strong>"Aprovar"</strong> para liberar o acesso do usuário selecionado.
        </p>
    </section>

    <!-- Tabela de Pendentes -->
    <section class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow space-y-2">
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
            @if($pendentes->count() > 0)
                Exibindo {{ $pendentes->count() }} usuário(s) pendente(s).
            @else
                Nenhum usuário pendente no momento.
            @endif
        </p>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white dark:bg-gray-800 rounded-lg shadow">
                <thead class="bg-gray-100 dark:bg-gray-700 sticky top-0">
                    <tr>
                        <th class="p-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">ID</th>
                        <th class="p-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Nome</th>
                        <th class="p-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">CPF</th>
                        <th class="p-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">E‑mail</th>
                        <th class="p-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Data de Cadastro</th>
                        <th class="p-4 text-center text-sm font-semibold text-gray-700 dark:text-gray-300">Ação</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($pendentes as $u)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-600">
                            <td class="p-4 text-gray-800 dark:text-gray-100">
                                <span class="inline-block bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-gray-200 text-xs font-semibold px-2 py-1 rounded">
                                    #{{ $u->use_id }}
                                </span>
                            </td>
                            <td class="p-4 text-gray-800 dark:text-gray-100">{{ $u->use_nome }}</td>
                            <td class="p-4 text-gray-800 dark:text-gray-100">{{ preg_replace('/\d(?=(?:.*\d){2})/', '*', $u->use_cpf) }}</td>
                            <td class="p-4 text-gray-800 dark:text-gray-100"><a href="mailto:{{ $u->use_email }}" class="text-gray-600 dark:text-gray-400 hover:underline">{{ $u->use_email }}</a></td>
                            <td class="p-4 text-gray-800 dark:text-gray-100">{{ $u->use_data_criacao->format('d/m/Y') }}</td>
                            <td class="p-4 text-center">
                                <form method="POST" action="{{ route('gestor.approve', $u) }}">
                                    @csrf
                                    <button type="submit" onclick="return confirm('Tem certeza que deseja aprovar este usuário?')" 
                                        class="inline-flex items-center gap-2 px-4 py-2 bg-green-500 hover:bg-green-600 text-white text-sm font-semibold rounded-md shadow-md transition">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Aprovar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-6 text-center text-gray-600 dark:text-gray-400">
                                Nenhum usuário pendente no momento.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

</div>
@endsection
