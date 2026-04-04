@extends('layouts.app')

@section('og_title', config('app.name') . ' · ' . __('Usuários'))
@section('og_description', __('Gestão de usuários cadastrados no sistema.'))

@section('content')
<div class="v-page">
    <x-breadcrumbs :items="[['label' => __('Página Inicial'), 'url' => route('dashboard')], ['label' => __('Usuários')]]" />
    <x-page-header :eyebrow="__('Gestão de acesso')" :title="__('Usuários')" />

    @if(session('status'))
        <x-alert type="success" :message="session('status')" />
    @endif

    @if(session('error'))
        <x-alert type="error" :message="session('error')" />
    @endif

    <section class="v-card space-y-3">
        <h2 class="v-section-title">{{ __('Usuários do sistema') }}</h2>
        <p class="text-sm leading-relaxed text-slate-600 dark:text-slate-400">{!!
            __('gestor.users.intro', [
                'link' => '<a href="'.e(route('gestor.pendentes')).'" class="font-semibold text-blue-600 hover:underline dark:text-blue-400">'.e(__('Cadastros pendentes')).'</a>',
            ])
        !!}</p>
    </section>

    <section class="v-card v-card--muted">
        <label for="search" class="v-toolbar-label mb-2">{{ __('Busca inteligente') }}</label>
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end">
            <div class="min-w-0 flex-1">
                <input type="text" id="search" name="search" value="{{ old('search', request('search')) }}"
                       data-live-url="{{ route('gestor.users.index') }}" data-live-param="search"
                       data-live-loading-id="search-loading-users"
                       placeholder="{{ __('Buscar por nome, e-mail ou perfil (ex.: ACE, ACS, gestor)…') }}"
                       class="v-input w-full">
            </div>
            <span id="search-loading-users" class="hidden text-sm text-slate-500 dark:text-slate-400 sm:pb-2" aria-live="polite">{{ __('Buscando…') }}</span>
        </div>
    </section>

    <section class="v-card v-card--flush overflow-hidden">
        @php
            $total = $usuarios->total();
            $count = $usuarios->count();
        @endphp
        <div class="v-table-meta border-b border-slate-200/80 px-4 py-3 dark:border-slate-700/80 sm:px-5">
            <span class="text-sm text-slate-600 dark:text-slate-400">
                @if($total > 0)
                    {{ __('Exibindo :first a :last de :total usuário(s) cadastrado(s).', ['first' => $usuarios->firstItem(), 'last' => $usuarios->lastItem(), 'total' => $total]) }}
                    @if(request('search'))
                        <span class="mt-1 block text-slate-500 sm:mt-0 sm:ml-1 sm:inline">{{ __('Resultados para:') }} <strong class="font-semibold text-slate-800 dark:text-slate-200">{{ request('search') }}</strong></span>
                    @endif
                @else
                    {{ __('Nenhum resultado para os filtros atuais.') }}
                @endif
            </span>
        </div>
        <div class="v-table-wrap">
            <table class="v-data-table">
                <thead>
                    <tr>
                        <th scope="col" class="text-left">{{ __('ID') }}</th>
                        <th scope="col" class="text-left">{{ __('Nome') }}</th>
                        <th scope="col" class="text-left">{{ __('CPF') }}</th>
                        <th scope="col" class="text-left">{{ __('E-mail') }}</th>
                        <th scope="col" class="text-left">{{ __('Perfil') }}</th>
                        <th scope="col" class="text-left">{{ __('2FA') }}</th>
                        <th scope="col" class="text-left">{{ __('Data de cadastro') }}</th>
                        <th scope="col" class="text-center">{{ __('Ações') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($usuarios as $usuario)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40">
                            <td class="font-mono text-xs text-slate-600 dark:text-slate-400">
                                <span class="inline-flex rounded-md bg-slate-100 px-2 py-0.5 font-semibold text-slate-800 dark:bg-slate-800 dark:text-slate-200">#{{ $usuario->use_id }}</span>
                            </td>
                            <td class="font-medium text-slate-900 dark:text-slate-100">{{ $usuario->use_nome }}</td>
                            <td class="text-slate-700 dark:text-slate-300">{{ preg_replace('/\d(?=(?:.*\d){2})/', '*', $usuario->use_cpf) }}</td>
                            <td class="text-slate-700 dark:text-slate-300">
                                <a href="mailto:{{ $usuario->use_email }}" class="text-blue-600 hover:underline dark:text-blue-400">{{ $usuario->use_email }}</a>
                            </td>
                            <td class="text-slate-700 dark:text-slate-300">
                                {{ \App\Models\User::perfilLabel($usuario->use_perfil) }}
                            </td>
                            <td>
                                @if(method_exists($usuario, 'hasEnabledTwoFactorAuthentication') && $usuario->hasEnabledTwoFactorAuthentication())
                                    <span class="inline-flex rounded-md bg-emerald-100 px-2 py-0.5 text-xs font-semibold text-emerald-900 dark:bg-emerald-950/50 dark:text-emerald-200">{{ __('Ativo') }}</span>
                                @else
                                    <span class="text-slate-400 dark:text-slate-500">—</span>
                                @endif
                            </td>
                            <td class="text-slate-600 dark:text-slate-400">{{ $usuario->use_data_criacao->format('d/m/Y') }}</td>
                            <td class="text-center">
                                @if($usuario->use_data_anonimizacao)
                                    <span class="inline-flex rounded-full bg-slate-400 px-2.5 py-1 text-xs font-semibold text-white dark:bg-slate-600">
                                        {{ __('Anonimizado em :data', ['data' => $usuario->use_data_anonimizacao->format('d/m/Y')]) }}
                                    </span>
                                @elseif(!$usuario->use_aprovado)
                                    <span class="inline-flex rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-950 dark:bg-amber-950/60 dark:text-amber-100">
                                        {{ __('Aprovação pendente') }}
                                    </span>
                                @else
                                    <div class="flex justify-center gap-1.5">
                                        <a href="{{ route('gestor.users.edit', $usuario) }}"
                                           class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border border-slate-200/90 bg-white/90 text-slate-600 shadow-sm transition hover:bg-slate-50 hover:text-slate-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500/35 dark:border-slate-600 dark:bg-slate-800/90 dark:text-slate-300 dark:hover:bg-slate-700"
                                           title="{{ __('Editar usuário') }}"
                                           aria-label="{{ __('Editar usuário') }}">
                                            <x-heroicon-o-pencil-square class="h-4 w-4 shrink-0" />
                                        </a>
                                        <form method="POST" action="{{ route('gestor.users.destroy', $usuario) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    onclick="return confirm(@json(__('Tem certeza que deseja anonimizar este usuário? Esta ação não pode ser desfeita.')))"
                                                    class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border border-red-200/90 bg-red-50/90 text-red-700 shadow-sm transition hover:bg-red-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500/40 dark:border-red-900/50 dark:bg-red-950/40 dark:text-red-300 dark:hover:bg-red-950/60"
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
                            <td colspan="8" class="px-4 py-12 text-center sm:px-5">
                                <div class="mx-auto flex max-w-sm flex-col items-center justify-center">
                                    <div class="mb-3 flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 dark:bg-slate-800">
                                        <x-heroicon-o-user-group class="h-7 w-7 shrink-0 text-slate-400 dark:text-slate-500" />
                                    </div>
                                    <p class="font-medium text-slate-700 dark:text-slate-300">{{ __('Nenhum usuário cadastrado.') }}</p>
                                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ __('Novos perfis entram pelo registro na tela de login.') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <x-pagination-relatorio :paginator="$usuarios" :item-label="__('usuários')" />
    </section>

    <section class="v-card flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="v-section-title">{{ __('Usuários pendentes') }}</h2>
            <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
                {{ __('Visualize e aprove cadastros que aguardam liberação para acessar o sistema.') }}
            </p>
        </div>
        <a href="{{ route('gestor.pendentes') }}" class="v-btn-primary inline-flex shrink-0 items-center justify-center gap-2 px-5 py-2.5 text-[13px] font-semibold">
            <x-heroicon-o-chevron-right class="h-4 w-4 shrink-0" aria-hidden="true" />
            {{ __('Ir para cadastros pendentes') }}
        </a>
    </section>
</div>
@endsection
