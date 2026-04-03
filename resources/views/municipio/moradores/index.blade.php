@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-7xl space-y-6">
    <x-breadcrumbs :items="array_filter([
        ['label' => __('Página Inicial'), 'url' => route('dashboard')],
        ['label' => __('Locais'), 'url' => route($profile . '.locais.index')],
        ['label' => __('Visualizar'), 'url' => route($profile . '.locais.show', $local)],
        ['label' => 'Moradores'],
    ])" />

    @if(session('success'))
        <x-alert type="success" :message="session('success')" />
    @endif
    @if(session('warning'))
        <x-alert type="warning" :message="session('warning')" />
    @endif
    @if(session('error'))
        <x-alert type="error" :message="session('error')" />
    @endif

    <div class="v-card flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-end">
        <a href="{{ route($profile . '.locais.moradores.create', $local) }}"
           class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500/60 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-gray-900">
            <x-heroicon-o-plus class="mr-2 h-4 w-4 shrink-0" />
            {{ __('Cadastrar ocupante') }}
        </a>
    </div>

    <section class="v-card space-y-3 dark:bg-gray-800">
        <h1 class="text-xl font-semibold tracking-tight text-gray-900 dark:text-gray-100">{{ config('visitaai_municipio.ocupantes.titulo_listagem') }}</h1>
        <p class="text-sm text-gray-600 dark:text-gray-400">
            {{ __('Código do imóvel') }}: <span class="font-mono font-semibold text-gray-900 dark:text-gray-100">{{ $local->loc_codigo_unico }}</span>
            · {{ $local->loc_endereco }}, {{ $local->loc_numero ?? 'S/N' }}, {{ $local->loc_bairro }}
        </p>
    </section>

    <section class="v-card border-amber-200/90 bg-amber-50 text-xs leading-relaxed text-amber-950 dark:border-amber-800 dark:bg-amber-950/35 dark:text-amber-100">
        {{ config('visitaai_municipio.ocupantes.disclaimer') }}
    </section>

    <div class="v-card v-card--flush overflow-hidden dark:bg-gray-800">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900/80">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">{{ __('Nome / identificação') }}</th>
                        <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">{{ __('Nascimento') }}</th>
                        <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">{{ __('Idade') }}</th>
                        <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">{{ __('Escolaridade') }}</th>
                        <th scope="col" class="px-4 py-3 text-right font-semibold text-gray-700 dark:text-gray-300">{{ __('Ações') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse ($moradores as $m)
                        <tr class="text-gray-800 transition-colors hover:bg-gray-50/80 dark:text-gray-200 dark:hover:bg-gray-900/40">
                            <td class="px-4 py-3">{{ $m->mor_nome ?: '-' }}</td>
                            <td class="px-4 py-3 tabular-nums">{{ $m->mor_data_nascimento ? $m->mor_data_nascimento->format('d/m/Y') : '-' }}</td>
                            <td class="px-4 py-3">{{ $m->idadeAnos() !== null ? $m->idadeAnos() . ' ' . __('anos') : '-' }}</td>
                            <td class="px-4 py-3">{{ $m->mor_escolaridade ? (config('visitaai_municipio.escolaridade_opcoes.' . $m->mor_escolaridade) ?: $m->mor_escolaridade) : '-' }}</td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <div class="inline-flex justify-end gap-1.5">
                                    <a href="{{ route($profile . '.locais.moradores.edit', [$local, $m]) }}"
                                       class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-600 shadow-sm transition hover:bg-slate-50 hover:text-slate-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400/40 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700"
                                       title="{{ __('Editar') }}"
                                       aria-label="{{ __('Editar ocupante') }}">
                                        <x-heroicon-o-pencil-square class="h-4 w-4 shrink-0" />
                                    </a>
                                    <form action="{{ route($profile . '.locais.moradores.destroy', [$local, $m]) }}" method="post" class="inline" onsubmit="return confirm(@js(__('Excluir este registro de ocupante?')));">
                                        @csrf
                                        @method('delete')
                                        <button type="submit"
                                                class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border border-red-200 bg-red-50 text-red-600 shadow-sm transition hover:bg-red-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-400/40 dark:border-red-900/50 dark:bg-red-950/40 dark:text-red-300 dark:hover:bg-red-950/60"
                                                title="{{ __('Excluir') }}"
                                                aria-label="{{ __('Excluir ocupante') }}">
                                            <x-heroicon-o-trash class="h-4 w-4 shrink-0" />
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-gray-500 dark:text-gray-400">{{ __('Nenhum ocupante registrado neste imóvel.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($moradores->hasPages())
            <div class="border-t border-gray-200 px-4 py-3 dark:border-gray-600">{{ $moradores->links() }}</div>
        @endif
    </div>
</div>
@endsection
