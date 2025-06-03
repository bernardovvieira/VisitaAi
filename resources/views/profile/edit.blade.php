@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6 space-y-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Meu Perfil</h1>

    @if(session('success'))
        <x-alert type="success" :message="session('success')" />
    @endif
    @if(session('error'))
        <x-alert type="error" :message="session('error')" />
    @endif

    <!-- Mensagem de Contexto -->
    <section class="p-6 bg-white dark:bg-gray-700 rounded-lg shadow space-y-2">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Olá, {{ Auth::user()->use_nome }}!</h2>
        <p class="text-gray-600 dark:text-gray-400">
            Aqui você pode atualizar seus dados pessoais, como nome e e-mail. Algumas informações, como CPF e perfil, são gerenciadas pelo sistema e não podem ser alteradas diretamente. Se precisar de ajuda, entre em contato com o suporte técnico.
        </p>
    </section>

    <!-- Grid de Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Card: Suas Informações --}}
        <div class="p-6 bg-white dark:bg-gray-700 rounded-lg shadow space-y-4">
            <h3 class="flex items-center text-lg font-semibold text-gray-800 dark:text-gray-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-500 mr-2 mt-[1px]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v1h16v-1c0-2.66-5.33-4-8-4z" />
            </svg>
                Suas Informações
            </h3>
            <dl class="text-gray-700 dark:text-gray-300 space-y-2">
                <div class="flex justify-between"><dt class="font-medium">ID</dt><dd>{{ Auth::id() }}</dd></div>
                <div class="flex justify-between"><dt class="font-medium">CPF</dt><dd>{{ preg_replace('/\d(?=(?:.*\d){2})/', '*', Auth::user()->use_cpf) }}</dd></div>
                <div class="flex justify-between"><dt class="font-medium">Nome</dt><dd>{{ Auth::user()->use_nome }}</dd></div>
                <div class="flex justify-between"><dt class="font-medium">E‑mail</dt><dd><a href="mailto:{{ Auth::user()->use_email }}" class="text-gary-600 dark:text-gray-400 hover:underline">{{ Auth::user()->use_email }}</a></dd></div>
                <div class="flex justify-between"><dt class="font-medium">Perfil</dt><dd>{{ Auth::user()->use_perfil == 'agente_endemias' ? 'Agente de Endemias' : (Auth::user()->use_perfil == 'agente_saude' ? 'Agente de Saúde' : 'Gestor Municipal') }}</dd></div>
                <div class="flex justify-between"><dt class="font-medium">Registrado em</dt><dd>{{ Auth::user()->use_data_criacao->format('d/m/Y') }}</dd></div>
                <div class="flex justify-between"><dt class="font-medium">Status</dt><dd>@if (Auth::user()->use_aprovado) <span class="text-green-600 dark:text-green-400 font-semibold">Ativo</span> @else <span class="text-yellow-600 dark:text-yellow-400 font-semibold">Pendente</span> @endif</dd></div>
            </dl>
        </div>

        {{-- Card: Atualizar Dados --}}
        <div class="p-6 bg-white dark:bg-gray-700 rounded-lg shadow space-y-6">
            <h3 class="flex items-center text-lg font-semibold text-gray-800 dark:text-gray-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5M18.5 2.5l3 3L12 15l-4 1 1-4 9.5-9.5z" />
                </svg>
                Atualizar Dados
            </h3>
            <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
                @csrf
                @method('patch')

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nome <span class="text-red-500">*</span></label>
                    <input id="name" name="name" type="text"
                        value="{{ old('name', Auth::user()->use_nome) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-green-500 focus:border-green-500"
                        required>
                    @error('name')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">E‑mail <span class="text-red-500">*</span></label>
                    <input id="email" name="email" type="email" autocapitalize="off"
                        value="{{ old('email', Auth::user()->use_email) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-green-500 focus:border-green-500"
                        required>
                    @error('email')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 transition">
                        Aplicar Alterações
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Texto de Informação -->
    <div class="p-6 mb-6 text-sm bg-blue-100 text-blue-800 rounded-lg dark:bg-blue-200 dark:text-blue-900" role="alert">
        <h4 class="text-base font-semibold mb-2">Informações Importantes</h4>
        <ul class="list-disc list-inside space-y-2">
            <li>Algumas informações são gerenciadas pelo sistema e não podem ser alteradas diretamente. Se necessário, entre em contato com o suporte técnico.</li>
            <li>Para alterar sua senha, utilize a opção <strong>"Esqueci minha senha"</strong> na tela de login.</li>
            <li>A gestão de permissões de acesso é realizada apenas por <strong>gestores</strong> no menu "Usuários".</li>
        </ul>
    </div>

    {{-- Card: Anonimizar Conta --}}
    <div class="p-6 bg-white dark:bg-gray-700 rounded-lg shadow space-y-4">
        <h3 class="flex items-center text-lg font-semibold text-gray-800 dark:text-gray-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
            </svg>
            Anonimizar Conta
        </h3>
        <p class="text-gray-600 dark:text-gray-400">
            A anonimização da conta remove permanentemente seus dados pessoais, como nome e e-mail, tornando-os irreversíveis. Você não poderá mais acessar sua conta após essa ação.
        </p>
        <div class="flex justify-end">
            @if(auth()->user()->isAgente())
                <!-- Aviso para agentes -->
                <p class="text-sm text-red-600 mr-2">Apenas gestores podem realizar essa ação.</p>
            @elseif(auth()->user()->isGestor())
                <!-- Link para gestores -->
                <a href="{{ route('gestor.users.index') }}"
                class="text-sm text-red-600 hover:underline font-semibold mr-2">
                    Acessar a página de usuários
                </a>
            @endif
        </div>
    </div>
</div>
@endsection
