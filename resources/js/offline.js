/**
 * Visita Aí – suporte offline (rascunhos + sincronização + aviso de visitas pendentes).
 * - Registra o Service Worker para cache das páginas.
 * - Se window.VisitaOfflineSyncPageUrl e window.VisitaOfflineProfile estiverem definidos (agente/saude),
 *   exibe banner quando houver visitas pendentes de envio (ao carregar e ao voltar online).
 * - Expõe window.VisitaOfflineSaveDraft(perfil, payload) para o formulário de visita.
 */
const DB_NAME = 'VisitaAiOffline';
const STORE_NAME = 'visitas_rascunho';

function openDB() {
    return new Promise((resolve, reject) => {
        const r = indexedDB.open(DB_NAME, 1);
        r.onerror = () => reject(r.error);
        r.onsuccess = () => resolve(r.result);
        r.onupgradeneeded = (e) => {
            const db = e.target.result;
            if (!db.objectStoreNames.contains(STORE_NAME)) {
                db.createObjectStore(STORE_NAME, { keyPath: 'id' });
            }
        };
    });
}

export function getPendingCount(perfil) {
    return openDB().then((db) => {
        return new Promise((resolve, reject) => {
            const t = db.transaction(STORE_NAME, 'readonly');
            const store = t.objectStore(STORE_NAME);
            const req = store.getAll();
            req.onsuccess = () => {
                const all = req.result || [];
                resolve(all.filter((d) => d.perfil === perfil).length);
            };
            req.onerror = () => reject(req.error);
        });
    });
}

export function getDrafts(perfil) {
    return openDB().then((db) => {
        return new Promise((resolve, reject) => {
            const t = db.transaction(STORE_NAME, 'readonly');
            const store = t.objectStore(STORE_NAME);
            const req = store.getAll();
            req.onsuccess = () => {
                const all = req.result || [];
                resolve(all.filter((d) => d.perfil === perfil));
            };
            req.onerror = () => reject(req.error);
        });
    });
}

export function getDraft(id) {
    return openDB().then((db) => {
        return new Promise((resolve, reject) => {
            const t = db.transaction(STORE_NAME, 'readonly');
            const store = t.objectStore(STORE_NAME);
            const req = store.get(id);
            req.onsuccess = () => resolve(req.result || null);
            req.onerror = () => reject(req.error);
        });
    });
}

export function deleteDraft(id) {
    return openDB().then((db) => {
        return new Promise((resolve, reject) => {
            const t = db.transaction(STORE_NAME, 'readwrite');
            const store = t.objectStore(STORE_NAME);
            const req = store.delete(id);
            req.onsuccess = () => resolve();
            req.onerror = () => reject(req.error);
        });
    });
}

export function saveDraft(perfil, payload) {
    const id = 'draft-' + Date.now() + '-' + Math.random().toString(36).slice(2, 9);
    const record = { id, perfil, payload, createdAt: new Date().toISOString() };
    return openDB().then((db) => {
        return new Promise((resolve, reject) => {
            const t = db.transaction(STORE_NAME, 'readwrite');
            const store = t.objectStore(STORE_NAME);
            store.put(record);
            t.oncomplete = () => resolve(id);
            t.onerror = () => reject(t.error);
        });
    });
}

function isOnline() {
    if (typeof window.visitaConnectionOnline === 'boolean') return window.visitaConnectionOnline;
    return typeof navigator !== 'undefined' && navigator.onLine;
}

function showPendingBanner(count, syncPageUrl) {
    if (count === 0 || !isOnline()) return;
    let el = document.getElementById('visita-offline-pending-banner');
    if (el) {
        const msg = el.querySelector('[data-pending-msg]');
        if (msg) msg.textContent = (count === 1 ? '1 visita guardada' : count + ' visitas guardadas') + ' no dispositivo. ';
        const link = el.querySelector('a[data-sync-link]');
        if (link && syncPageUrl) link.setAttribute('href', syncPageUrl);
        return;
    }
    el = document.createElement('div');
    el.id = 'visita-offline-pending-banner';
    el.setAttribute('role', 'alert');
    el.className = 'bg-amber-500 text-amber-900 px-4 py-3 flex flex-wrap items-center justify-center gap-3 text-sm font-medium shadow';
    const span = document.createElement('span');
    span.setAttribute('data-pending-msg', '');
    span.textContent = (count === 1 ? '1 visita guardada' : count + ' visitas guardadas') + ' no dispositivo. ';
    const a = document.createElement('a');
    a.setAttribute('data-sync-link', '');
    a.setAttribute('href', syncPageUrl || '#');
    a.className = 'underline font-semibold hover:no-underline';
    a.textContent = 'Enviar agora';
    const btn = document.createElement('button');
    btn.setAttribute('type', 'button');
    btn.setAttribute('aria-label', 'Fechar');
    btn.className = 'ml-2 px-2 py-1 rounded bg-amber-600 hover:bg-amber-700 text-amber-900';
    btn.textContent = '\u2715';
    btn.addEventListener('click', () => el.remove());
    el.appendChild(span);
    el.appendChild(a);
    el.appendChild(btn);
    document.body.insertBefore(el, document.body.firstChild);
}

function hidePendingBanner() {
    const el = document.getElementById('visita-offline-pending-banner');
    if (el) el.remove();
}

function updatePendingBanner() {
    const syncPageUrl = window.VisitaOfflineSyncPageUrl;
    const perfil = window.VisitaOfflineProfile;
    if (!syncPageUrl || !perfil) {
        hidePendingBanner();
        return;
    }
    if (!isOnline()) {
        hidePendingBanner();
        return;
    }
    getPendingCount(perfil)
        .then((count) => {
            if (count > 0) showPendingBanner(count, syncPageUrl);
            else hidePendingBanner();
        })
        .catch(() => hidePendingBanner());
}

if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js', { scope: '/' }).catch(() => {});
    });
}

window.addEventListener('load', () => {
    if (navigator.onLine && window.VisitaOfflineSyncPageUrl && window.VisitaOfflineProfile) {
        updatePendingBanner();
    }
});

window.addEventListener('online', () => {
    if (window.VisitaOfflineSyncPageUrl && window.VisitaOfflineProfile) {
        updatePendingBanner();
    }
});

document.addEventListener('visita-connection-change', (e) => {
    if (!e.detail.online) hidePendingBanner();
    else if (window.VisitaOfflineSyncPageUrl && window.VisitaOfflineProfile) updatePendingBanner();
});
window.addEventListener('visita-connection-change', (e) => {
    if (!e.detail.online) hidePendingBanner();
    else if (window.VisitaOfflineSyncPageUrl && window.VisitaOfflineProfile) updatePendingBanner();
});

window.VisitaOfflineSaveDraft = function (perfil, payload) {
    return saveDraft(perfil, payload);
};

window.VisitaOfflineGetPendingCount = getPendingCount;
window.VisitaOfflineUpdateBanner = updatePendingBanner;
window.VisitaOfflineGetDrafts = getDrafts;
window.VisitaOfflineGetDraft = getDraft;
window.VisitaOfflineDeleteDraft = deleteDraft;
