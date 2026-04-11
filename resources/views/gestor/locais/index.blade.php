<!-- resources/views/gestor/locais/index.blade.php -->
@extends('layouts.app')

@section('og_title', config('app.name') . ' · ' . __('Locais'))
@section('og_description', __('Locais cadastrados pelos profissionais de campo (ACE/ACS). Consulte endereços, códigos e coordenadas da base territorial.'))

@section('content')
<div class="v-page v-page--wide v-page--dense">
    <x-breadcrumbs :items="[['label' => __('Página Inicial'), 'url' => route('dashboard')], ['label' => __('Locais')]]" />
    <x-page-header :eyebrow="__('Cadastro territorial')" :title="__('Locais')">
        <x-slot name="lead">
            <p>{{ __('Locais registrados pelos profissionais (ACE/ACS). Consulte endereço, código único e coordenadas.') }}</p>
        </x-slot>
    </x-page-header>

    <x-flash-alerts />

    @if(!empty($coordenadasDuplicadas))
        <x-ui.callout variant="amber" class="v-alert-erp border-amber-200/60 dark:border-amber-900/40" role="alert">
            <p class="text-sm font-medium text-amber-950 dark:text-amber-100">{{ __('Coordenadas duplicadas') }}</p>
            <p class="mt-1 text-sm text-amber-900/90 dark:text-amber-200/85">{{ __('Existem imóveis com a mesma coordenada (latitude e longitude). Revise os locais para evitar duplicidade.') }}</p>
        </x-ui.callout>
    @endif

    <x-section-card class="v-card--flush overflow-hidden">
        <div class="v-list-toolbar">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div class="min-w-0 flex-1 space-y-2">
                    <label for="search" class="v-toolbar-label">{{ __('Busca inteligente') }}</label>
                    <div class="flex items-center gap-2">
                        <input type="text" id="search" name="search" value="{{ old('search', request('search')) }}"
                               data-live-url="{{ route('gestor.locais.index') }}" data-live-param="search"
                               data-live-loading-id="search-loading-gestor-locais"
                               placeholder="{{ __('Endereço, bairro, código, tipo (residencial, comercial, terreno) ou zona (urbano, rural)…') }}"
                               class="v-input" />
                        <span id="search-loading-gestor-locais" class="hidden shrink-0 text-xs text-slate-500 dark:text-slate-400" aria-live="polite">{{ __('Buscando…') }}</span>
                    </div>
                </div>
                <div class="w-full shrink-0 lg:w-72 xl:w-80">
                    <x-ui.disclosure variant="footer-lgpd">
                        <x-slot name="summary">
                            <span>{{ __('O que significa "Primário"?') }}</span>
                        </x-slot>
                        <p class="text-[10px] leading-relaxed">{!! __('O local <strong>primário</strong> é o endereço de referência do município (cidade/estado) no sistema. Foi configurado previamente pelo gestor e não pode ser editado nem excluído pela interface. Os demais locais são os imóveis visitados pelos profissionais (ACE/ACS).') !!}</p>
                    </x-ui.disclosure>
                </div>
            </div>
        </div>
        <div class="v-table-meta">
            <span>
                {{ __('Exibindo :atual de :total local(is) cadastrados.', ['atual' => $locais->count(), 'total' => $locais->total()]) }}
                @if(request('search'))
                    <span class="text-slate-500 dark:text-slate-500">{{ __('Resultados para:') }} <strong class="text-slate-700 dark:text-slate-300">{{ request('search') }}</strong></span>
                @endif
            </span>
        </div>
        <div class="v-table-wrap">
            <table class="v-data-table">
                <thead>
                    <tr>
                        <th scope="col">{{ __('Código') }}</th>
                        <th scope="col">{{ __('Zona') }}</th>
                        <th scope="col">{{ __('Imóvel') }}</th>
                        <th scope="col">{{ __('Endereço') }}</th>
                        <th scope="col">{{ __('Bairro') }}</th>
                        <th scope="col">{{ __('Cidade') }}</th>
                        <th scope="col">{{ __('Responsável') }}</th>
                        <th scope="col">{{ __('Coordenadas') }}</th>
                        <th scope="col" class="text-center">{{ __('Ações') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($locais as $local)
                        <tr>
                            <td>
                                <span class="inline-flex rounded-lg bg-slate-100 px-2 py-1 text-xs font-semibold tabular-nums text-slate-800 dark:bg-slate-800 dark:text-slate-200">#{{ $local->loc_codigo_unico }}</span>
                                @if($local->isPrimary())
                                    <span class="mt-1 block text-xs font-medium text-slate-500 dark:text-slate-400" title="{{ __('Local primário do município') }}">{{ __('Primário') }}</span>
                                @endif
                            </td>
                            <td>
                                @if ($local->loc_zona == 'U')
                                    <span class="inline-flex rounded-md bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-800 dark:bg-slate-800 dark:text-slate-200">{{ __('Urbana') }}</span>
                                @elseif ($local->loc_zona == 'R')
                                    <span class="inline-flex rounded-md bg-amber-100 px-2 py-0.5 text-xs font-semibold text-amber-900 dark:bg-amber-950/60 dark:text-amber-200">{{ __('Rural') }}</span>
                                @endif
                            </td>
                            <td class="leading-tight text-slate-600 dark:text-slate-300">
                                @if ($local->loc_tipo == 'R')
                                    <span title="{{ __('Residencial') }}">{{ __('Residencial') }}</span>
                                @elseif ($local->loc_tipo == 'C')
                                    <span title="{{ __('Comercial') }}">{{ __('Comercial') }}</span>
                                @elseif ($local->loc_tipo == 'T')
                                    <span title="{{ __('Terreno baldio') }}">{{ __('Terreno baldio') }}</span>
                                @endif
                            </td>
                            <td class="leading-snug">
                                <span class="font-medium text-slate-900 dark:text-slate-100">{{ $local->loc_endereco }}</span>,
                                @if($local->loc_numero){{ $local->loc_numero }}@else<span class="text-slate-500">{{ __('N/D') }}</span>@endif
                            </td>
                            <td>{{ $local->loc_bairro }}</td>
                            <td>{{ $local->loc_cidade }}</td>
                            <td>{{ $local->loc_responsavel_nome ?? __('Não informado') }}</td>
                            <td class="tabular-nums text-xs text-slate-600 dark:text-slate-400">{{ $local->loc_latitude }}, {{ $local->loc_longitude }}</td>
                            <td class="text-center">
                                <a href="{{ route('gestor.locais.show', $local) }}"
                                    class="v-btn-icon-primary"
                                    title="{{ __('Visualizar') }}"
                                    aria-label="{{ __('Visualizar local') }}">
                                    <x-heroicon-o-eye class="h-4 w-4 shrink-0" />
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="!p-0">
                                <div class="v-empty-state px-4">
                                    <div class="v-empty-state__icon" aria-hidden="true">
                                        <x-heroicon-o-map-pin class="h-7 w-7 shrink-0" />
                                    </div>
                                    <p class="v-empty-state__title">{{ __('Nenhum local cadastrado.') }}</p>
                                    <p class="v-empty-state__text">{{ __('Os locais aparecerão aqui quando os profissionais (ACE/ACS) os cadastrarem.') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <x-pagination-relatorio :paginator="$locais" item-label="locais" />
    </x-section-card>
</div>
@endsection
