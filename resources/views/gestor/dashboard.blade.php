<!-- resources/views/gestor/dashboard.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-6 text-gray-900 dark:text-gray-100">Painel do Gestor</h1>

    <!-- Mensagem de boas-vindas -->
    <section class="mb-8 p-4 bg-white dark:bg-gray-700 rounded-lg shadow">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Bem-vindo, {{ Auth::user()->use_nome }}</h2>
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
    <section class="space-y-4">
        <header><h2 class="text-xl font-semibold mb-2 text-gray-800 dark:text-gray-200">Estatísticas Principais</h2></header>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Total de Agentes -->
            <div class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-500 dark:text-indigo-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a7 7 0 00-14 0v2h5m4-4a4 4 0 10-8 0h8zM10 8a4 4 0 118 0 4 4 0 01-8 0z" />
                    </svg>
                    <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200">Total de Agentes</h3>
                </div>
                <p class="mt-2 text-3xl text-gray-900 dark:text-gray-100">{{ \App\Models\User::where('use_perfil','agente')->where('use_aprovado', true)->count() }}</p>
            </div>
            <!-- Agentes Pendentes -->
            <div class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-500 dark:text-yellow-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0zM12 9v4m0 4h.01" />
                    </svg>
                    <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200">Agentes Pendentes</h3>
                </div>
                <p class="mt-2 text-3xl text-gray-900 dark:text-gray-100">{{ \App\Models\User::where('use_perfil','agente')->where('use_aprovado', false)->count() }}</p>
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
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400 dark:text-gray-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3v18h18M9 17v-6m4 6v-10m4 10v-4" />
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
                <p class="mt-2 text-3xl text-gray-900 dark:text-gray-100">--</p>
            </div>
        </div>
    </section>

    <!-- Ações Rápidas -->
    <section class="mt-8 space-y-4">
        <header><h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Ações Rápidas</h2></header>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <a href="{{ route('gestor.pendentes') }}" class="flex items-center justify-center px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-100 rounded shadow-sm transition duration-150 ease-in-out hover:shadow-lg dark:hover:bg-gray-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-gray-500 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                Usuários Pendentes
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
                Gerenciar Doenças
            </a>
            <a href="#" class="flex items-center justify-center px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-100 rounded shadow-sm transition duration-150 ease-in-out hover:shadow-lg dark:hover:bg-gray-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-gray-500 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                Gerenciar Visitas
            </a>
            <a href="#" class="flex items-center justify-center px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-100 rounded shadow-sm transition duration-150 ease-in-out hover:shadow-lg dark:hover:bg-gray-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-gray-500 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                Gerar Relatórios
            </a>
            <a href="#" class="flex items-center justify-center px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-100 rounded shadow-sm transition duration-150 ease-in-out hover:shadow-lg dark:hover:bg-gray-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-gray-500 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                Logs de Atividade
            </a>
        </div>
    </section>

    <!-- Suporte Técnico -->
    <section class="mt-8">
        <header><h2 class="text-xl font-semibold mb-2 text-gray-800 dark:text-gray-200">Ajuda e Suporte</h2></header>
        <div class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow">
            <p class="mt-2 text-gray-600 dark:text-gray-400">Encontrou algum problema? Contate:</p>
            <ul class="mt-2 space-y-1 text-gray-600 dark:text-gray-400">
                <li><b>E-mail:</b> <a href="mailto:bernardo@bitwise.dev.br" class="underline text-gray-800 dark:text-gray-100 hover:text-black dark:hover:text-gray-200 transition-colors">bernardo@bitwise.dev.br</a></li>
                <li><b>WhatsApp:</b> <a href="https://wa.me/5554996605584" class="underline text-gray-800 dark:text-gray-100 hover:text-black dark:hover:text-gray-200 transition-colors">+55 54 9 9660-5584</a></li>
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