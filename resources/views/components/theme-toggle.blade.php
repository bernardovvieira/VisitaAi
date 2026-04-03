@props(['floating' => false])

@php
  $wrapperClass = $floating
    ? 'theme-toggle-float fixed z-[100]'
    : 'inline-flex items-center';
  $btnClass = $floating
    ? 'theme-toggle-circle w-12 h-12 rounded-full shadow-md hover:shadow-lg transition-all focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 dark:focus:ring-offset-gray-900 flex items-center justify-center'
    : 'inline-flex items-center p-2 rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition';
@endphp

<div class="{{ $wrapperClass }}" data-theme-toggle data-floating="{{ $floating ? '1' : '0' }}">
  <button type="button"
          data-theme-btn
          aria-label="{{ __('Alternar tema claro/escuro') }}"
          class="{{ $btnClass }}"
          title="{{ __('Alternar tema claro/escuro') }}">
    {{-- Sol: modo escuro → clique vai para claro --}}
    <span data-theme-icon="sun" class="hidden inline-flex text-amber-400">
      <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
        <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"/>
      </svg>
    </span>
    {{-- Lua: modo claro → clique vai para escuro --}}
    <span data-theme-icon="moon" class="inline-flex text-gray-600">
      <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
        <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/>
      </svg>
    </span>
  </button>
</div>

@if($floating)
<style>
.theme-toggle-float {
  position: fixed;
  right: 1.5rem;
  bottom: 1.5rem;
  left: auto;
  top: auto;
}
.theme-toggle-circle {
  background-color: #fff;
  border: 1px solid #e5e7eb;
}
.dark .theme-toggle-circle {
  background-color: #1f2937;
  border-color: #4b5563;
}
</style>
@endif

<script>
(function() {
  function updateIconsIn(wrap) {
    var isDark = document.documentElement.classList.contains('dark');
    var iconSun = wrap.querySelector('[data-theme-icon="sun"]');
    var iconMoon = wrap.querySelector('[data-theme-icon="moon"]');
    if (iconSun) iconSun.classList.toggle('hidden', !isDark);
    if (iconMoon) iconMoon.classList.toggle('hidden', isDark);
  }

  function updateAllIcons() {
    document.querySelectorAll('[data-theme-toggle]').forEach(updateIconsIn);
  }

  function doToggle() {
    if (window.VisitaTheme && typeof window.VisitaTheme.toggle === 'function') {
      window.VisitaTheme.toggle();
    } else {
      var isDark = document.documentElement.classList.contains('dark');
      var next = isDark ? 'light' : 'dark';
      try { localStorage.setItem('theme', next); } catch (e) {}
      document.documentElement.classList.toggle('dark', next === 'dark');
      if (window.VisitaThemeSyncUrl) {
        var tok = document.querySelector('meta[name="csrf-token"]');
        var opts = { method: 'PATCH', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': tok ? tok.getAttribute('content') : '', 'Accept': 'application/json' }, body: JSON.stringify({ tema: next }) };
        fetch(window.VisitaThemeSyncUrl, opts).catch(function() {});
      }
    }
    updateAllIcons();
  }

  function initToggle(wrap) {
    if (wrap.hasAttribute('data-theme-inited')) return;
    wrap.setAttribute('data-theme-inited', '1');
    var btn = wrap.querySelector('[data-theme-btn]');
    if (btn) btn.addEventListener('click', doToggle);
    updateIconsIn(wrap);
  }

  function run() {
    document.querySelectorAll('[data-theme-toggle]').forEach(initToggle);
  }
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', run);
  } else {
    run();
  }
})();
</script>
