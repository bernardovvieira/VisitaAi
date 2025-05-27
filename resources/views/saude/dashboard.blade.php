@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-6 text-gray-900 dark:text-gray-100">Painel do Agente de Saúde</h1>

    <!-- Mensagem de boas-vindas -->
    <section class="mb-8 p-4 bg-white dark:bg-gray-700 rounded-lg shadow">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Bem-vindo, {{ Auth::user()->use_nome }}</h2>
        <p class="mt-2 text-gray-600 dark:text-gray-400">
            Este painel é exclusivo para registro e acompanhamento de visitas do tipo <strong>LIRAa</strong>.
        </p>
        <br>
        <!-- Relógio -->
        <div class="flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500 dark:text-gray-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l4 2M12 4a8 8 0 100 16 8 8 0 000-16z" />
            </svg>
            <span id="clock" class="text-gray-700 dark:text-gray-300"></span>
        </div>
    </section>

    <!-- Estatísticas Pessoais -->
    <section class="space-y-4">
        <header>
            <h2 class="text-xl font-semibold mb-2 text-gray-800 dark:text-gray-200">Estatísticas</h2>
        </header>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Minhas visitas -->
            <div class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500 dark:text-blue-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-4.42 0-8 1.79-8 4v2h16v-2c0-2.21-3.58-4-8-4z" />
                    </svg>
                    <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200">Minhas Visitas LIRAa</h3>
                </div>
                <p class="mt-2 text-3xl text-gray-900 dark:text-gray-100">
                    {{ \App\Models\Visita::where('fk_usuario_id', Auth::user()->use_id)->where('vis_tipo', 'LIRAa')->count() }}
                </p>
            </div>

            <!-- Doenças Monitoradas -->
            <div class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-500 dark:text-red-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3v18h18M9 17v-6m4 6v-10m4 10v-4" />
                    </svg>
                    <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200">Doenças Monitoradas</h3>
                </div>
                <p class="mt-2 text-3xl text-gray-900 dark:text-gray-100">
                    {{ \App\Models\Doenca::count() }}
                </p>
            </div>
        </div>
    </section>

    <!-- Ações Rápidas -->
    <section class="mt-8 space-y-4">
        <header>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Ações Rápidas</h2>
        </header>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <a href="{{ route('saude.doencas.index') }}" class="flex items-center justify-center px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-100 rounded shadow-sm hover:shadow-lg transition dark:hover:bg-gray-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-gray-500 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                Doenças Monitoradas
            </a>
            <a href="{{ route('saude.visitas.create') }}" class="flex items-center justify-center px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-100 rounded shadow-sm hover:shadow-lg transition dark:hover:bg-gray-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-gray-500 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                Registrar Visita LIRAa
            </a>
            <a href="{{ route('saude.visitas.index') }}" class="flex items-center justify-center px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-100 rounded shadow-sm hover:shadow-lg transition dark:hover:bg-gray-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-gray-500 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                Minhas Visitas
            </a>
        </div>
    </section>

    <!-- Suporte Técnico -->
    <section class="mt-8">
        <header>
            <h2 class="text-xl font-semibold mb-2 text-gray-800 dark:text-gray-200">Ajuda e Suporte</h2>
        </header>
        <div class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow">
            <p class="mt-2 text-gray-600 dark:text-gray-400">Encontrou algum problema? Contate:</p>
            <ul class="mt-2 space-y-1 text-gray-600 dark:text-gray-400">
                <li><b>E-mail:</b> <a href="mailto:bernardo@bitwise.dev.br" class="underline text-gray-800 dark:text-gray-100 hover:text-black dark:hover:text-gray-200 transition-colors">bernardo@bitwise.dev.br</a></li>
                <li><b>WhatsApp:</b> <a href="https://wa.me/5554996605584" class="underline text-gray-800 dark:text-gray-100">+55 54 9 9660-5584</a></li>
            </ul>
        </div>
    </section>
</div>

{{-- Scripts --}}
<script>
    function updateClock() {
        const now = new Date();
        const options = {
            weekday: 'long', year: 'numeric', month: 'long',
            day: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit'
        };
        document.getElementById('clock').innerText = now.toLocaleDateString('pt-BR', options);
    }
    setInterval(updateClock, 1000);
    updateClock();
</script>
@endsection