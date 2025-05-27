@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6 max-w-4xl space-y-6">

    <!-- Botão Voltar -->
    <div>
        <a href="{{ route('gestor.users.index') }}" 
           class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-semibold text-sm rounded-lg shadow transition">
            <svg class="h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M15 19l-7-7 7-7"/>
            </svg>
            Voltar
        </a>
    </div>

    <!-- Card introdutório -->
    <section class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Editar Usuário</h2>
        <p class="mt-2 text-gray-600 dark:text-gray-400">
            Atualize as informações do usuário conforme necessário. 
            Se não desejar alterar a senha, deixe o campo de senha em branco.
        </p>
    </section>

    <!-- Formulário de Edição -->
    <section class="p-6 bg-white dark:bg-gray-700 rounded-lg shadow space-y-6">
        @if(session('status'))
            <x-alert type="success" :message="session('status')" />
        @endif

        <form method="POST" action="{{ route('gestor.users.update', $user) }}" class="space-y-6">
            @csrf
            @method('PATCH')

            <!-- Nome -->
            <div>
                <label for="use_nome" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nome <span class="text-red-500">*</span></label>
                <input type="text" id="use_nome" name="use_nome" value="{{ old('use_nome', $user->use_nome) }}" 
                       required autofocus
                       class="mt-1 block w-full rounded-md bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm">
            </div>

            <!-- CPF -->
            <div>
                <label for="" class="block text-sm font-medium text-gray-700 dark:text-gray-300">CPF <span class="text-red-500">*</span></label>
                <input type="text" id="" name="" value="{{ old('use_cpf', preg_replace('/\d(?=(?:.*\d){2})/', '*', $user->use_cpf)) }}" 
                       readonly
                       class="mt-1 block w-full rounded-md bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm">
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Não é possível alterar o CPF. Caso precise, entre em contato com o suporte.</p>
            </div>

            <!-- Email -->
            <div>
                <label for="use_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">E-mail <span class="text-red-500">*</span></label>
                <input type="email" id="use_email" name="use_email" value="{{ old('use_email', $user->use_email) }}"
                       required
                       class="mt-1 block w-full rounded-md bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm">
            </div>

            <!-- Perfil -->
            <div>
                <label for="use_perfil" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Perfil <span class="text-red-500">*</span></label>
                <select id="use_perfil" name="use_perfil"
                        class="mt-1 block w-full rounded-md bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm" required>
                    <option value="gestor" {{ $user->use_perfil == 'gestor' ? 'selected' : '' }}>Gestor</option>
                    <option value="agente_endemias" {{ $user->use_perfil == 'agente' ? 'selected' : '' }}>Agente de Endemias</option>
                    <option value="agente_saude" {{ $user->use_perfil == 'tecnico' ? 'selected' : '' }}>Técnico de Saúde</option>
                </select>
            </div>

            <!-- Data Cadastro -->
            <div>
                <label for="" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Data de Cadastro <span class="text-red-500">*</span></label>
                <input type="text" id="" name="" value="{{ $user->use_data_criacao->format('d/m/Y') }}" 
                       readonly
                       class="mt-1 block w-full rounded-md bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm">
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Data em que o usuário foi cadastrado no sistema.</p>
            </div>

            <!-- Nova Senha -->
            <div>
                <label for="use_senha" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nova Senha</label>
                <input type="password" id="use_senha" name="use_senha" autocomplete="new-password"
                       class="mt-1 block w-full rounded-md bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm">
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Deixe em branco se não quiser alterar a senha atual.</p>
            </div>

            <!-- Botão Salvar -->
            <div class="flex justify-end">
                <button type="submit" 
                        class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold text-sm rounded-lg shadow-md transition">
                    Salvar Alterações
                </button>
            </div>
        </form>
    </section>

</div>
@endsection
