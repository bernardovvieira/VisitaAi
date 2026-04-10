@extends('layouts.app')

@section('og_title', config('app.name') . ' · ' . __('Meu perfil'))
@section('og_description', __('Atualize nome, e-mail e configurações da sua conta.'))

@section('content')
<div class="v-page space-y-5">
    <x-page-header :eyebrow="__('Conta e segurança')" :title="__('Meu perfil')" />

    <x-flash-alerts />

    <!-- Mensagem de Contexto -->
    <x-section-card class="space-y-2 dark:bg-gray-800">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">{{ __('Olá, :name!', ['name' => Auth::user()->use_nome]) }}</h2>
        <p class="text-gray-600 dark:text-gray-400">
            {{ __('Aqui você pode atualizar seus dados pessoais, como nome e e-mail. Algumas informações, como CPF e perfil, são gerenciadas pelo sistema e não podem ser alteradas diretamente. Se precisar de ajuda, entre em contato com a Bitwise Technologies (suporte técnico).') }}
        </p>
    </x-section-card>

    <!-- Grid: coluna esquerda = 2 cards empilhados; coluna direita = Atualizar Dados -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Coluna esquerda: Informações pessoais + Acesso e status --}}
        <div class="space-y-6">
            {{-- Card: Informações pessoais --}}
            <x-section-card class="space-y-4 dark:bg-gray-800">
                <h3 class="flex items-center text-lg font-semibold text-gray-800 dark:text-gray-200">
                    <x-heroicon-o-user class="mr-2 mt-px h-5 w-5 shrink-0 text-blue-600 dark:text-blue-400" />
                    {{ __('Informações pessoais') }}
                </h3>
                <dl class="space-y-2 text-gray-700 dark:text-gray-300">
                    <div class="flex justify-between gap-4"><dt class="font-medium text-slate-600 dark:text-slate-300">ID</dt><dd class="text-right">{{ Auth::id() }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="font-medium text-slate-600 dark:text-slate-300">{{ __('CPF') }}</dt><dd class="text-right">{{ preg_replace('/\d(?=(?:.*\d){2})/', '*', Auth::user()->use_cpf) }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="font-medium text-slate-600 dark:text-slate-300">{{ __('Nome') }}</dt><dd class="text-right">{{ Auth::user()->use_nome }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="font-medium text-slate-600 dark:text-slate-300">{{ __('E-mail') }}</dt><dd class="text-right"><a href="mailto:{{ Auth::user()->use_email }}" class="text-gray-600 hover:underline dark:text-gray-400">{{ Auth::user()->use_email }}</a></dd></div>
                </dl>
            </x-section-card>

            {{-- Card: Acesso e status --}}
            <x-section-card class="space-y-4 dark:bg-gray-800">
                <h3 class="flex items-center text-lg font-semibold text-gray-800 dark:text-gray-200">
                    <x-heroicon-o-shield-check class="mr-2 mt-px h-5 w-5 shrink-0 text-blue-600 dark:text-blue-400" />
                    {{ __('Acesso e status') }}
                </h3>
                <dl class="space-y-2 text-gray-700 dark:text-gray-300">
                    <div class="flex justify-between gap-4"><dt class="font-medium text-slate-600 dark:text-slate-300">{{ __('Perfil') }}</dt><dd class="text-right">{{ \App\Models\User::perfilLabel(Auth::user()->use_perfil) }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="font-medium text-slate-600 dark:text-slate-300">{{ __('Registrado em') }}</dt><dd class="text-right">{{ Auth::user()->use_data_criacao->format('d/m/Y') }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="font-medium text-slate-600 dark:text-slate-300">{{ __('Status') }}</dt><dd class="text-right">@if (Auth::user()->use_aprovado) <span class="font-semibold text-emerald-600 dark:text-emerald-400">{{ __('Ativo') }}</span> @else <span class="font-semibold text-amber-600 dark:text-amber-400">{{ __('Pendente') }}</span> @endif</dd></div>
                </dl>
            </x-section-card>
        </div>

        {{-- Coluna direita: Atualizar Dados --}}
        <x-section-card class="space-y-6 dark:bg-gray-800">
            <h3 class="flex items-center text-lg font-semibold text-gray-800 dark:text-gray-200">
                <x-heroicon-o-pencil-square class="mr-2 h-5 w-5 shrink-0 text-blue-600 dark:text-blue-400" />
                {{ __('Atualizar dados') }}
            </h3>
            <form method="POST" action="{{ route('profile.update') }}" class="space-y-4" id="profile-update-form">
                @csrf
                @method('patch')

                <div>
                    <label for="name" class="v-toolbar-label">{{ __('Nome') }} <span class="text-red-500">*</span></label>
                    <input id="name" name="name" type="text"
                        value="{{ old('name', Auth::user()->use_nome) }}"
                        class="v-input mt-1"
                        required>
                    @error('name')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="email" class="v-toolbar-label">{{ __('E-mail') }} <span class="text-red-500">*</span></label>
                    <input id="email" name="email" type="email" autocapitalize="off"
                        value="{{ old('email', Auth::user()->use_email) }}"
                        class="v-input mt-1"
                        required>
                    @error('email')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="tema" class="v-toolbar-label">{{ __('Preferência de tema') }} <span class="text-red-500">*</span></label>
                    <select id="tema" name="tema" class="v-select mt-1">
                        <option value="light" {{ old('tema', Auth::user()->use_tema ?? 'light') === 'light' ? 'selected' : '' }}>{{ __('Modo claro') }}</option>
                        <option value="dark" {{ old('tema', Auth::user()->use_tema ?? 'light') === 'dark' ? 'selected' : '' }}>{{ __('Modo escuro') }}</option>
                    </select>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Define o tema ao fazer login. Ao criar a conta, o padrão é modo claro.') }}</p>
                    @error('tema')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="flex justify-end">
                    <x-primary-button type="submit" id="profile-update-btn" class="!px-3 !py-1.5 !text-xs">{{ __('Aplicar alterações') }}</x-primary-button>
                </div>
            </form>
            <script>
            (function(){
                var form = document.getElementById('profile-update-form');
                var btn = document.getElementById('profile-update-btn');
                if (form && btn) form.addEventListener('submit', function(){
                    btn.disabled = true;
                    while (btn.firstChild) btn.removeChild(btn.firstChild);
                    var sp = document.createElement('span');
                    sp.className = 'inline-flex items-center gap-2';
                    var svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
                    svg.setAttribute('class', 'animate-spin h-4 w-4');
                    svg.setAttribute('fill', 'none');
                    svg.setAttribute('viewBox', '0 0 24 24');
                    var c = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
                    c.setAttribute('class', 'opacity-25');
                    c.setAttribute('cx', '12'); c.setAttribute('cy', '12'); c.setAttribute('r', '10');
                    c.setAttribute('stroke', 'currentColor'); c.setAttribute('stroke-width', '4');
                    svg.appendChild(c);
                    var p = document.createElementNS('http://www.w3.org/2000/svg', 'path');
                    p.setAttribute('class', 'opacity-75');
                    p.setAttribute('fill', 'currentColor');
                    p.setAttribute('d', 'M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z');
                    svg.appendChild(p);
                    sp.appendChild(svg);
                    sp.appendChild(document.createTextNode(@json(__('Salvando…'))));
                    btn.appendChild(sp);
                });
            })();
            </script>
        </x-section-card>
    </div>

    {{-- Card: Autenticação em dois fatores (2FA) --}}
    <x-section-card class="space-y-4 dark:bg-gray-800">
        <h3 class="flex items-center text-lg font-semibold text-gray-800 dark:text-gray-200">
            <x-heroicon-o-lock-closed class="mr-2 h-5 w-5 shrink-0 text-amber-500" />
            {{ __('Autenticação em dois fatores (2FA)') }}
        </h3>
        <p class="text-gray-600 dark:text-gray-400">
            {{ __('A 2FA exige um código do seu celular (app como Google Authenticator ou similar) além da senha ao entrar, aumentando a segurança da conta.') }}
        </p>
        @if(Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::twoFactorAuthentication()))
            @php $user = Auth::user(); @endphp
            @if(method_exists($user, 'hasEnabledTwoFactorAuthentication') && $user->hasEnabledTwoFactorAuthentication())
                <p class="text-sm font-medium text-emerald-600 dark:text-emerald-400">{{ __('2FA ativado para esta conta.') }}</p>
                <a href="{{ route('password.confirm') }}?return_action=disable_2fa"
                   onclick="return confirm(@json(__('Tem certeza que deseja desativar a autenticação em dois fatores?')));"
                   class="v-btn-danger !px-3 !py-1.5 !text-xs">
                    {{ __('Desativar autenticação em dois fatores') }}
                </a>
            @else
                <a href="{{ route('profile.two-factor') }}"
                   class="v-btn-slate gap-1.5 !px-3 !py-1.5 !text-xs">
                   <x-heroicon-o-chevron-right class="h-3.5 w-3.5 shrink-0" />
                    {{ __('Ativar autenticação em dois fatores (2FA)') }}
                </a>
            @endif
        @else
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('2FA não está disponível neste ambiente.') }}</p>
        @endif
    </x-section-card>

    <!-- Texto de Informação -->
    <x-section-card class="v-card--muted text-sm text-slate-900 dark:text-slate-100" role="alert">
        <h4 class="mb-2 text-base font-semibold">{{ __('Informações importantes') }}</h4>
        <ul class="list-inside list-disc space-y-2">
            <li>{{ __('Algumas informações são gerenciadas pelo sistema e não podem ser alteradas diretamente. Se necessário, entre em contato com a Bitwise Technologies (suporte).') }}</li>
            <li>{{ __('Para alterar sua senha, utilize a opção “Esqueci minha senha” na tela de login.') }}</li>
            <li>{!! __('A gestão de permissões de acesso é realizada apenas por :g no menu :u.', ['g' => '<strong>'.e(__('Gestores')).'</strong>', 'u' => '“'.e(__('Usuários')).'”']) !!}</li>
        </ul>
    </x-section-card>

    {{-- Card: Anonimizar Conta --}}
    <x-section-card class="space-y-4 dark:bg-gray-800">
        <h3 class="flex items-center text-lg font-semibold text-gray-800 dark:text-gray-200">
            <x-heroicon-o-shield-exclamation class="mr-2 h-5 w-5 shrink-0 text-red-500" />
            {{ __('Anonimizar conta') }}
        </h3>
        <p class="text-gray-600 dark:text-gray-400">
            {{ __('A anonimização da conta remove permanentemente seus dados pessoais, como nome e e-mail, tornando-os irreversíveis. Você não poderá mais acessar sua conta após essa ação.') }}
        </p>
        <div class="flex justify-end">
            @if(auth()->user()->isAgente())
                <p class="mr-2 text-sm text-red-600">{{ __('Apenas gestores podem realizar essa ação.') }}</p>
            @elseif(auth()->user()->isGestor())
                <a href="{{ route('gestor.users.index') }}"
                   class="v-btn-danger !px-3 !py-1.5 !text-xs">
                    {{ __('Ir para usuários') }}
                </a>
            @endif
        </div>
    </x-section-card>
</div>
@endsection
