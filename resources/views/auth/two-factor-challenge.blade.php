<x-guest-layout>
    <div class="mx-auto mt-8 w-full max-w-md">
        <h1 class="mb-2 text-xl font-semibold text-slate-900 dark:text-slate-100 sm:text-2xl">
            {{ __('Código de autenticação') }}
        </h1>
        <p class="mb-6 text-sm text-slate-600 dark:text-slate-400">
            {{ __('Digite o código de 6 dígitos gerado pelo seu aplicativo autenticador.') }}
        </p>

        @if ($errors->any())
            <div class="v-alert v-alert--error mb-4 text-left" role="alert">
                <p class="text-sm font-medium">{{ $errors->first() }}</p>
            </div>
        @endif

        <form method="POST"
              action="{{ route('two-factor.login.store') }}"
              id="two-factor-form"
              data-label-verifying="{{ __('Verificando…') }}">
            @csrf
            <div class="mb-4">
                <x-input-label for="code">{{ __('Código') }} <span class="text-red-500">*</span></x-input-label>
                <input type="text"
                       id="code"
                       name="code"
                       inputmode="numeric"
                       pattern="[0-9]*"
                       maxlength="6"
                       autocomplete="one-time-code"
                       placeholder="000000"
                       class="v-input mt-1 block w-full"
                       required
                       autofocus>
            </div>
            <div class="mt-6 flex flex-wrap gap-3">
                <a href="{{ route('login') }}" class="v-btn-secondary inline-flex items-center justify-center px-4 py-2.5 text-[13px] font-semibold">
                    {{ __('Voltar ao login') }}
                </a>
                <x-primary-button type="submit" id="two-factor-submit-btn">{{ __('Verificar') }}</x-primary-button>
            </div>
        </form>
        <script>
            (function () {
                var form = document.getElementById('two-factor-form');
                var btn = document.getElementById('two-factor-submit-btn');
                if (!form || !btn) {
                    return;
                }
                form.addEventListener('submit', function () {
                    btn.disabled = true;
                    var verifying = form.getAttribute('data-label-verifying') || '';
                    var spin = document.createElement('span');
                    spin.className = 'inline-flex items-center';
                    var svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
                    svg.setAttribute('class', 'animate-spin -ml-1 mr-2 h-4 w-4');
                    svg.setAttribute('fill', 'none');
                    svg.setAttribute('viewBox', '0 0 24 24');
                    var c1 = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
                    c1.setAttribute('class', 'opacity-25');
                    c1.setAttribute('cx', '12');
                    c1.setAttribute('cy', '12');
                    c1.setAttribute('r', '10');
                    c1.setAttribute('stroke', 'currentColor');
                    c1.setAttribute('stroke-width', '4');
                    var p1 = document.createElementNS('http://www.w3.org/2000/svg', 'path');
                    p1.setAttribute('class', 'opacity-75');
                    p1.setAttribute('fill', 'currentColor');
                    p1.setAttribute('d', 'M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z');
                    svg.appendChild(c1);
                    svg.appendChild(p1);
                    spin.appendChild(svg);
                    spin.appendChild(document.createTextNode(verifying));
                    btn.replaceChildren(spin);
                });
            })();
        </script>
    </div>
</x-guest-layout>
