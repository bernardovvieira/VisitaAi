<!-- resources/views/gestor/dashboard.blade.php -->
@extends('layouts.app')

@section('og_title', config('app.name') . ' — Painel do Gestor')
@section('og_description', 'Painel do gestor municipal. Acompanhe estatísticas e gerencie doenças, locais, visitas, usuários e relatórios.')

@section('content')
<div class="space-y-6">
    <h1 class="text-2xl font-bold mb-6 text-gray-900 dark:text-gray-100">Painel do Gestor</h1>

    <!-- Mensagem de boas-vindas -->
    <section class="mb-8 p-4 bg-white dark:bg-gray-700 rounded-lg shadow">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Bem-vindo(a), {{ Auth::user()->use_nome }}</h2>
        <p class="mt-2 text-gray-600 dark:text-gray-400">O sistema Visita Aí organiza e facilita o gerenciamento de Agentes de Epidemiologia e suas visitas. Acompanhe abaixo as principais estatísticas e acesse rapidamente as funcionalidades.</p>
        <br>
        <!-- Relógio -->
        <div class="flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500 dark:text-gray-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l4 2M12 4a8 8 0 100 16 8 8 0 000-16z" />
            </svg>
            <span id="clock" class="text-gray-700 dark:text-gray-300"></span>
        </div>  
    </section>

    <!-- Estatísticas Principais -->
    @php
        $pendentesCount = \App\Models\User::where(function($q) { $q->where('use_perfil', 'agente_endemias')->orWhere('use_perfil', 'agente_saude'); })->where('use_aprovado', false)->count();
    @endphp
    <section class="space-y-4">
        <header><h2 class="text-xl font-semibold mb-2 text-gray-800 dark:text-gray-200">Estatísticas Principais</h2></header>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Total de Agentes -->
            <div class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-500 dark:text-indigo-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-4.42 0-8 1.79-8 4v2h16v-2c0-2.21-3.58-4-8-4z" />
                    </svg>
                    <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200">Total de Agentes</h3>
                </div>
                <p class="mt-2 text-3xl text-gray-900 dark:text-gray-100">    {{ \App\Models\User::where(function($query) { $query->where('use_perfil', 'agente_endemias')->orWhere('use_perfil', 'agente_saude'); })->where('use_aprovado', true)->count() }}</p>
            </div>
            <!-- Agentes Pendentes -->
            <div class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow {{ $pendentesCount > 0 ? 'ring-2 ring-amber-400 dark:ring-amber-500 border-2 border-amber-400 dark:border-amber-500' : '' }}">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-500 dark:text-yellow-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0zM12 9v4m0 4h.01" />
                    </svg>
                    <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200">Agentes Pendentes</h3>
                    @if($pendentesCount > 0)
                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-200">Atenção</span>
                    @endif
                </div>
                <p class="mt-2 text-3xl text-gray-900 dark:text-gray-100">{{ $pendentesCount }}</p>
            </div>
            <!-- Total de Gestores -->
            <div class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-500 dark:text-green-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zm-4 8c-4.418 0-8 1.79-8 4v1h16v-1c0-2.21-3.582-4-8-4z" />
                    </svg>
                    <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200">Total de Gestores</h3>
                </div>
                <p class="mt-2 text-3xl text-gray-900 dark:text-gray-100">{{ \App\Models\User::where('use_perfil','gestor')->count() }}</p>
            </div>
            <!-- Total de Doenças -->
            <div class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-teal-400 dark:text-teal-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <circle cx="12" cy="12" r="3" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="
                            M12 2v2
                            M12 20v2
                            M4.93 4.93l1.41 1.41
                            M17.66 17.66l1.41 1.41
                            M2 12h2
                            M20 12h2
                            M4.93 19.07l1.41-1.41
                            M17.66 6.34l1.41-1.41" />
                    </svg>
                    <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200">Total de Doenças</h3>
                </div>
                <p class="mt-2 text-3xl text-gray-900 dark:text-gray-100">{{ \App\Models\Doenca::count() }}</p>
            </div>
            <!-- Total de Visitas -->
            <div class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400 dark:text-gray-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3v18h18M9 17v-6m4 6v-10m4 10v-4" />
                    </svg>
                    <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200">Total de Visitas</h3>
                </div>
                <p class="mt-2 text-3xl text-gray-900 dark:text-gray-100">{{ \App\Models\Visita::count() }}</p>
            </div>
        </div>
    </section>

    <!-- Insights do período -->
    @php
        $visitasComPendencia = \App\Models\Visita::where('vis_pendencias', true)->count();
        $inicioMes = now()->startOfMonth();
        $doencaMaisMes = \Illuminate\Support\Facades\DB::table('monitoradas')
            ->join('visitas', 'visitas.vis_id', '=', 'monitoradas.fk_visita_id')
            ->join('doencas', 'doencas.doe_id', '=', 'monitoradas.fk_doenca_id')
            ->where('visitas.vis_data', '>=', $inicioMes)
            ->select('doencas.doe_nome', \Illuminate\Support\Facades\DB::raw('COUNT(*) as total'))
            ->groupBy('doencas.doe_id', 'doencas.doe_nome')
            ->orderByDesc('total')
            ->first();
        $visitasEsteMes = \App\Models\Visita::where('vis_data', '>=', $inicioMes)->count();
    @endphp
    <section class="mt-8 space-y-4">
        <header><h2 class="text-xl font-semibold mb-2 text-gray-800 dark:text-gray-200">Insights do período</h2></header>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Visitas com pendência -->
            <div class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow {{ $visitasComPendencia > 0 ? 'ring-2 ring-red-500 dark:ring-red-500 border-2 border-red-500 dark:border-red-500' : '' }}">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 {{ $visitasComPendencia > 0 ? 'text-red-500 dark:text-red-400' : 'text-gray-500 dark:text-gray-400' }} mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200">Visitas com pendência</h3>
                </div>
                <p class="mt-2 text-3xl text-gray-900 dark:text-gray-100">{{ $visitasComPendencia }}</p>
            </div>
            <!-- Visitas neste mês -->
            <div class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-emerald-500 dark:text-emerald-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200">Visitas neste mês</h3>
                </div>
                <p class="mt-2 text-3xl text-gray-900 dark:text-gray-100">{{ $visitasEsteMes }}</p>
            </div>
            @if ($doencaMaisMes && $doencaMaisMes->total > 0)
            <!-- Destaque do mês -->
            <div class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500 dark:text-blue-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200 truncate" title="{{ $doencaMaisMes->doe_nome }}">Destaque do mês</h3>
                </div>
                <p class="mt-2 text-3xl text-gray-900 dark:text-gray-100 truncate" title="{{ $doencaMaisMes->doe_nome }}">{{ $doencaMaisMes->doe_nome }}</p>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $doencaMaisMes->total }} registro(s)</p>
            </div>
            @endif
        </div>
    </section>

    <!-- Ações Rápidas -->
    <section class="mt-8 space-y-4">
        <header><h2 class="text-xl font-semibold mb-2 text-gray-800 dark:text-gray-200">Ações Rápidas</h2></header>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <a href="{{ route('gestor.pendentes') }}" class="flex items-center justify-center px-4 py-2 rounded shadow-sm transition duration-150 ease-in-out hover:shadow-lg {{ $pendentesCount > 0 ? 'bg-amber-100 dark:bg-amber-900/40 text-amber-900 dark:text-amber-100 border-2 border-amber-400 dark:border-amber-500' : 'bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-100 dark:hover:bg-gray-500' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 {{ $pendentesCount > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-gray-500 dark:text-gray-300' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                Usuários Pendentes
                @if($pendentesCount > 0)
                    <span class="ml-2 inline-flex items-center justify-center w-6 h-6 rounded-full bg-amber-500 dark:bg-amber-600 text-white text-xs font-bold">{{ $pendentesCount }}</span>
                @endif
            </a>
            <a href="{{ route('gestor.users.index') }}" class="flex items-center justify-center px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-100 rounded shadow-sm transition duration-150 ease-in-out hover:shadow-lg dark:hover:bg-gray-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-gray-500 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                Gerenciar Usuários
            </a>
            <a href="{{ route('gestor.doencas.index') }}" class="flex items-center justify-center px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-100 rounded shadow-sm transition duration-150 ease-in-out hover:shadow-lg dark:hover:bg-gray-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-gray-500 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                Doenças Monitoradas
            </a>
            <a href="{{ route('gestor.visitas.index') }}" class="flex items-center justify-center px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-100 rounded shadow-sm transition duration-150 ease-in-out hover:shadow-lg dark:hover:bg-gray-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-gray-500 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                Visitas Realizadas
            </a>
            <a href="{{ route('gestor.relatorios.index') }}" class="flex items-center justify-center px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-100 rounded shadow-sm transition duration-150 ease-in-out hover:shadow-lg dark:hover:bg-gray-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-gray-500 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                Visualizar Relatórios
            </a>
            <a href="{{ route('gestor.logs.index') }}" class="flex items-center justify-center px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-100 rounded shadow-sm transition duration-150 ease-in-out hover:shadow-lg dark:hover:bg-gray-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-gray-500 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                Auditoria
            </a>
        </div>
    </section>

    <!-- Suporte Técnico -->
    <section class="mt-8">
        <header><h2 class="text-xl font-semibold mb-2 text-gray-800 dark:text-gray-200">Ajuda e Suporte</h2></header>
        <div class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow">
            <p class="mt-2 text-gray-600 dark:text-gray-400">Encontrou algum problema? Contate a <em>Bitwise Technologies</em>:</p>
            <ul class="mt-2 space-y-1 text-gray-600 dark:text-gray-400">
                <li><b>Site:</b> <a href="https://bitwise.dev.br" target="_blank" rel="noopener" class="underline text-gray-800 dark:text-gray-100 hover:text-black dark:hover:text-gray-200 transition-colors">bitwise.dev.br</a></li>
                <li><b>E-mail:</b> <a href="mailto:bernardo@bitwise.dev.br" class="underline text-gray-800 dark:text-gray-100 hover:text-black dark:hover:text-gray-200 transition-colors">bernardo@bitwise.dev.br</a></li>
            </ul>
        </div>
    </section>
</div>

<!-- Scripts -->
<script>
    function updateClock() {
        const now = new Date();
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit' };
        document.getElementById('clock').innerText = now.toLocaleDateString('pt-BR', options);
    }
    setInterval(updateClock, 1000);
    updateClock();
</script>
@endsection