@extends('layouts.app')

@section('content')
<div class="v-page">
    <x-breadcrumbs :items="[['label' => __('Página Inicial'), 'url' => route('dashboard')], ['label' => __('Usuários'), 'url' => route('gestor.users.index')], ['label' => __('Editar')]]" />
    <x-page-header :eyebrow="__('Gestão municipal')" :title="__('Editar usuário')" />

    <!-- Card introdutório -->
    <section class="v-card">
        <h2 class="v-section-title">{{ __('Dados da conta') }}</h2>
        <p class="mt-2 text-gray-600 dark:text-gray-400">
            Atualize as informações do usuário conforme necessário. Se não desejar alterar a senha, deixe o campo de senha em branco.
        </p>
    </section>

    <!-- Formulário de Edição -->
    <section class="v-card space-y-4">
        @if(session('status'))
            <x-alert type="success" :message="session('status')" />
        @endif

        @if ($errors->any())
            @php
                $fieldLabels = [
                    'use_nome' => 'Nome',
                    'use_email' => 'E-mail',
                    'use_perfil' => 'Perfil',
                    'use_senha' => 'Nova senha',
                    'use_senha_confirmation' => 'Confirmar nova senha',
                ];
                $errorFields = array_unique($errors->keys());
                $labels = array_map(fn ($k) => $fieldLabels[$k] ?? $k, $errorFields);
            @endphp
            <div class="px-4 py-3 rounded-lg bg-red-600 dark:bg-red-700 text-white text-sm" role="alert">
                <p class="font-medium">Corrija os erros nos campos indicados abaixo.</p>
                @if (count($labels) > 0)
                    <p class="mt-1 opacity-90">Campos com erro: {{ implode(', ', $labels) }}.</p>
                @endif
            </div>
        @endif

        <form method="POST" action="{{ route('gestor.users.update', $user) }}" class="space-y-6" id="user-edit-form">
            @csrf
            @method('PATCH')

            <!-- Nome -->
            <div>
                <label for="use_nome" class="v-toolbar-label">Nome <span class="text-red-500">*</span></label>
                <input type="text" id="use_nome" name="use_nome" value="{{ old('use_nome', $user->use_nome) }}" 
                       required autofocus
                       class="v-input mt-1 @error('use_nome') border-red-500 dark:border-red-400 border @enderror">
                <x-input-error :messages="$errors->get('use_nome')" class="mt-1" />
            </div>

            <!-- CPF (somente leitura) -->
            <div>
                <label for="cpf_mascarado" class="v-toolbar-label">CPF <span class="text-red-500">*</span></label>
                <input type="text"
                       id="cpf_mascarado"
                       name="cpf_mascarado"
                       value="{{ old('cpf_mascarado', preg_replace('/\d(?=(?:.*\d){2})/', '*', $user->use_cpf)) }}" 
                       readonly
                       class="v-input mt-1">
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Não é possível alterar o CPF. Caso precise, entre em contato com a Bitwise Technologies (suporte).</p>
            </div>

            <!-- Email -->
            <div>
                <label for="use_email" class="v-toolbar-label">E-mail <span class="text-red-500">*</span></label>
                <input type="email" id="use_email" name="use_email" value="{{ old('use_email', $user->use_email) }}"
                       required
                       class="v-input mt-1 @error('use_email') border-red-500 dark:border-red-400 border @enderror">
                <x-input-error :messages="$errors->get('use_email')" class="mt-1" />
            </div>

            <!-- Perfil -->
            <div>
                <label for="use_perfil" class="v-toolbar-label">Perfil <span class="text-red-500">*</span></label>
                <select id="use_perfil" name="use_perfil"
                        class="v-select mt-1 @error('use_perfil') border-red-500 dark:border-red-400 border @enderror" required>
                    <option value="gestor" {{ old('use_perfil', $user->use_perfil) == 'gestor' ? 'selected' : '' }}>{{ \App\Models\User::perfilLabel('gestor') }}</option>
                    <option value="agente_endemias" {{ old('use_perfil', $user->use_perfil) == 'agente_endemias' ? 'selected' : '' }}>{{ \App\Models\User::perfilLabel('agente_endemias') }}</option>
                    <option value="agente_saude" {{ old('use_perfil', $user->use_perfil) == 'agente_saude' ? 'selected' : '' }}>{{ \App\Models\User::perfilLabel('agente_saude') }}</option>
                </select>
                <x-input-error :messages="$errors->get('use_perfil')" class="mt-1" />
            </div>

            <!-- Data Cadastro -->
            <div>
                <label for="data_cadastro_display" class="v-toolbar-label">Data de Cadastro</label>
                <input type="text" id="data_cadastro_display" value="{{ $user->use_data_criacao->format('d/m/Y') }}" 
                       readonly
                       class="v-input mt-1 cursor-default border-slate-200/80 bg-slate-100 focus:ring-0 dark:border-slate-600 dark:bg-slate-900/80">
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Data em que o usuário se cadastrou.</p>
            </div>

            <!-- Nova Senha -->
            <div>
                <label for="use_senha" class="v-toolbar-label">Nova Senha</label>
                <input type="password" id="use_senha" name="use_senha" autocomplete="new-password"
                       class="v-input mt-1 @error('use_senha') border border-red-500 dark:border-red-400 @enderror">
                <div class="mt-2 h-1.5 w-full rounded-full bg-gray-200 dark:bg-gray-600 overflow-hidden" role="presentation" aria-hidden="true">
                    <div id="password-strength-bar" class="h-full rounded-full bg-red-500 transition-all duration-300 ease-out" style="width: 0%"></div>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Mínimo 8 caracteres, com letras, números e pelo menos um caractere especial (ex.: @, #, $, !). Deixe em branco se não quiser alterar a senha atual.</p>
                <x-input-error :messages="$errors->get('use_senha')" class="mt-1" />
            </div>

            <!-- Confirmar Nova Senha -->
            <div>
                <label for="use_senha_confirmation" class="v-toolbar-label">Confirmar Nova Senha</label>
                <input type="password" id="use_senha_confirmation" name="use_senha_confirmation" autocomplete="new-password"
                       class="v-input mt-1 @error('use_senha_confirmation') border-red-500 dark:border-red-400 border @enderror">
                <p id="password-match-feedback" class="mt-1 text-sm hidden" aria-live="polite"></p>
                <x-input-error :messages="$errors->get('use_senha_confirmation')" class="mt-1" />
            </div>

            <div class="flex justify-end">
                <x-primary-button type="submit" id="user-edit-btn">{{ __('Salvar alterações') }}</x-primary-button>
            </div>
        </form>
    </section>

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
