@extends('layouts.app')

@section('content')
<div class="v-page space-y-5">
    <x-page-header title="Configurar autenticador" />

    @if($errors->any())
        <x-alert type="error" :message="$errors->first() ?: optional($errors->confirmTwoFactorAuthentication)->first('code')" />
    @endif

    <div class="v-card space-y-4">
        <h3 class="v-section-title flex items-center gap-2">
            <x-heroicon-o-lock-closed class="h-5 w-5 shrink-0 text-amber-500" />
            Adicione a conta no seu app
        </h3>
        <p class="text-gray-600 dark:text-gray-400">
            Escaneie o QR code com o aplicativo autenticador no celular ou use a chave manual abaixo. Depois, digite o código de 6 dígitos que o app mostra.
        </p>

        {{-- Chave manual --}}
        <div>
            <label class="v-toolbar-label">Chave manual</label>
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
            <label class="v-toolbar-label mb-1">QR code</label>
            <div class="p-4 rounded-md border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 inline-block">
                <div class="w-[192px] h-[192px] flex items-center justify-center [&>svg]:shrink-0 [&>svg]:block">
                    {!! $qrCodeSvg !!}
                </div>
            </div>
        </div>

        {{-- Código e botões --}}
        <form method="POST" action="{{ url(route('two-factor.confirm')) }}" class="space-y-4">
            @csrf
            <div>
                <label for="code" class="v-toolbar-label">Código do autenticador <span class="text-red-500">*</span></label>
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
                @error('code', 'confirmTwoFactorAuthentication')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex flex-wrap gap-3">
                <button type="submit"
                        class="inline-flex items-center rounded-md border border-transparent bg-slate-600 px-2.5 py-1 text-xs font-semibold text-white shadow-sm transition hover:bg-slate-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-500/40 dark:bg-slate-600 dark:hover:bg-slate-500">
                    Confirmar e ativar 2FA
                </button>
                <a href="{{ route('profile.edit') }}"
                   class="inline-flex items-center rounded-md border border-transparent bg-red-600 px-2.5 py-1 text-xs font-semibold text-white shadow-sm transition hover:bg-red-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500/40">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
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
