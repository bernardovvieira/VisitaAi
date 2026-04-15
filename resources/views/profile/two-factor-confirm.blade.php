@extends('layouts.app')

@section('og_title', config('app.brand') . ' · ' . __('Configurar autenticador'))
@section('og_description', __('Escaneie o QR code ou use a chave manual e confirme com o código de 6 dígitos.'))

@section('content')
<div class="v-page space-y-5">
    <x-page-header :title="__('Configurar autenticador')" />

    @if($errors->any())
        <x-alert type="error" :message="$errors->first() ?: optional($errors->confirmTwoFactorAuthentication)->first('code')" />
    @endif

    <x-section-card class="space-y-4">
        <h3 class="v-section-title flex items-center gap-2">
            <x-heroicon-o-lock-closed class="h-5 w-5 shrink-0 text-amber-500" />
            Adicione a conta no seu app
        </h3>
        <p class="text-gray-600 dark:text-gray-400">
            Escaneie o QR code com o aplicativo autenticador no celular ou use a chave manual abaixo. Depois, digite o código de 6 dígitos que o app mostra.
        </p>

        {{-- Chave manual --}}
        <div>
            <x-input-label :value="__('Chave manual')" class="mb-1" />
            <div class="mt-1 flex gap-2">
                <input type="text"
                       value="{{ $secretKeyFormatted }}"
                       readonly
                       id="secret-key"
                       class="block w-full max-w-md font-mono text-sm rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 select-all">
                <button type="button"
                        id="btn-copy-secret"
                        data-secret="{{ $secretKey }}"
                        class="inline-flex items-center rounded-md border border-slate-300 bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
                    Copiar
                </button>
            </div>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">No app, use &quot;Inserir chave manualmente&quot; e cole a chave (sem espaços).</p>
        </div>

        {{-- QR code (gerado em 192x192px; container no mesmo tamanho para não cortar) --}}
        <div>
            <x-input-label :value="__('QR code')" class="mb-1" />
            <div class="p-4 rounded-md border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 inline-block">
                <div class="w-[192px] h-[192px] flex items-center justify-center [&>svg]:shrink-0 [&>svg]:block">
                    {!! $qrCodeSvg !!}
                </div>
            </div>
        </div>

        {{-- Código e botões --}}
        <form method="POST" action="{{ url(route('two-factor.confirm')) }}" class="space-y-4">
            @csrf
            <x-form-field name="code" :label="__('Código do autenticador')" :required="true" :messages="$errors->confirmTwoFactorAuthentication->get('code')">
                <input type="text"
                       id="code"
                       name="code"
                       inputmode="numeric"
                       pattern="[0-9]*"
                       maxlength="6"
                       autocomplete="one-time-code"
                       placeholder="000000"
                       class="v-input mt-1 max-w-xs"
                       required>
            </x-form-field>
            <div class="flex flex-wrap gap-3">
                <button type="submit"
                        class="v-btn-slate !px-2.5 !py-1 !text-xs">
                    Confirmar e ativar 2FA
                </button>
                <a href="{{ route('profile.edit') }}"
                   class="v-btn-danger !px-2.5 !py-1 !text-xs">
                    Cancelar
                </a>
            </div>
        </form>
    </x-section-card>
</div>

<script>
document.getElementById('btn-copy-secret')?.addEventListener('click', function () {
    var secret = this.getAttribute('data-secret') || '';
    navigator.clipboard.writeText(secret).then(function () {
        var btn = document.getElementById('btn-copy-secret');
        if (btn) { btn.textContent = 'Copiado!'; setTimeout(function () { btn.textContent = 'Copiar'; }, 2000); }
    });
});
</script>
@endsection
