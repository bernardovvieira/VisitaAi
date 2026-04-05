@php
    $isEdit = $tenant->exists;
@endphp

<div class="space-y-4">
    <div>
        <label for="slug" class="v-toolbar-label">slug <span class="text-red-500">*</span></label>
        <input id="slug" name="slug" type="text" value="{{ old('slug', $tenant->slug) }}" required pattern="[a-z0-9]+(?:-[a-z0-9]+)*"
               class="v-input mt-1 font-mono text-sm" autocomplete="off" @if($isEdit) readonly @endif />
        @error('slug')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        @if($isEdit)<p class="mt-1 text-xs text-slate-500">{{ __('Slug não editável; crie novo tenant se necessário.') }}</p>@endif
    </div>

    <div>
        <label for="environment" class="v-toolbar-label">{{ __('Ambiente') }} <span class="text-red-500">*</span></label>
        <select id="environment" name="environment" class="v-input mt-1" required>
            @foreach(['sandbox' => 'sandbox', 'production' => 'production'] as $val => $label)
                <option value="{{ $val }}" @selected(old('environment', $tenant->environment ?? 'production') === $val)>{{ $label }}</option>
            @endforeach
        </select>
        @error('environment')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="database" class="v-toolbar-label">MySQL database <span class="text-red-500">*</span></label>
        <input id="database" name="database" type="text" value="{{ old('database', $tenant->database) }}" required class="v-input mt-1 font-mono text-sm" />
        @error('database')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="db_host" class="v-toolbar-label">{{ __('Host MySQL (opcional)') }}</label>
        <input id="db_host" name="db_host" type="text" value="{{ old('db_host', $tenant->db_host) }}" class="v-input mt-1 font-mono text-sm" placeholder="DB_HOST padrão" />
        @error('db_host')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="db_username" class="v-toolbar-label">{{ __('Utilizador MySQL (opcional)') }}</label>
        <input id="db_username" name="db_username" type="text" value="{{ old('db_username', $tenant->db_username) }}" class="v-input mt-1 font-mono text-sm" autocomplete="off" />
        @error('db_username')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="db_password" class="v-toolbar-label">{{ __('Senha MySQL (opcional; deixe vazio para não alterar)') }}</label>
        <input id="db_password" name="db_password" type="password" value="" class="v-input mt-1 font-mono text-sm" autocomplete="new-password" />
        @error('db_password')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="display_name" class="v-toolbar-label">{{ __('Nome exibido (APP)') }}</label>
        <input id="display_name" name="display_name" type="text" value="{{ old('display_name', $tenant->display_name) }}" class="v-input mt-1" />
        @error('display_name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="brand" class="v-toolbar-label">{{ __('Marca curta') }}</label>
        <input id="brand" name="brand" type="text" value="{{ old('brand', $tenant->brand) }}" class="v-input mt-1" />
        @error('brand')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="settings_json" class="v-toolbar-label">{{ __('Settings (JSON opcional)') }}</label>
        <textarea id="settings_json" name="settings_json" rows="4" class="v-input mt-1 font-mono text-xs">{{ old('settings_json', $tenant->settings ? json_encode($tenant->settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '') }}</textarea>
        @error('settings_json')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div class="flex items-center gap-2">
        <input id="active" name="active" type="checkbox" value="1" class="rounded border-slate-300" @checked(old('active', $tenant->active ?? true)) />
        <label for="active" class="text-sm text-slate-700 dark:text-slate-300">{{ __('Ativo') }}</label>
    </div>
    @error('active')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
</div>
