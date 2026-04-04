@extends('layouts.app')

@section('content')
<div class="v-page">
    <x-breadcrumbs :items="[['label' => __('Página Inicial'), 'url' => route('dashboard')], ['label' => __('Usuários'), 'url' => route('gestor.users.index')], ['label' => __('Pendentes')]]" />

    <x-page-header :eyebrow="__('Aprovações')" :title="__('Usuários pendentes')" />

    @if(session('status'))
        <x-alert type="success" :message="session('status')" />
    @endif

    <!-- Card introdutório -->
    <section class="v-card space-y-2 dark:bg-gray-800">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Gerenciamento de Aprovação</h2>
        <p class="text-sm text-gray-600 dark:text-gray-400">
            Nesta tela, você pode visualizar usuários que aguardam aprovação para acessar o sistema.
            Use o botão de confirmação na coluna Ação para liberar o acesso do usuário selecionado.
        </p>
    </section>

    <!-- Tabela de Pendentes -->
    <section class="v-card space-y-2 dark:bg-gray-800">
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
                        <th class="p-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">E-mail</th>
                        <th class="p-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Data de Cadastro</th>
                        <th class="p-4 text-center text-sm font-semibold text-gray-700 dark:text-gray-300">Ação</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($pendentes as $u)
                        <tr class="v-table-row-interactive">
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
                                <form method="POST" action="{{ route('gestor.approve', $u) }}" class="inline">
                                    @csrf
                                    <button type="submit" onclick="return confirm('Tem certeza que deseja aprovar este usuário?')"
                                        class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border border-transparent bg-emerald-600 text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500/45 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-gray-900"
                                        title="{{ __('Aprovar usuário') }}"
                                        aria-label="{{ __('Aprovar usuário') }}">
                                        <x-heroicon-o-check class="h-4 w-4 shrink-0" />
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
