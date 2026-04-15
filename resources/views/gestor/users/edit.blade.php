@extends('layouts.app')

@section('og_title', config('app.brand') . ' · ' . __('Editar usuário'))
@section('og_description', __('Atualização de dados e permissões do usuário municipal.'))

@section('content')
<div class="v-page">
    <x-breadcrumbs :items="[['label' => __('Página Inicial'), 'url' => route('dashboard')], ['label' => __('Usuários'), 'url' => route('gestor.users.index')], ['label' => __('Editar')]]" />
    <x-page-header :eyebrow="__('Gestão municipal')" :title="__('Editar usuário')" />

    <!-- Card introdutório -->
    <x-section-card>
        <h2 class="v-section-title">{{ __('Dados da conta') }}</h2>
        <p class="mt-2 text-gray-600 dark:text-gray-400">
            {{ __('Atualize as informações do usuário conforme necessário. Se não desejar alterar a senha, deixe o campo de senha em branco.') }}
        </p>
    </x-section-card>

    <!-- Formulário de Edição -->
    <x-section-card class="space-y-4">
        <x-flash-alerts />

        @if ($errors->any())
            @php
                $fieldLabels = [
                    'use_nome' => __('Nome'),
                    'use_email' => __('E-mail'),
                    'use_perfil' => __('Perfil'),
                    'use_senha' => __('Nova senha'),
                    'use_senha_confirmation' => __('Confirmar nova senha'),
                ];
                $errorFields = array_unique($errors->keys());
                $labels = array_map(fn ($k) => $fieldLabels[$k] ?? $k, $errorFields);
            @endphp
            <x-alert type="error" :title="__('Corrija os erros nos campos indicados abaixo.')" :message="count($labels) > 0 ? __('Campos com erro: :labels.', ['labels' => implode(', ', $labels)]) : null" />
        @endif

        <form method="POST" action="{{ route('gestor.users.update', $user) }}" class="space-y-6" id="user-edit-form">
            @csrf
            @method('PATCH')

            <x-form-field name="use_nome" :label="__('Nome')" :required="true">
                <x-text-input id="use_nome" name="use_nome" type="text" value="{{ old('use_nome', $user->use_nome) }}" required autofocus />
            </x-form-field>

            <x-form-field name="cpf_mascarado" :label="__('CPF')" :required="true" :help="__('Não é possível alterar o CPF. Caso precise, entre em contato com a Bitwise Technologies (suporte).')">
                <x-text-input id="cpf_mascarado" name="cpf_mascarado" type="text" value="{{ old('cpf_mascarado', preg_replace('/\d(?=(?:.*\d){2})/', '*', $user->use_cpf)) }}" readonly />
            </x-form-field>

            <x-form-field name="use_email" :label="__('E-mail')" :required="true">
                <x-text-input id="use_email" name="use_email" type="email" value="{{ old('use_email', $user->use_email) }}" required />
            </x-form-field>

            <x-form-field name="use_perfil" :label="__('Perfil')" :required="true">
                <select id="use_perfil" name="use_perfil" class="v-select mt-1" required>
                    <option value="gestor" {{ old('use_perfil', $user->use_perfil) == 'gestor' ? 'selected' : '' }}>{{ \App\Models\User::perfilLabel('gestor') }}</option>
                    <option value="agente_endemias" {{ old('use_perfil', $user->use_perfil) == 'agente_endemias' ? 'selected' : '' }}>{{ \App\Models\User::perfilLabel('agente_endemias') }}</option>
                    <option value="agente_saude" {{ old('use_perfil', $user->use_perfil) == 'agente_saude' ? 'selected' : '' }}>{{ \App\Models\User::perfilLabel('agente_saude') }}</option>
                </select>
            </x-form-field>

            <x-form-field name="data_cadastro_display" :label="__('Data de Cadastro')" :help="__('Data em que o usuário se cadastrou.')">
                <x-text-input id="data_cadastro_display" type="text" value="{{ $user->use_data_criacao->format('d/m/Y') }}" readonly class="cursor-default border-slate-200/80 bg-slate-100 focus:ring-0 dark:border-slate-600 dark:bg-slate-900/80" />
            </x-form-field>

            <x-form-field name="use_senha" :label="__('Nova Senha')" :help="__('Mínimo 8 caracteres, com letras, números e pelo menos um caractere especial (ex.: @, #, $, !). Deixe em branco se não quiser alterar a senha atual.')">
                <x-text-input id="use_senha" name="use_senha" type="password" autocomplete="new-password" />
                <div class="mt-2 h-1.5 w-full overflow-hidden rounded-full bg-gray-200 dark:bg-gray-600" role="presentation" aria-hidden="true">
                    <div id="password-strength-bar" class="h-full rounded-full bg-red-500 transition-all duration-300 ease-out" style="width: 0%"></div>
                </div>
            </x-form-field>

            <x-form-field name="use_senha_confirmation" :label="__('Confirmar Nova Senha')">
                <x-text-input id="use_senha_confirmation" name="use_senha_confirmation" type="password" autocomplete="new-password" />
                <p id="password-match-feedback" class="mt-1 hidden text-sm" aria-live="polite"></p>
            </x-form-field>

            <div class="flex justify-end">
                <x-primary-button type="submit" id="user-edit-btn">{{ __('Salvar alterações') }}</x-primary-button>
            </div>
        </form>
    </x-section-card>

