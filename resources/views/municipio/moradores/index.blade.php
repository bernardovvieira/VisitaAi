@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-7xl space-y-6">
    <x-breadcrumbs :items="array_filter([
        ['label' => 'Página Inicial', 'url' => route('dashboard')],
        ['label' => 'Locais', 'url' => route($profile . '.locais.index')],
        ['label' => 'Visualizar', 'url' => route($profile . '.locais.show', $local)],
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

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-end">
        <a href="{{ route($profile . '.locais.moradores.create', $local) }}"
           class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500/60 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-gray-900">
            <x-heroicon-o-plus class="mr-2 h-4 w-4 shrink-0" />
            {{ __('Cadastrar ocupante') }}
        </a>
    </div>

    <section class="space-y-3 rounded-xl border border-gray-200/80 bg-white p-5 shadow-sm dark:border-gray-600 dark:bg-gray-800">
        <h1 class="text-xl font-semibold tracking-tight text-gray-900 dark:text-gray-100">{{ config('visitaai_municipio.ocupantes.titulo_listagem') }}</h1>
        <p class="text-sm text-gray-600 dark:text-gray-400">
            {{ __('Código do imóvel') }}: <span class="font-mono font-semibold text-gray-900 dark:text-gray-100">{{ $local->loc_codigo_unico }}</span>
            · {{ $local->loc_endereco }}, {{ $local->loc_numero ?? 'S/N' }}, {{ $local->loc_bairro }}
        </p>
        <p class="rounded-lg border border-amber-200/90 bg-amber-50 px-3 py-2.5 text-xs leading-relaxed text-amber-950 dark:border-amber-800 dark:bg-amber-950/35 dark:text-amber-100">
            {{ config('visitaai_municipio.ocupantes.disclaimer') }}
        </p>
    </section>

    <div class="overflow-hidden rounded-xl border border-gray-200/80 bg-white shadow-sm dark:border-gray-600 dark:bg-gray-800">
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
                            <td class="space-x-3 px-4 py-3 text-right whitespace-nowrap">
                                <a href="{{ route($profile . '.locais.moradores.edit', [$local, $m]) }}" class="font-medium text-blue-700 underline-offset-2 hover:underline dark:text-blue-400">{{ __('Editar') }}</a>
                                <form action="{{ route($profile . '.locais.moradores.destroy', [$local, $m]) }}" method="post" class="inline" onsubmit="return confirm(@js(__('Excluir este registro de ocupante?')));">
                                    @csrf
                                    @method('delete')
                                    <button type="submit" class="font-medium text-red-600 underline-offset-2 hover:underline dark:text-red-400">{{ __('Excluir') }}</button>
                                </form>
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
