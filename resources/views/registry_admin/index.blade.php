@extends('layouts.app')

@section('og_title', config('app.name') . ' · ' . __('Registry de tenants'))
@section('og_description', __('Gestão técnica dos tenants (subdomínio → base de dados).'))

@section('content')
<div class="v-page space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <x-page-header :eyebrow="__('Sistema')" :title="__('Registry de tenants')" />
        <a href="{{ route('registry.admin.create') }}" class="v-btn-compact v-btn-compact--blue shrink-0">
            <x-heroicon-o-plus class="h-4 w-4 shrink-0" />
            {{ __('Novo') }}
        </a>
    </div>

    <x-flash-alerts />

    <x-section-card class="dark:bg-gray-800">
        @if($tenants->isEmpty())
            <p class="text-sm text-slate-600 dark:text-slate-400">{{ __('Nenhum tenant. Crie o primeiro para mapear subdomínio → MySQL.') }}</p>
        @else
            <div class="v-table-wrap">
                <table class="v-data-table">
                    <thead>
                        <tr>
                            <th>{{ __('Slug') }}</th>
                            <th>{{ __('Ambiente') }}</th>
                            <th>{{ __('Database') }}</th>
                            <th>{{ __('Ativo') }}</th>
                            <th class="text-right">{{ __('Ações') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tenants as $t)
                            <tr>
                                <td class="font-mono text-sm">{{ $t->slug }}</td>
                                <td>{{ $t->environment }}</td>
                                <td class="font-mono text-xs">{{ $t->database }}</td>
                                <td>{{ $t->active ? __('Sim') : __('Não') }}</td>
                                <td class="text-right whitespace-nowrap">
                                    <a href="{{ route('registry.admin.edit', $t) }}" class="text-blue-600 hover:underline dark:text-blue-400">{{ __('Editar') }}</a>
                                    <form action="{{ route('registry.admin.destroy', $t) }}" method="POST" class="inline" onsubmit="return confirm(@json(__('Remover este tenant do registry?')));">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="ml-3 text-red-600 hover:underline dark:text-red-400">{{ __('Remover') }}</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-section-card>
</div>
@endsection
