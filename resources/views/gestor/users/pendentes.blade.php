@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <x-breadcrumbs :items="[['label' => 'Página Inicial', 'url' => route('dashboard')], ['label' => 'Usuários', 'url' => route('gestor.users.index')], ['label' => 'Pendentes']]" />

    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Usuários Pendentes</h1>

    @if(session('status'))
        <x-alert type="success" :message="session('status')" />
    @endif

    <!-- Card introdutório -->
    <section class="rounded-xl border border-gray-200/80 bg-white p-5 shadow-sm dark:border-gray-600 dark:bg-gray-800 space-y-2">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Gerenciamento de Aprovação</h2>
        <p class="text-sm text-gray-600 dark:text-gray-400">
            Nesta tela, você pode visualizar usuários que aguardam aprovação para acessar o sistema.
            Clique no botão <strong>"Aprovar"</strong> para liberar o acesso do usuário selecionado.
        </p>
    </section>

    <!-- Tabela de Pendentes -->
    <section class="rounded-xl border border-gray-200/80 bg-white p-5 shadow-sm dark:border-gray-600 dark:bg-gray-800 space-y-2">
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
            @if($pendentes->total() > 0)
                Exibindo {{ $pendentes->count() }} de {{ $pendentes->total() }} usuário(s) pendente(s).
            @else
                Nenhum usuário pendente no momento.
            @endif
        </p>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
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
                                        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-semibold rounded-md shadow-md transition">
                                        <x-heroicon-o-check class="h-4 w-4 shrink-0" />
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
        <x-pagination-relatorio :paginator="$pendentes" item-label="usuários pendentes" />
    </section>

</div>
@endsection
