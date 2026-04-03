{{-- Aviso de cookies: exibido na home e consulta pública até o usuário aceitar --}}
<div id="cookie-banner" class="fixed bottom-0 left-0 right-0 z-[90] hidden" role="dialog" aria-label="Aviso de cookies">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 p-4 rounded-t-lg shadow-lg bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
            <p class="text-sm text-gray-700 dark:text-gray-300">
                Utilizamos cookies para melhorar sua experiência, lembrar suas preferências (como tema claro/escuro) e garantir o funcionamento do sistema.
                Ao continuar, você concorda com o uso de cookies essenciais.
            </p>
            <div class="flex-shrink-0">
                <button type="button"
                        id="cookie-banner-accept"
                        class="rounded-md border border-transparent bg-blue-600 px-3 py-1.5 text-[13px] font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500/40 dark:bg-blue-600 dark:hover:bg-blue-500">
                    Aceitar
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
