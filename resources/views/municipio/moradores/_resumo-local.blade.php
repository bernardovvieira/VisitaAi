{{-- Requer: $local, $moradorResumo — apenas telas de local gestor/ACE (não e-SUS / não ACS). --}}
@php
    $rp = request()->routeIs('gestor.*') ? 'gestor' : 'agente';
@endphp
<section class="rounded-xl border border-gray-200/80 bg-white p-5 shadow-sm dark:border-gray-600 dark:bg-gray-800 space-y-3">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">{{ config('visitaai_municipio.ocupantes.titulo_secao_local') }}</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ config('visitaai_municipio.ocupantes.disclaimer') }}</p>
        </div>
        <a href="{{ route($rp . '.locais.moradores.index', $local) }}"
           class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500/60">
            {{ config('visitaai_municipio.ocupantes.botao_gerenciar') }} ({{ $moradorResumo['total'] ?? 0 }})
        </a>
    </div>
    @if(($moradorResumo['total'] ?? 0) > 0)
        <dl class="grid grid-cols-2 sm:grid-cols-5 gap-2 text-xs sm:text-sm text-gray-700 dark:text-gray-300 pt-2 border-t border-gray-200 dark:border-gray-600">
            <div><dt class="text-gray-500 dark:text-gray-400">0–11 anos</dt><dd class="font-semibold">{{ $moradorResumo['faixas']['0-11'] }}</dd></div>
            <div><dt class="text-gray-500 dark:text-gray-400">12–17</dt><dd class="font-semibold">{{ $moradorResumo['faixas']['12-17'] }}</dd></div>
            <div><dt class="text-gray-500 dark:text-gray-400">18–59</dt><dd class="font-semibold">{{ $moradorResumo['faixas']['18-59'] }}</dd></div>
            <div><dt class="text-gray-500 dark:text-gray-400">60+</dt><dd class="font-semibold">{{ $moradorResumo['faixas']['60+'] }}</dd></div>
            <div><dt class="text-gray-500 dark:text-gray-400">Sem data</dt><dd class="font-semibold">{{ $moradorResumo['faixas']['sem_info'] }}</dd></div>
        </dl>
    @endif
</section>
