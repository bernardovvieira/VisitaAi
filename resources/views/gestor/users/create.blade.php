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

            <div>
                <label for="use_nome" class="v-toolbar-label">{{ __('Nome') }} <span class="text-red-500">*</span></label>
                <input type="text" id="use_nome" name="use_nome" value="{{ old('use_nome') }}" required autofocus
                       class="v-input mt-1 @error('use_nome') border border-red-500 dark:border-red-400 @enderror">
                <x-input-error :messages="$errors->get('use_nome')" class="mt-1" />
            </div>

            <div>
                <label for="use_cpf" class="v-toolbar-label">{{ __('CPF') }} <span class="text-red-500">*</span></label>
                <input type="text" id="use_cpf" name="use_cpf" value="{{ old('use_cpf') }}" required
                       class="v-input mt-1 @error('use_cpf') border border-red-500 dark:border-red-400 @enderror">
                <x-input-error :messages="$errors->get('use_cpf')" class="mt-1" />
            </div>

            <div>
                <label for="use_email" class="v-toolbar-label">{{ __('E-mail') }} <span class="text-red-500">*</span></label>
                <input type="email" id="use_email" name="use_email" value="{{ old('use_email') }}" required
                       class="v-input mt-1 @error('use_email') border border-red-500 dark:border-red-400 @enderror">
                <x-input-error :messages="$errors->get('use_email')" class="mt-1" />
            </div>

            <div>
                <label for="use_perfil" class="v-toolbar-label">{{ __('Perfil') }} <span class="text-red-500">*</span></label>
                <select id="use_perfil" name="use_perfil" class="v-select mt-1" required>
                    <option value="gestor" @selected(old('use_perfil') === 'gestor')>{{ \App\Models\User::perfilLabel('gestor') }}</option>
                    <option value="agente_endemias" @selected(old('use_perfil') === 'agente_endemias')>{{ \App\Models\User::perfilLabel('agente_endemias') }}</option>
                    <option value="agente_saude" @selected(old('use_perfil') === 'agente_saude')>{{ \App\Models\User::perfilLabel('agente_saude') }}</option>
                </select>
                <x-input-error :messages="$errors->get('use_perfil')" class="mt-1" />
            </div>

            <div>
                <label for="use_senha" class="v-toolbar-label">{{ __('Senha') }} <span class="text-red-500">*</span></label>
                <input type="password" id="use_senha" name="use_senha" autocomplete="new-password" required
                       class="v-input mt-1 @error('use_senha') border border-red-500 dark:border-red-400 @enderror">
                <x-input-error :messages="$errors->get('use_senha')" class="mt-1" />
            </div>

            <div>
                <label for="use_senha_confirmation" class="v-toolbar-label">{{ __('Confirmar senha') }} <span class="text-red-500">*</span></label>
                <input type="password" id="use_senha_confirmation" name="use_senha_confirmation" autocomplete="new-password" required
                       class="v-input mt-1">
            </div>

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
})();
</script>
@endsection
