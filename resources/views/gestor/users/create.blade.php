@extends('layouts.app')

@section('og_title', config('app.name') . ' · ' . __('Novo usuário'))
@section('og_description', __('Cadastro de novo usuário para operação municipal.'))

@section('content')
<div class="v-page">
    <x-breadcrumbs :items="[['label' => __('Página Inicial'), 'url' => route('dashboard')], ['label' => __('Usuários'), 'url' => route('gestor.users.index')], ['label' => __('Novo')]]" />
    <x-page-header :eyebrow="__('Gestão de acesso')" :title="__('Cadastrar usuário')" />

    <x-section-card class="space-y-4">
        <x-flash-alerts />

        <form method="POST" action="{{ route('gestor.users.store') }}" class="space-y-6" id="user-create-form">
            @csrf

            <x-form-field name="use_nome" :label="__('Nome')" :required="true">
                <x-text-input id="use_nome" name="use_nome" type="text" value="{{ old('use_nome') }}" required autofocus />
            </x-form-field>

            <x-form-field name="use_cpf" :label="__('CPF')" :required="true">
                <x-text-input id="use_cpf" name="use_cpf" type="text" value="{{ old('use_cpf') }}" required />
            </x-form-field>

            <x-form-field name="use_email" :label="__('E-mail')" :required="true">
                <x-text-input id="use_email" name="use_email" type="email" value="{{ old('use_email') }}" required />
            </x-form-field>

            <x-form-field name="use_perfil" :label="__('Perfil')" :required="true">
                <select id="use_perfil" name="use_perfil" class="v-select mt-1" required>
                    <option value="gestor" @selected(old('use_perfil') === 'gestor')>{{ \App\Models\User::perfilLabel('gestor') }}</option>
                    <option value="agente_endemias" @selected(old('use_perfil') === 'agente_endemias')>{{ \App\Models\User::perfilLabel('agente_endemias') }}</option>
                    <option value="agente_saude" @selected(old('use_perfil') === 'agente_saude')>{{ \App\Models\User::perfilLabel('agente_saude') }}</option>
                </select>
            </x-form-field>

            <x-form-field name="use_senha" :label="__('Senha')" :required="true">
                <x-text-input id="use_senha" name="use_senha" type="password" autocomplete="new-password" required />
                <div class="mt-2 h-1.5 w-full overflow-hidden rounded-full bg-gray-200 dark:bg-gray-600" role="presentation" aria-hidden="true">
                    <div id="password-strength-bar" class="h-full rounded-full bg-red-500 transition-all duration-300 ease-out" style="width: 0%"></div>
                </div>
            </x-form-field>

            <x-form-field name="use_senha_confirmation" :label="__('Confirmar senha')" :required="true">
                <x-text-input id="use_senha_confirmation" name="use_senha_confirmation" type="password" autocomplete="new-password" required />
                <p id="password-match-feedback" class="mt-1 hidden text-sm" aria-live="polite"></p>
            </x-form-field>

            <div class="flex justify-end">
                <x-primary-button type="submit" id="user-create-btn">{{ __('Salvar usuário') }}</x-primary-button>
            </div>
        </form>
    </x-section-card>
</div>
<script>
(function () {
    var form = document.getElementById('user-create-form');
    var btn = document.getElementById('user-create-btn');
    if (form && btn) {
        form.addEventListener('submit', function () {
            btn.disabled = true;
            btn.textContent = 'Salvando...';
        });
    }

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
    }

    var pwdConf = document.getElementById('use_senha_confirmation');
    var matchFeedback = document.getElementById('password-match-feedback');
    if (pwd && pwdConf && matchFeedback) {
        function updateMatchFeedback() {
            var p = pwd.value || '';
            var c = pwdConf.value || '';
            matchFeedback.classList.add('hidden');
            if (c.length === 0) return;

            if (p === c) {
                matchFeedback.textContent = 'Senhas conferem.';
                matchFeedback.classList.remove('text-red-600', 'dark:text-red-400');
                matchFeedback.classList.add('text-blue-600', 'dark:text-blue-400');
            } else {
                matchFeedback.textContent = 'As senhas não conferem.';
                matchFeedback.classList.remove('text-blue-600', 'dark:text-blue-400');
                matchFeedback.classList.add('text-red-600', 'dark:text-red-400');
            }

            matchFeedback.classList.remove('hidden');
        }

        pwd.addEventListener('input', updateMatchFeedback);
        pwdConf.addEventListener('input', updateMatchFeedback);
    }
})();
</script>
@endsection
