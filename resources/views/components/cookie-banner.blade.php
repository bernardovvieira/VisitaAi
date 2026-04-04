{{-- Aviso de cookies: exibido na home e consulta pública até o usuário aceitar --}}
<div id="cookie-banner" class="fixed bottom-0 left-0 right-0 z-[90] hidden" role="dialog" aria-label="{{ __('Aviso de cookies') }}">
    <div class="mx-auto max-w-7xl px-4 py-4 sm:px-6 lg:px-8">
        <div class="v-cookie-panel">
            <p class="text-sm leading-relaxed text-slate-700 dark:text-slate-300">
                {{ __('Utilizamos cookies para melhorar sua experiência, lembrar suas preferências (como tema claro/escuro) e garantir o funcionamento do sistema. Ao continuar, você concorda com o uso de cookies essenciais.') }}
            </p>
            <div class="flex shrink-0">
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
  var banner = document.getElementById('cookie-banner');
  var btn = document.getElementById('cookie-banner-accept');

  function hideBanner() {
    if (banner) banner.classList.add('hidden');
    try { localStorage.setItem(STORAGE_KEY, '1'); } catch (e) {}
  }

  function showBanner() {
    if (banner) banner.classList.remove('hidden');
  }

  try {
    if (localStorage.getItem(STORAGE_KEY) === '1') {
      hideBanner();
      return;
    }
  } catch (e) {}

  showBanner();
  if (btn) btn.addEventListener('click', hideBanner);
})();
</script>
