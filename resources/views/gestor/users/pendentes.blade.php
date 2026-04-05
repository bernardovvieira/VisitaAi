@extends('layouts.app')

@section('content')
<div class="v-page">
    <x-breadcrumbs :items="[['label' => __('Página Inicial'), 'url' => route('dashboard')], ['label' => __('Usuários'), 'url' => route('gestor.users.index')], ['label' => __('Pendentes')]]" />

    <x-page-header :eyebrow="__('Aprovações')" :title="__('Usuários pendentes')" />

    <x-flash-alerts />

    <!-- Card introdutório -->
    <x-section-card class="space-y-2 dark:bg-gray-800">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">{{ __('gestor.pendentes.intro_title') }}</h2>
        <p class="text-sm text-gray-600 dark:text-gray-400">
            {{ __('gestor.pendentes.intro_lead') }}
        </p>
    </x-section-card>

    <!-- Tabela de Pendentes -->
    <x-section-card class="space-y-2 dark:bg-gray-800">
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
            @if($pendentes->total() > 0)
                {{ __('gestor.pendentes.table_summary', ['visible' => $pendentes->count(), 'total' => $pendentes->total()]) }}
            @else
                {{ __('gestor.pendentes.empty_table') }}
            @endif
        </p>

        <div class="v-table-wrap">
            <table class="v-data-table">
                <thead>
                    <tr>
                        <th scope="col" class="whitespace-nowrap">{{ __('ID') }}</th>
                        <th scope="col">{{ __('Nome') }}</th>
                        <th scope="col">{{ __('CPF') }}</th>
                        <th scope="col">{{ __('E-mail') }}</th>
                        <th scope="col">{{ __('Data de Cadastro') }}</th>
                        <th scope="col" class="text-center">{{ __('Ação') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendentes as $u)
                        <tr>
                            <td class="whitespace-nowrap">
                                <span class="inline-flex rounded-lg bg-slate-100 px-2 py-1 text-xs font-semibold tabular-nums text-slate-800 dark:bg-slate-800 dark:text-slate-200">
                                    #{{ $u->use_id }}
                                </span>
                            </td>
                            <td class="font-medium text-slate-900 dark:text-slate-100">{{ $u->use_nome }}</td>
                            <td class="tabular-nums text-slate-700 dark:text-slate-300">{{ preg_replace('/\d(?=(?:.*\d){2})/', '*', $u->use_cpf) }}</td>
                            <td><a href="mailto:{{ $u->use_email }}" class="text-blue-600 hover:underline dark:text-blue-400">{{ $u->use_email }}</a></td>
                            <td class="tabular-nums">{{ $u->use_data_criacao->format('d/m/Y') }}</td>
                            <td class="text-center">
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
                            <td colspan="6" class="!p-0">
                                <div class="px-4 py-8 text-center text-sm text-slate-600 dark:text-slate-400">
                                    {{ __('gestor.pendentes.empty_table') }}
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <x-pagination-relatorio :paginator="$pendentes" :item-label="__('gestor.pendentes.pagination_item')" />
    </x-section-card>

</div>
@endsection
