{{-- Contrato / matrícula / escritura do imóvel. $local opcional (edição). --}}
@php
    $local = $local ?? null;
    $hasDoc = $local && $local->exists && $local->loc_documento_posse_path;
    $downloadUrl = null;
    if ($hasDoc) {
        $downloadUrl = auth()->user()->isGestor()
            ? route('gestor.locais.documento-posse', $local)
            : route('agente.locais.documento-posse', $local);
    }
@endphp

<fieldset class="space-y-3">
    <legend class="v-section-title mb-2">{{ __('Documento do imóvel (posse)') }}</legend>
    <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Contrato, matrícula ou escritura (opcional). Formatos: PDF ou imagem. Limite: 10 MB.') }}</p>

    <div class="space-y-3 rounded-lg border border-slate-200 bg-slate-50/60 p-3 dark:border-slate-700 dark:bg-slate-900/40"
         x-data="{
            fileName: '',
            openPicker() { this.$refs.locDocPosse.click(); },
            updateName(event) { this.fileName = event.target.files && event.target.files.length ? event.target.files[0].name : ''; }
         }">
        @if($hasDoc)
            <p class="text-xs text-slate-700 dark:text-slate-200">
                <span class="font-semibold">{{ __('Arquivo atual') }}:</span>
                <span class="break-all">{{ $local->loc_documento_posse_nome ?: __('Documento salvo') }}</span>
            </p>
            <div class="flex flex-wrap gap-2">
                <a href="{{ $downloadUrl }}" target="_blank" rel="noopener"
                   class="inline-flex items-center rounded-md border border-slate-300 bg-white px-2.5 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
                    {{ __('Baixar documento atual') }}
                </a>
            </div>
            <label class="flex items-center gap-2 text-xs text-slate-700 dark:text-slate-300">
                <input type="checkbox" name="remover_documento_posse" value="1" class="rounded border-slate-300 text-red-600 focus:ring-red-500">
                <span>{{ __('Remover documento atual') }}</span>
            </label>
        @endif

        <div>
            <x-input-label for="loc_documento_posse" :value="__('Enviar ficheiro')" />
            <input type="file"
                   x-ref="locDocPosse"
                   id="loc_documento_posse"
                   name="loc_documento_posse"
                   accept="image/*,application/pdf,.pdf"
                   class="sr-only"
                   @change="updateName($event)">
            <div class="mt-1 flex flex-wrap items-center gap-3">
                <button type="button" @click="openPicker()"
                        class="inline-flex items-center rounded-md border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
                    {{ __('Selecionar arquivo') }}
                </button>
                <span class="text-xs text-slate-600 dark:text-slate-400" x-text="fileName ? ('{{ __('Novo') }}: ' + fileName) : '{{ __('Nenhum arquivo selecionado') }}'"></span>
            </div>
        </div>
        <x-input-error :messages="$errors->get('loc_documento_posse')" class="mt-1" />
        <x-input-error :messages="$errors->get('remover_documento_posse')" class="mt-1" />
    </div>
</fieldset>
