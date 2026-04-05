{{-- Resumo visível; texto completo (config municipal) em <details> para reduzir ruído. --}}
@php
    $full = trim((string) config('visitaai_municipio.ocupantes.disclaimer', ''));
@endphp
@if(filled($full))
    <x-ui.disclosure variant="amber">
        <x-slot name="summary">
            <span class="border-b border-dotted border-amber-700/50 pb-px dark:border-amber-400/50">{{ __('Resumo: dados opcionais do imóvel (LGPD e SUS). Ver orientação completa.') }}</span>
        </x-slot>
        {{ $full }}
    </x-ui.disclosure>
@endif