</div>
<script>
(function(){
    var form = document.getElementById('user-edit-form');
    var btn = document.getElementById('user-edit-btn');
    if (form && btn) form.addEventListener('submit', function(){ btn.disabled = true; btn.innerHTML = '<svg class="animate-spin -ml-1 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Salvando…'; });

    // Barra de força da senha (igual ao registro)
    var bar = document.getElementById('password-strength-bar');
    var pwd = document.getElementById('use_senha');
    if (bar && pwd) {
        function updatePasswordStrength() {
            var val = (pwd.value || '');
            var minLen = val.length >= 8;
            var hasLetter = /[a-zA-Z]/.test(val);
            var hasMixed = /[a-z]/.test(val) && /[A-Z]/.test(val);
            var hasNumber = /\d/.test(val);
            var hasSymbol = /[^a-zA-Z0-9]/.test(val);
            var n = [minLen, hasLetter, hasMixed, hasNumber, hasSymbol].filter(Boolean).length;
            bar.style.width = (n * 20) + '%';
            bar.classList.remove('bg-red-500', 'bg-amber-500', 'bg-blue-500');
            bar.classList.add(n >= 5 ? 'bg-blue-500' : n >= 3 ? 'bg-amber-500' : 'bg-red-500');
        }
        pwd.addEventListener('input', updatePasswordStrength);
        pwd.addEventListener('change', updatePasswordStrength);
    }

    // Confirmar senha: exibir se as duas conferem ou não
    var pwdConf = document.getElementById('use_senha_confirmation');
    var matchFeedback = document.getElementById('password-match-feedback');
    if (pwdConf && matchFeedback) {
        function updateMatchFeedback() {
            var p = (pwd && pwd.value) ? pwd.value : '';
            var c = (pwdConf.value || '');
            matchFeedback.classList.add('hidden');
            if (c.length === 0) return;
            if (p === c) {
                matchFeedback.textContent = 'Senhas conferem.';
                matchFeedback.classList.remove('text-red-600', 'dark:text-red-400');
                matchFeedback.classList.add('text-blue-600', 'dark:text-blue-400');
                matchFeedback.classList.remove('hidden');
            } else {
                matchFeedback.textContent = 'As senhas não conferem.';
                matchFeedback.classList.remove('text-blue-600', 'dark:text-blue-400');
                matchFeedback.classList.add('text-red-600', 'dark:text-red-400');
                matchFeedback.classList.remove('hidden');
            }
        }
        if (pwd) pwd.addEventListener('input', updateMatchFeedback);
        pwdConf.addEventListener('input', updateMatchFeedback);
    }
})();
</script>
@endsection
