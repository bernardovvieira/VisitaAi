@php
    $txt = trim((string) config('visitaai_municipio.lgpd.contextos.visitas_observacoes_ocupantes', ''));
@endphp
@if(filled($txt))
    <x-ui.disclosure variant="amber-compact">
        <x-slot name="summary">
            <span class="border-b border-dotted border-amber-700/50 pb-px dark:border-amber-400/50">{{ __('LGPD: observações sobre ocupantes nesta visita (ver texto completo)') }}</span>
        </x-slot>
        {{ $txt }}
    </x-ui.disclosure>
@endif
