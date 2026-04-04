@props(['floating' => false])

@php
  $wrapperClass = $floating
    ? 'theme-toggle-float fixed z-[100]'
    : 'inline-flex items-center';
  $btnClass = $floating
    ? 'theme-toggle-circle flex h-12 w-12 items-center justify-center rounded-full transition-all focus:outline-none focus:ring-2 focus:ring-blue-500/45 focus:ring-offset-2 dark:focus:ring-offset-slate-900'
    : 'inline-flex items-center rounded-xl border border-slate-200/90 bg-white/90 p-2 text-slate-600 shadow-sm backdrop-blur-sm transition hover:border-slate-300 hover:bg-slate-50 hover:text-slate-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500/35 dark:border-slate-600 dark:bg-slate-800/90 dark:text-slate-300 dark:hover:border-slate-500 dark:hover:bg-slate-700 dark:hover:text-white';
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
    <span data-theme-icon="moon" class="inline-flex text-slate-500 dark:text-slate-400">
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
  background-color: rgba(255, 255, 255, 0.92);
  border: 1px solid rgb(226 232 240 / 0.95);
  box-shadow:
    0 4px 20px rgb(15 23 42 / 0.12),
    0 1px 0 rgb(255 255 255 / 0.7) inset;
  backdrop-filter: blur(12px) saturate(150%);
  -webkit-backdrop-filter: blur(12px) saturate(150%);
}
.theme-toggle-circle:hover {
  box-shadow:
    0 8px 28px rgb(15 23 42 / 0.16),
    0 1px 0 rgb(255 255 255 / 0.75) inset;
}
.dark .theme-toggle-circle {
  background-color: rgb(15 23 42 / 0.9);
  border-color: rgb(51 65 85 / 0.9);
  box-shadow:
    0 4px 24px rgb(0 0 0 / 0.4),
    inset 0 1px 0 rgb(255 255 255 / 0.06);
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
