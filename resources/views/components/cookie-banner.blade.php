{{-- Aviso de cookies: exibido na home e consulta pública até o usuário aceitar --}}
<div id="cookie-banner" class="fixed bottom-0 left-0 right-0 z-[90] hidden" role="dialog" aria-label="{{ __('Aviso de cookies') }}">
  <div class="mx-auto max-w-7xl px-3 pb-[calc(env(safe-area-inset-bottom)+0.75rem)] pt-3 sm:px-6 sm:py-4 lg:px-8">
        <div class="v-cookie-panel">
            <p class="text-sm leading-relaxed text-slate-700 dark:text-slate-300">
                {{ __('Utilizamos cookies para melhorar sua experiência, lembrar suas preferências (como tema claro/escuro) e garantir o funcionamento do sistema. Ao continuar, você concorda com o uso de cookies essenciais.') }}
            </p>
      <div class="flex shrink-0 flex-col gap-2 sm:flex-row">
        <button type="button"
            id="cookie-banner-dismiss"
            class="v-btn-secondary w-full sm:w-auto">
          {{ __('Agora não') }}
        </button>
                <button type="button"
                        id="cookie-banner-accept"
                        class="v-btn-primary w-full sm:w-auto">
                    {{ __('Aceitar') }}
                </button>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
  var STORAGE_KEY = 'visitaai_cookies_aceitos';
  var SESSION_DISMISS_KEY = 'visitaai_cookies_dispensado_sessao';
  var banner = document.getElementById('cookie-banner');
  var btn = document.getElementById('cookie-banner-accept');
  var dismiss = document.getElementById('cookie-banner-dismiss');

  function hideBanner(accepted) {
    if (banner) banner.classList.add('hidden');
    try {
      if (accepted) {
        localStorage.setItem(STORAGE_KEY, '1');
        sessionStorage.removeItem(SESSION_DISMISS_KEY);
      } else {
        sessionStorage.setItem(SESSION_DISMISS_KEY, '1');
      }
    } catch (e) {}
  }

  function showBanner() {
    if (banner) banner.classList.remove('hidden');
  }

  try {
    if (localStorage.getItem(STORAGE_KEY) === '1') {
      hideBanner(true);
      return;
    }
    if (sessionStorage.getItem(SESSION_DISMISS_KEY) === '1') {
      hideBanner(false);
      return;
    }
  } catch (e) {}

  showBanner();
  if (btn) btn.addEventListener('click', function() { hideBanner(true); });
  if (dismiss) dismiss.addEventListener('click', function() { hideBanner(false); });
})();
</script>
