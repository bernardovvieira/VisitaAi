<!-- resources/views/gestor/users/index.blade.php -->
@extends('layouts.app')

@section('og_title', config('app.name') . ' · Usuários')
@section('og_description', 'Usuários cadastrados no sistema. Visualize, edite e gerencie permissões.')

@section('content')
<div class="space-y-6">
    <x-breadcrumbs :items="[['label' => 'Página Inicial', 'url' => route('dashboard')], ['label' => 'Usuários']]" />
    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Usuários</h1>

    @if(session('status'))
        <x-alert type="success" :message="session('status')" />
    @endif

    @if(session('error'))
        <x-alert type="error" :message="session('error')" />    
    @endif

    <!-- Card introdutório -->
    <section class="rounded-xl border border-gray-200/80 bg-white p-5 shadow-sm dark:border-gray-600 dark:bg-gray-800">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Usuários do sistema</h2>
        <p class="mt-2 text-gray-600 dark:text-gray-400">
            Visualize, edite e exclua usuários cadastrados. Novos usuários devem se registrar pela tela de login.
        </p>
    </section>

    <!-- Busca (atualiza ao digitar) -->
    <section class="rounded-xl border border-gray-200/80 bg-white p-5 shadow-sm dark:border-gray-600 dark:bg-gray-800">
        <div class="flex flex-col sm:flex-row sm:items-end gap-4">
            <div class="flex-1">
                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Busca inteligente</label>
                <div class="flex items-center gap-2">
                    <input type="text" id="search" name="search" value="{{ old('search', request('search')) }}"
                           data-live-url="{{ route('gestor.users.index') }}" data-live-param="search"
                           data-live-loading-id="search-loading-users"
                           placeholder="Nome, e-mail ou perfil (ex.: ACE, ACS, gestor)..."
                           class="w-full rounded-lg border border-gray-200 bg-gray-50 text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 dark:focus:border-blue-600 dark:focus:ring-blue-600 px-4 py-2">
                    <span id="search-loading-users" class="hidden text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap" aria-live="polite">Buscando…</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Tabela de Usuários -->
    <section class="rounded-xl border border-gray-200/80 bg-white p-5 shadow-sm dark:border-gray-600 dark:bg-gray-800">
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
            Exibindo {{ $usuarios->count() }} de {{ $usuarios->total() }} usuário(s) cadastrado(s).
            @if(request('search'))
                <span class="text-gray-500">Resultados para: <strong>{{ request('search') }}</strong></span>
            @endif
        </p>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-100 dark:bg-gray-700">
                    <tr>
                        <th class="p-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">ID</th>
                        <th class="p-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Nome</th>
                        <th class="p-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">CPF</th>
                        <th class="p-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">E-mail</th>
                        <th class="p-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Perfil</th>
                        <th class="p-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">2FA</th>
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
                                    $perfil = \App\Models\User::perfilLabel($usuario->use_perfil);
                                @endphp
                                {{ $perfil }}
                            </td>
                            <td class="p-4">
                                @if(method_exists($usuario, 'hasEnabledTwoFactorAuthentication') && $usuario->hasEnabledTwoFactorAuthentication())
                                    <span class="inline-flex items-center rounded px-2 py-1 text-xs font-medium bg-slate-100 text-slate-800 dark:bg-slate-800/80 dark:text-slate-300">Ativo</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium text-gray-500 dark:text-gray-400">-</span>
                                @endif
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
    <div class="flex justify-center gap-1.5">
        <a href="{{ route('gestor.users.edit', $usuario) }}"
           class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-600 shadow-sm transition hover:bg-slate-50 hover:text-slate-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400/40 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700"
           title="{{ __('Editar usuário') }}"
           aria-label="{{ __('Editar usuário') }}">
            <x-heroicon-o-pencil-square class="h-4 w-4 shrink-0" />
        </a>
        <form method="POST" action="{{ route('gestor.users.destroy', $usuario) }}" class="inline">
            @csrf
            @method('DELETE')
            <button type="submit"
                    onclick="return confirm('Tem certeza que deseja anonimizar este usuário? Esta ação não pode ser desfeita.')"
                    class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border border-red-200 bg-red-50 text-red-600 shadow-sm transition hover:bg-red-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-400/40 dark:border-red-900/50 dark:bg-red-950/40 dark:text-red-300 dark:hover:bg-red-950/60"
                    title="{{ __('Anonimizar usuário') }}"
                    aria-label="{{ __('Anonimizar usuário') }}">
                <x-heroicon-o-shield-exclamation class="h-4 w-4 shrink-0" />
            </button>
        </form>
    </div>
@endif

                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center">
                                <div class="flex flex-col items-center justify-center max-w-sm mx-auto">
                                    <div class="w-14 h-14 rounded-full bg-gray-100 dark:bg-gray-600 flex items-center justify-center mb-3">
                                        <x-heroicon-o-user-group class="h-7 w-7 shrink-0 text-gray-400 dark:text-gray-500" />
                                    </div>
                                    <p class="text-gray-600 dark:text-gray-400 font-medium">Nenhum usuário cadastrado.</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-500 mt-1">Novos usuários devem se registrar pela tela de login.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <x-pagination-relatorio :paginator="$usuarios" item-label="usuários" />
    </section>

    <!-- Card de Acesso a Pendentes -->
    <section class="rounded-xl border border-gray-200/80 bg-white p-5 shadow-sm dark:border-gray-600 dark:bg-gray-800 space-y-3">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Usuários Pendentes</h2>
        <p class="text-sm text-gray-600 dark:text-gray-400">
            Visualize e aprove usuários que aguardam liberação para acessar o sistema.
        </p>
        <a href="{{ route('gestor.pendentes') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-gray-600 hover:bg-gray-700 dark:bg-gray-600 dark:hover:bg-gray-500 rounded-lg shadow transition">
            <x-heroicon-o-chevron-right class="h-4 w-4 shrink-0" />
            Ver usuários pendentes
        </a>
    </section>
</div>
@endsection