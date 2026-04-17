{{-- Requer: $local, $moradorResumo | telas de local gestor/ACE/ACS. --}}
@php
    $rp = auth()->user()->locaisRouteProfile();
    $ehGestor = auth()->user()->isGestor();
@endphp
<x-section-card class="space-y-3">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div class="min-w-0 flex-1 space-y-2">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">{{ config('visitaai_municipio.ocupantes.titulo_secao_local') }}</h2>
        </div>
        <a href="{{ route($rp . '.locais.moradores.index', $local) }}"
           class="v-btn-compact v-btn-compact--blue shrink-0 self-start">
            @if($ehGestor)
                {{ __('Ver ocupantes e documentos') }} ({{ $moradorResumo['total'] ?? 0 }})
            @else
                {{ config('visitaai_municipio.ocupantes.botao_gerenciar') }} ({{ $moradorResumo['total'] ?? 0 }})
            @endif
        </a>
    </div>
    @if(($moradorResumo['total'] ?? 0) > 0)
        <dl class="grid grid-cols-2 sm:grid-cols-5 gap-2 border-t border-gray-200 pt-2 text-xs text-gray-700 dark:border-gray-600 dark:text-gray-300 sm:text-sm">
            <div><dt class="text-gray-500 dark:text-gray-400">{{ __('0 a 11 anos') }}</dt><dd class="font-semibold">{{ $moradorResumo['faixas']['0-11'] }}</dd></div>
            <div><dt class="text-gray-500 dark:text-gray-400">{{ __('12 a 17 anos') }}</dt><dd class="font-semibold">{{ $moradorResumo['faixas']['12-17'] }}</dd></div>
            <div><dt class="text-gray-500 dark:text-gray-400">{{ __('18 a 59 anos') }}</dt><dd class="font-semibold">{{ $moradorResumo['faixas']['18-59'] }}</dd></div>
            <div><dt class="text-gray-500 dark:text-gray-400">{{ __('60 anos ou mais') }}</dt><dd class="font-semibold">{{ $moradorResumo['faixas']['60+'] }}</dd></div>
            <div><dt class="text-gray-500 dark:text-gray-400">{{ __('Sem data de nascimento') }}</dt><dd class="font-semibold">{{ $moradorResumo['faixas']['sem_info'] }}</dd></div>
        </dl>
    @endif
</x-section-card>
