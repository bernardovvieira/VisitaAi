/**
 * Tema claro/escuro: persistido em localStorage e aplicado via classe em <html>.
 * Tailwind usa darkMode: 'class', então .dark no html ativa as variantes dark:
 */

const STORAGE_KEY = 'theme';

function getStored() {
  try {
    return localStorage.getItem(STORAGE_KEY);
  } catch {
    return null;
  }
}

function setStored(value) {
  try {
    if (value) localStorage.setItem(STORAGE_KEY, value);
    else localStorage.removeItem(STORAGE_KEY);
  } catch {
    // Ignore storage errors (private mode/quota).
  }
}

function prefersDark() {
  return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
}

/**
 * Aplica o tema ao documento (classe 'dark' no html).
 * @param {'light'|'dark'} theme
 */
export function applyTheme(theme) {
  const html = document.documentElement;
  if (theme === 'dark') {
    html.classList.add('dark');
  } else {
    html.classList.remove('dark');
  }
}

/**
 * Retorna o tema efetivo atual (light ou dark).
 */
export function getTheme() {
  return document.documentElement.classList.contains('dark') ? 'dark' : 'light';
}

/**
 * Inicializa o tema na carga da página: localStorage ou preferência do sistema.
 * Chamar o mais cedo possível (ex.: script no head) para evitar flash.
 */
export function initTheme() {
  const stored = getStored();
  if (stored === 'dark' || stored === 'light') {
    applyTheme(stored);
    return stored;
  }
  const dark = prefersDark();
  applyTheme(dark ? 'dark' : 'light');
  return dark ? 'dark' : 'light';
}

/**
 * Alterna entre claro e escuro; persiste e aplica.
 * @returns {'light'|'dark'} novo tema
 */
export function toggleTheme() {
  const next = getTheme() === 'dark' ? 'light' : 'dark';
  setStored(next);
  applyTheme(next);
  return next;
}

// Para uso global (ex.: onclick no Blade)
window.VisitaTheme = {
  init: initTheme,
  toggle: toggleTheme,
  get: getTheme,
  apply: applyTheme,
};
