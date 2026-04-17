{{-- Contrato / matrícula / escritura do imóvel. $local opcional (edição). --}}
@php
    $local = $local ?? null;
    $rp = auth()->user()->locaisRouteProfile();
    $docs = ($local && $local->exists)
        ? $local->documentosPosse
        : collect();
@endphp

<x-arquivos-zona
    variant="imovel"
    :titulo="__('Arquivos do imóvel (posse)')"
    :descricao="__('Contrato, matrícula, escritura ou outro comprovativo. PDF ou imagem, até 10 MB por arquivo. Pode anexar vários.')"
    class="mt-1"
>
    <div class="space-y-3"
         x-data="{
            fileSummary: '',
            openPicker() { this.$refs.locDocPosse.click(); },
            updateName(event) {
                const files = event.target.files;
                if (!files || !files.length) { this.fileSummary = ''; return; }
                if (files.length === 1) { this.fileSummary = files[0].name; return; }
                this.fileSummary = files.length + ' {{ __('arquivos selecionados') }}';
            }
         }">
        @if($docs->isNotEmpty())
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">{{ __('Arquivos já enviados') }}</p>
            <ul class="space-y-2">
                @foreach($docs as $doc)
                    <li class="flex flex-wrap items-center justify-between gap-2 rounded-lg border border-slate-200/80 bg-slate-50/80 px-3 py-2.5 text-xs dark:border-slate-600 dark:bg-slate-800/60">
                        <span class="min-w-0 flex-1 break-all font-medium text-slate-800 dark:text-slate-100">{{ $doc->original_name ?: __('Arquivo') }}</span>
                        <div class="flex flex-wrap items-center gap-2">
                            <a href="{{ route($rp.'.locais.documento-posse', [$local, $doc]) }}" target="_blank" rel="noopener"
                               class="inline-flex shrink-0 items-center rounded-md border border-slate-300 bg-white px-2.5 py-1.5 font-semibold text-slate-700 hover:bg-slate-100 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800">
                                {{ __('Baixar') }}
                            </a>
                            <label class="inline-flex shrink-0 items-center gap-1.5 text-slate-700 dark:text-slate-300">
                                <input type="checkbox" name="remover_documentos_posse[]" value="{{ $doc->id }}" class="rounded border-slate-300 text-red-600 focus:ring-red-500">
                                <span>{{ __('Remover') }}</span>
                            </label>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif

        <div class="rounded-lg border border-dashed border-slate-200/90 bg-slate-50/50 p-3 dark:border-slate-600 dark:bg-slate-800/40">
            <x-input-label for="loc_documentos_posse" :value="__('Adicionar arquivos')" class="text-slate-800 dark:text-slate-200" />
            <input type="file"
                   x-ref="locDocPosse"
                   id="loc_documentos_posse"
                   name="loc_documentos_posse[]"
                   accept="image/*,application/pdf,.pdf"
                   multiple
                   class="sr-only"
                   @change="updateName($event)">
            <div class="mt-2 flex flex-wrap items-center gap-3">
                <button type="button" @click="openPicker()"
                        class="inline-flex items-center rounded-md border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
                    {{ __('Selecionar arquivo(s)') }}
                </button>
                <span class="text-xs text-slate-600 dark:text-slate-400" x-text="fileSummary ? ('{{ __('Novo') }}: ' + fileSummary) : '{{ __('Nenhum arquivo selecionado') }}'"></span>
            </div>
        </div>
        @foreach($errors->keys() as $errKey)
            @if(str_starts_with((string) $errKey, 'loc_documentos_posse'))
                <x-input-error :messages="$errors->get($errKey)" class="mt-1" />
            @endif
        @endforeach
        @foreach($errors->keys() as $errKey)
            @if(str_starts_with((string) $errKey, 'remover_documentos_posse'))
                <x-input-error :messages="$errors->get($errKey)" class="mt-1" />
            @endif
        @endforeach
    </div>
</x-arquivos-zona>
