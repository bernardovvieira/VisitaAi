@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Meu Perfil</h1>

    @if(session('success'))
        <x-alert type="success" :message="session('success')" />
    @endif
    @if(session('status') === 'two-factor-authentication-enabled')
        <x-alert type="success" message="Autenticação em dois fatores (2FA) ativada com sucesso. Na próxima sessão você precisará informar o código do aplicativo autenticador." />
    @elseif(session('status'))
        <x-alert type="success" :message="session('status')" />
    @endif
    @if(session('error'))
        <x-alert type="error" :message="session('error')" />
    @endif

    <!-- Mensagem de Contexto -->
    <section class="rounded-xl border border-gray-200/80 bg-white p-6 shadow-sm dark:border-gray-600 dark:bg-gray-800 space-y-2">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Olá, {{ Auth::user()->use_nome }}!</h2>
        <p class="text-gray-600 dark:text-gray-400">
            Aqui você pode atualizar seus dados pessoais, como nome e e-mail. Algumas informações, como CPF e perfil, são gerenciadas pelo sistema e não podem ser alteradas diretamente. Se precisar de ajuda, entre em contato com a Bitwise Technologies (suporte técnico).
        </p>
    </section>

    <!-- Grid: coluna esquerda = 2 cards empilhados; coluna direita = Atualizar Dados -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Coluna esquerda: Informações pessoais + Acesso e status --}}
        <div class="space-y-6">
            {{-- Card: Informações pessoais --}}
            <div class="rounded-xl border border-gray-200/80 bg-white p-6 shadow-sm dark:border-gray-600 dark:bg-gray-800 space-y-4">
                <h3 class="flex items-center text-lg font-semibold text-gray-800 dark:text-gray-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600 dark:text-blue-400 mr-2 mt-[1px]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v1h16v-1c0-2.66-5.33-4-8-4z" />
                    </svg>
                    Informações pessoais
                </h3>
                <dl class="text-gray-700 dark:text-gray-300 space-y-2">
                    <div class="flex justify-between"><dt class="font-medium">ID</dt><dd>{{ Auth::id() }}</dd></div>
                    <div class="flex justify-between"><dt class="font-medium">CPF</dt><dd>{{ preg_replace('/\d(?=(?:.*\d){2})/', '*', Auth::user()->use_cpf) }}</dd></div>
                    <div class="flex justify-between"><dt class="font-medium">Nome</dt><dd>{{ Auth::user()->use_nome }}</dd></div>
                    <div class="flex justify-between"><dt class="font-medium">E‑mail</dt><dd><a href="mailto:{{ Auth::user()->use_email }}" class="text-gray-600 dark:text-gray-400 hover:underline">{{ Auth::user()->use_email }}</a></dd></div>
                </dl>
            </div>

            {{-- Card: Acesso e status --}}
            <div class="rounded-xl border border-gray-200/80 bg-white p-6 shadow-sm dark:border-gray-600 dark:bg-gray-800 space-y-4">
                <h3 class="flex items-center text-lg font-semibold text-gray-800 dark:text-gray-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600 dark:text-blue-400 mr-2 mt-[1px]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    Acesso e status
                </h3>
                <dl class="text-gray-700 dark:text-gray-300 space-y-2">
                    <div class="flex justify-between"><dt class="font-medium">Perfil</dt><dd>{{ \App\Models\User::perfilLabel(Auth::user()->use_perfil) }}</dd></div>
                    <div class="flex justify-between"><dt class="font-medium">Registrado em</dt><dd>{{ Auth::user()->use_data_criacao->format('d/m/Y') }}</dd></div>
                    <div class="flex justify-between"><dt class="font-medium">Status</dt><dd>@if (Auth::user()->use_aprovado) <span class="text-blue-600 dark:text-blue-400 font-semibold">Ativo</span> @else <span class="text-yellow-600 dark:text-yellow-400 font-semibold">Pendente</span> @endif</dd></div>
                </dl>
            </div>
        </div>

        {{-- Coluna direita: Atualizar Dados --}}
        <div class="rounded-xl border border-gray-200/80 bg-white p-6 shadow-sm dark:border-gray-600 dark:bg-gray-800 space-y-6">
            <h3 class="flex items-center text-lg font-semibold text-gray-800 dark:text-gray-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600 dark:text-blue-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5M18.5 2.5l3 3L12 15l-4 1 1-4 9.5-9.5z" />
                </svg>
                Atualizar Dados
            </h3>
            <form method="POST" action="{{ route('profile.update') }}" class="space-y-4" id="profile-update-form">
                @csrf
                @method('patch')

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nome <span class="text-red-500">*</span></label>
                    <input id="name" name="name" type="text"
                        value="{{ old('name', Auth::user()->use_nome) }}"
                        class="mt-1 block w-full rounded-lg border-gray-300 bg-white text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:focus:border-blue-600 dark:focus:ring-blue-600"
                        required>
                    @error('name')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">E‑mail <span class="text-red-500">*</span></label>
                    <input id="email" name="email" type="email" autocapitalize="off"
                        value="{{ old('email', Auth::user()->use_email) }}"
                        class="mt-1 block w-full rounded-lg border-gray-300 bg-white text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:focus:border-blue-600 dark:focus:ring-blue-600"
                        required>
                    @error('email')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="tema" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Preferência de tema <span class="text-red-500">*</span></label>
                    <select id="tema" name="tema" class="mt-1 block w-full rounded-lg border-gray-300 bg-white text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:focus:border-blue-600 dark:focus:ring-blue-600">
                        <option value="light" {{ old('tema', Auth::user()->use_tema ?? 'light') === 'light' ? 'selected' : '' }}>Modo claro</option>
                        <option value="dark" {{ old('tema', Auth::user()->use_tema ?? 'light') === 'dark' ? 'selected' : '' }}>Modo escuro</option>
                    </select>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Define o tema ao fazer login. Ao criar a conta, o padrão é modo claro.</p>
                    @error('tema')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="flex justify-end">
                    <button type="submit" id="profile-update-btn" class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Aplicar Alterações
                    </button>
                </div>
            </form>
            <script>
            (function(){
                var form = document.getElementById('profile-update-form');
                var btn = document.getElementById('profile-update-btn');
                if (form && btn) form.addEventListener('submit', function(){ btn.disabled = true; btn.innerHTML = '<svg class="animate-spin -ml-1 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Salvando…'; });
            })();
            </script>
        </div>
    </div>

    {{-- Card: Autenticação em dois fatores (2FA) --}}
    <div class="rounded-xl border border-gray-200/80 bg-white p-6 shadow-sm dark:border-gray-600 dark:bg-gray-800 space-y-4">
        <h3 class="flex items-center text-lg font-semibold text-gray-800 dark:text-gray-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-amber-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
            Autenticação em dois fatores (2FA)
        </h3>
        <p class="text-gray-600 dark:text-gray-400">
            A 2FA exige um código do seu celular (app como Google Authenticator ou similar) além da senha ao entrar, aumentando a segurança da conta.
        </p>
        @if(Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::twoFactorAuthentication()))
            @php $user = Auth::user(); @endphp
            @if(method_exists($user, 'hasEnabledTwoFactorAuthentication') && $user->hasEnabledTwoFactorAuthentication())
                <p class="text-sm font-medium text-blue-600 dark:text-blue-400">2FA ativado para esta conta.</p>
                <a href="{{ route('password.confirm') }}?return_action=disable_2fa"
                   onclick="return confirm('Tem certeza que deseja desativar a autenticação em dois fatores?');"
                   class="inline-flex items-center px-4 py-2 text-sm font-semibold text-white bg-red-600 hover:bg-red-700 dark:bg-red-600 dark:hover:bg-red-700 rounded-lg shadow transition">
                    Desativar autenticação em dois fatores
                </a>
            @else
                <a href="{{ route('profile.two-factor') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-gray-600 hover:bg-gray-700 dark:bg-gray-600 dark:hover:bg-gray-500 rounded-lg shadow transition">
                   <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    Ativar autenticação em dois fatores 
                </a>
            @endif
        @else
            <p class="text-sm text-gray-500 dark:text-gray-400">2FA não está disponível neste ambiente.</p>
        @endif
    </div>

    <!-- Texto de Informação -->
    <div class="mb-6 rounded-lg border border-slate-200 bg-slate-100 p-6 text-sm text-slate-900 dark:border-slate-600 dark:bg-slate-800/60 dark:text-slate-100" role="alert">
        <h4 class="text-base font-semibold mb-2">Informações Importantes</h4>
        <ul class="list-disc list-inside space-y-2">
            <li>Algumas informações são gerenciadas pelo sistema e não podem ser alteradas diretamente. Se necessário, entre em contato com a Bitwise Technologies (suporte).</li>
            <li>Para alterar sua senha, utilize a opção <strong>"Esqueci minha senha"</strong> na tela de login.</li>
            <li>A gestão de permissões de acesso é realizada apenas por <strong>gestores</strong> no menu "Usuários".</li>
        </ul>
    </div>

    {{-- Card: Anonimizar Conta --}}
    <div class="rounded-xl border border-gray-200/80 bg-white p-6 shadow-sm dark:border-gray-600 dark:bg-gray-800 space-y-4">
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
                <!-- Aviso para ACE/ACS -->
                <p class="text-sm text-red-600 mr-2">Apenas gestores podem realizar essa ação.</p>
            @elseif(auth()->user()->isGestor())
                <!-- Link para gestores -->
                <a href="{{ route('gestor.users.index') }}"
                   class="inline-flex items-center px-4 py-2 text-sm font-semibold text-white bg-red-600 hover:bg-red-700 dark:bg-red-600 dark:hover:bg-red-700 rounded-lg shadow transition">
                    Ir para Usuários
                </a>
            @endif
        </div>
    </div>
</div>
@endsection
