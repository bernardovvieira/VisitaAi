@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Configurar autenticador</h1>

    @if($errors->any())
        <x-alert type="error" :message="$errors->first() ?: optional($errors->confirmTwoFactorAuthentication)->first('code')" />
    @endif

    <div class="p-6 bg-white dark:bg-gray-700 rounded-lg shadow space-y-6">
        <h3 class="flex items-center text-lg font-semibold text-gray-800 dark:text-gray-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-amber-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
            Adicione a conta no seu app
        </h3>
        <p class="text-gray-600 dark:text-gray-400">
            Escaneie o QR code com o aplicativo autenticador no celular ou use a chave manual abaixo. Depois, digite o código de 6 dígitos que o app mostra.
        </p>

        {{-- Chave manual --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Chave manual</label>
            <div class="mt-1 flex gap-2">
                <input type="text"
                       value="{{ $secretKeyFormatted }}"
                       readonly
                       id="secret-key"
                       class="block w-full max-w-md font-mono text-sm rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 select-all">
                <button type="button"
                        id="btn-copy-secret"
                        data-secret="{{ $secretKey }}"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-600 hover:bg-gray-300 dark:hover:bg-gray-500 rounded-md transition">
                    Copiar
                </button>
            </div>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">No app, use &quot;Inserir chave manualmente&quot; e cole a chave (sem espaços).</p>
        </div>

        {{-- QR code (gerado em 192x192px; container no mesmo tamanho para não cortar) --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">QR code</label>
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
                <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Código do autenticador <span class="text-red-500">*</span></label>
                <input type="text"
                       id="code"
                       name="code"
                       inputmode="numeric"
                       pattern="[0-9]*"
                       maxlength="6"
                       autocomplete="one-time-code"
                       placeholder="000000"
                       class="mt-1 block w-full max-w-xs rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-green-500 focus:border-green-500"
                       required>
                @error('code', 'confirmTwoFactorAuthentication')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex flex-wrap gap-3">
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 text-sm font-semibold text-white bg-gray-600 hover:bg-gray-700 dark:bg-gray-600 dark:hover:bg-gray-500 rounded-md shadow transition">
                    Confirmar e ativar 2FA
                </button>
                <a href="{{ route('profile.edit') }}"
                   class="inline-flex items-center px-4 py-2 text-sm font-semibold text-white bg-red-600 hover:bg-red-700 dark:bg-red-600 dark:hover:bg-red-700 rounded-md shadow transition">
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
