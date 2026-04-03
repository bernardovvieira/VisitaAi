@extends('layouts.app')

@section('content')
<div class="v-page space-y-5">
    <x-page-header :eyebrow="__('Conta e segurança')" :title="__('Meu perfil')" />

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
    <section class="v-card space-y-2 dark:bg-gray-800">
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
            <div class="v-card space-y-4 dark:bg-gray-800">
                <h3 class="flex items-center text-lg font-semibold text-gray-800 dark:text-gray-200">
                    <x-heroicon-o-user class="mr-2 mt-px h-5 w-5 shrink-0 text-blue-600 dark:text-blue-400" />
                    Informações pessoais
                </h3>
                <dl class="text-gray-700 dark:text-gray-300 space-y-2">
                    <div class="flex justify-between"><dt class="font-medium">ID</dt><dd>{{ Auth::id() }}</dd></div>
                    <div class="flex justify-between"><dt class="font-medium">CPF</dt><dd>{{ preg_replace('/\d(?=(?:.*\d){2})/', '*', Auth::user()->use_cpf) }}</dd></div>
                    <div class="flex justify-between"><dt class="font-medium">Nome</dt><dd>{{ Auth::user()->use_nome }}</dd></div>
                    <div class="flex justify-between"><dt class="font-medium">E-mail</dt><dd><a href="mailto:{{ Auth::user()->use_email }}" class="text-gray-600 dark:text-gray-400 hover:underline">{{ Auth::user()->use_email }}</a></dd></div>
                </dl>
            </div>

            {{-- Card: Acesso e status --}}
            <div class="v-card space-y-4 dark:bg-gray-800">
                <h3 class="flex items-center text-lg font-semibold text-gray-800 dark:text-gray-200">
                    <x-heroicon-o-shield-check class="mr-2 mt-px h-5 w-5 shrink-0 text-blue-600 dark:text-blue-400" />
                    Acesso e status
                </h3>
                <dl class="text-gray-700 dark:text-gray-300 space-y-2">
                    <div class="flex justify-between"><dt class="font-medium">Perfil</dt><dd>{{ \App\Models\User::perfilLabel(Auth::user()->use_perfil) }}</dd></div>
                    <div class="flex justify-between"><dt class="font-medium">Registrado em</dt><dd>{{ Auth::user()->use_data_criacao->format('d/m/Y') }}</dd></div>
                    <div class="flex justify-between"><dt class="font-medium">Status</dt><dd>@if (Auth::user()->use_aprovado) <span class="font-semibold text-emerald-600 dark:text-emerald-400">Ativo</span> @else <span class="font-semibold text-amber-600 dark:text-amber-400">Pendente</span> @endif</dd></div>
                </dl>
            </div>
        </div>

        {{-- Coluna direita: Atualizar Dados --}}
        <div class="v-card space-y-6 dark:bg-gray-800">
            <h3 class="flex items-center text-lg font-semibold text-gray-800 dark:text-gray-200">
                <x-heroicon-o-pencil-square class="mr-2 h-5 w-5 shrink-0 text-blue-600 dark:text-blue-400" />
                Atualizar Dados
            </h3>
            <form method="POST" action="{{ route('profile.update') }}" class="space-y-4" id="profile-update-form">
                @csrf
                @method('patch')

                <div>
                    <label for="name" class="v-toolbar-label">Nome <span class="text-red-500">*</span></label>
                    <input id="name" name="name" type="text"
                        value="{{ old('name', Auth::user()->use_nome) }}"
                        class="v-input mt-1"
                        required>
                    @error('name')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="email" class="v-toolbar-label">E-mail <span class="text-red-500">*</span></label>
                    <input id="email" name="email" type="email" autocapitalize="off"
                        value="{{ old('email', Auth::user()->use_email) }}"
                        class="v-input mt-1"
                        required>
                    @error('email')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="tema" class="v-toolbar-label">Preferência de tema <span class="text-red-500">*</span></label>
                    <select id="tema" name="tema" class="v-select mt-1">
                        <option value="light" {{ old('tema', Auth::user()->use_tema ?? 'light') === 'light' ? 'selected' : '' }}>Modo claro</option>
                        <option value="dark" {{ old('tema', Auth::user()->use_tema ?? 'light') === 'dark' ? 'selected' : '' }}>Modo escuro</option>
                    </select>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Define o tema ao fazer login. Ao criar a conta, o padrão é modo claro.</p>
                    @error('tema')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="flex justify-end">
                    <x-primary-button type="submit" id="profile-update-btn">Aplicar Alterações</x-primary-button>
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
    <div class="v-card space-y-4 dark:bg-gray-800">
        <h3 class="flex items-center text-lg font-semibold text-gray-800 dark:text-gray-200">
            <x-heroicon-o-lock-closed class="mr-2 h-5 w-5 shrink-0 text-amber-500" />
            Autenticação em dois fatores (2FA)
        </h3>
        <p class="text-gray-600 dark:text-gray-400">
            A 2FA exige um código do seu celular (app como Google Authenticator ou similar) além da senha ao entrar, aumentando a segurança da conta.
        </p>
        @if(Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::twoFactorAuthentication()))
            @php $user = Auth::user(); @endphp
            @if(method_exists($user, 'hasEnabledTwoFactorAuthentication') && $user->hasEnabledTwoFactorAuthentication())
                <p class="text-sm font-medium text-emerald-600 dark:text-emerald-400">2FA ativado para esta conta.</p>
                <a href="{{ route('password.confirm') }}?return_action=disable_2fa"
                   onclick="return confirm('Tem certeza que deseja desativar a autenticação em dois fatores?');"
                   class="inline-flex items-center px-4 py-2 text-sm font-semibold text-white bg-red-600 hover:bg-red-700 dark:bg-red-600 dark:hover:bg-red-700 rounded-lg shadow transition">
                    Desativar autenticação em dois fatores
                </a>
            @else
                <a href="{{ route('profile.two-factor') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-gray-600 hover:bg-gray-700 dark:bg-gray-600 dark:hover:bg-gray-500 rounded-lg shadow transition">
                   <x-heroicon-o-chevron-right class="h-4 w-4 shrink-0" />
                    Ativar autenticação em dois fatores 
                </a>
            @endif
        @else
            <p class="text-sm text-gray-500 dark:text-gray-400">2FA não está disponível neste ambiente.</p>
        @endif
    </div>

    <!-- Texto de Informação -->
    <div class="v-card v-card--muted text-sm text-slate-900 dark:text-slate-100" role="alert">
        <h4 class="text-base font-semibold mb-2">Informações Importantes</h4>
        <ul class="list-disc list-inside space-y-2">
            <li>Algumas informações são gerenciadas pelo sistema e não podem ser alteradas diretamente. Se necessário, entre em contato com a Bitwise Technologies (suporte).</li>
            <li>Para alterar sua senha, utilize a opção <strong>"Esqueci minha senha"</strong> na tela de login.</li>
            <li>A gestão de permissões de acesso é realizada apenas por <strong>gestores</strong> no menu "Usuários".</li>
        </ul>
    </div>

    {{-- Card: Anonimizar Conta --}}
    <div class="v-card space-y-4 dark:bg-gray-800">
        <h3 class="flex items-center text-lg font-semibold text-gray-800 dark:text-gray-200">
            <x-heroicon-o-shield-exclamation class="mr-2 h-5 w-5 shrink-0 text-red-500" />
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
