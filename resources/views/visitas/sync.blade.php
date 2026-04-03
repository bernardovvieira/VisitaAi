@extends('layouts.app')

@section('og_title', config('app.name') . ' · Sincronizar')
@section('og_description', 'Envie locais e visitas salvos no dispositivo quando estiver sem internet.')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <x-breadcrumbs :items="[['label' => 'Página Inicial', 'url' => route('dashboard')], ['label' => 'Sincronizar']]" />
    <h1 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-gray-100">Sincronizar</h1>

    <section class="rounded-xl border border-gray-200/80 bg-white p-5 shadow-sm dark:border-gray-600 dark:bg-gray-800">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Enviar dados guardados offline</h2>
        <p class="mt-2 text-gray-600 dark:text-gray-400">Envie locais e visitas guardados no dispositivo. Serão enviados primeiro os locais, depois as visitas.</p>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Quando você usar "Guardar no dispositivo para enviar depois" na tela de registrar visita, as visitas aparecerão aqui para enviar.</p>
        <div id="sync-actions" class="hidden flex flex-wrap gap-3 items-center mt-4">
            <button type="button" id="sync-btn"
                    class="rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500/60 disabled:cursor-not-allowed disabled:opacity-50">
                Enviar todas agora
            </button>
            <button type="button" id="sync-clear-btn"
                    class="rounded-xl bg-red-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-red-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500/50 disabled:cursor-not-allowed disabled:opacity-50">
                Apagar todas do dispositivo
            </button>
            <span class="text-sm text-gray-500 dark:text-gray-400" id="sync-result"></span>
        </div>
    </section>

    @if(!empty($locaisSyncSubmitUrl))
    <section class="rounded-xl border border-gray-200/80 bg-white p-5 shadow-sm dark:border-gray-600 dark:bg-gray-800" id="sync-locais-section"
             data-sync-url="{{ $locaisSyncSubmitUrl }}" data-index-url="{{ $locaisIndexRoute ?? $visitasIndexRoute }}">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Locais guardados no dispositivo</h2>
        <p class="mt-2 text-gray-600 dark:text-gray-400" id="sync-locais-status">Carregando…</p>
        <div id="sync-locais-list" class="space-y-2 mt-4"></div>
    </section>
    @endif

    <section class="rounded-xl border border-gray-200/80 bg-white p-5 shadow-sm dark:border-gray-600 dark:bg-gray-800"
             id="sync-section"
             data-sync-url="{{ $syncSubmitUrl }}"
             data-locais-sync-url="{{ $locaisSyncSubmitUrl ?? '' }}"
             data-csrf-token="{{ csrf_token() }}"
             data-perfil="{{ $perfil }}"
             data-index-url="{{ $visitasIndexRoute }}">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Visitas guardadas no dispositivo</h2>
        <p class="mt-2 text-gray-600 dark:text-gray-400" id="sync-status">
            Carregando…
        </p>
        <div id="sync-list" class="space-y-2 mt-4">
            <!-- Preenchido via JS a partir do IndexedDB -->
        </div>
        <p id="sync-offline-warning" class="hidden mt-4 text-sm text-amber-700 dark:text-amber-300 bg-amber-100 dark:bg-amber-900/30 px-3 py-2 rounded">
            Você está sem internet. Conecte o dispositivo à internet (Wi‑Fi ou dados) para poder enviar as visitas.
        </p>
    </section>
</div>

<script>
(function() {
    const DB_NAME = 'VisitaAiOffline';
    const DB_VERSION = 3;
    const STORE_NAME = 'visitas_rascunho';
    const LOCAIS_STORE_NAME = 'locais_rascunho';
    const CEP_CACHE_STORE = 'cep_cache';
    const SECTION = document.getElementById('sync-section');
    if (!SECTION) return;
    const STATUS = document.getElementById('sync-status');
    const LIST = document.getElementById('sync-list');
    const ACTIONS = document.getElementById('sync-actions');
    const SYNC_BTN = document.getElementById('sync-btn');
    const CLEAR_BTN = document.getElementById('sync-clear-btn');
    const RESULT = document.getElementById('sync-result');
    const locaisSection = document.getElementById('sync-locais-section');
    const locaisListEl = document.getElementById('sync-locais-list');
    const locaisStatusEl = document.getElementById('sync-locais-status');

    const syncUrl = SECTION.getAttribute('data-sync-url');
    const locaisSyncUrl = SECTION.getAttribute('data-locais-sync-url') || '';
    const csrfToken = SECTION.getAttribute('data-csrf-token');
    const perfil = SECTION.getAttribute('data-perfil') || 'agente';

    function openDB() {
        return new Promise(function(resolve, reject) {
            const r = indexedDB.open(DB_NAME, DB_VERSION);
            r.onerror = function() { reject(r.error); };
            r.onsuccess = function() { resolve(r.result); };
            r.onupgradeneeded = function(e) {
                var db = e.target.result;
                if (!db.objectStoreNames.contains(STORE_NAME)) {
                    db.createObjectStore(STORE_NAME, { keyPath: 'id' });
                }
                if (!db.objectStoreNames.contains(LOCAIS_STORE_NAME)) {
                    db.createObjectStore(LOCAIS_STORE_NAME, { keyPath: 'id' });
                }
                if (!db.objectStoreNames.contains(CEP_CACHE_STORE)) {
                    db.createObjectStore(CEP_CACHE_STORE, { keyPath: 'cep' });
                }
            };
        });
    }

    function getAllLocalDrafts() {
        return openDB().then(function(db) {
            return new Promise(function(resolve, reject) {
                var t = db.transaction(LOCAIS_STORE_NAME, 'readonly');
                var store = t.objectStore(LOCAIS_STORE_NAME);
                var req = store.getAll();
                req.onsuccess = function() {
                    var all = req.result || [];
                    resolve(all.filter(function(d) { return d.perfil === perfil; }));
                };
                req.onerror = function() { reject(req.error); };
            });
        });
    }

    function removeLocalDrafts(ids) {
        if (!ids.length) return Promise.resolve();
        return openDB().then(function(db) {
            return new Promise(function(resolve, reject) {
                var t = db.transaction(LOCAIS_STORE_NAME, 'readwrite');
                var store = t.objectStore(LOCAIS_STORE_NAME);
                ids.forEach(function(id) { store.delete(id); });
                t.oncomplete = function() { resolve(); };
                t.onerror = function() { reject(t.error); };
            });
        });
    }

    function getAllDrafts() {
        return openDB().then(function(db) {
            return new Promise(function(resolve, reject) {
                var t = db.transaction(STORE_NAME, 'readonly');
                var store = t.objectStore(STORE_NAME);
                var req = store.getAll();
                req.onsuccess = function() {
                    var all = req.result || [];
                    resolve(all.filter(function(d) { return d.perfil === perfil; }));
                };
                req.onerror = function() { reject(req.error); };
            });
        });
    }

    function removeDrafts(ids) {
        return openDB().then(function(db) {
            return new Promise(function(resolve, reject) {
                var t = db.transaction(STORE_NAME, 'readwrite');
                var store = t.objectStore(STORE_NAME);
                ids.forEach(function(id) { store.delete(id); });
                t.oncomplete = function() { resolve(); };
                t.onerror = function() { reject(t.error); };
            });
        });
    }

    function formatDataDdMmAaaa(str) {
        if (!str || typeof str !== 'string') return str || '';
        str = str.trim();
        var m = str.match(/^(\d{4})-(\d{2})-(\d{2})/);
        if (m) return m[3] + '/' + m[2] + '/' + m[1];
        m = str.match(/^(\d{2})\/(\d{2})\/(\d{4})$/);
        if (m) return str;
        return str;
    }

    function formatDraftLabel(draft, index) {
        var p = draft.payload || {};
        var data = formatDataDdMmAaaa(p.vis_data || '');
        var localPart = '';
        if (p.fk_local_id) localPart = ', local ' + p.fk_local_id;
        else if (p.local_draft_id) localPart = ', local a sincronizar';
        var prefix = index != null ? (index + 1) + '. ' : '';
        return prefix + 'Visita em ' + data + localPart;
    }

    function trashIconSvg() {
        var s = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
        s.setAttribute('class', 'w-5 h-5');
        s.setAttribute('fill', 'none');
        s.setAttribute('stroke', 'currentColor');
        s.setAttribute('viewBox', '0 0 24 24');
        s.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>';
        return s;
    }

    var emptyHint = document.getElementById('sync-empty-hint');
    function renderList(drafts) {
        LIST.innerHTML = '';
        if (drafts.length === 0) {
            STATUS.textContent = 'Nenhuma visita pendente de envio.';
            if (emptyHint) emptyHint.classList.remove('hidden');
            ACTIONS.classList.add('hidden');
            return;
        }
        STATUS.textContent = 'Você tem ' + drafts.length + ' visita(s) guardada(s) no dispositivo.';
        if (emptyHint) emptyHint.classList.add('hidden');
        ACTIONS.classList.remove('hidden');
        RESULT.textContent = '';
        drafts.forEach(function(d, i) {
            var row = document.createElement('div');
            row.className = 'flex items-center justify-between gap-2 p-4 rounded-lg bg-gray-100 dark:bg-gray-600 text-sm text-gray-800 dark:text-gray-200';
            row.setAttribute('data-draft-id', d.id);
            var label = document.createElement('span');
            label.className = 'flex-1 min-w-0';
            label.textContent = formatDraftLabel(d, i);
            row.appendChild(label);
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'shrink-0 p-1.5 rounded text-gray-500 hover:text-red-600 hover:bg-red-100 dark:hover:text-red-400 dark:hover:bg-red-900/30 transition';
            btn.setAttribute('title', 'Excluir esta visita do dispositivo');
            btn.setAttribute('aria-label', 'Excluir');
            btn.appendChild(trashIconSvg());
            btn.addEventListener('click', function() {
                var id = d.id;
                removeDrafts([id]).then(function() {
                    return getAllDrafts().then(renderList);
                }).then(function() {
                    if (locaisSection && typeof getAllLocalDrafts === 'function') return getAllLocalDrafts().then(renderLocaisList);
                }).catch(function(err) {
                    RESULT.textContent = 'Erro ao excluir: ' + (err.message || '');
                });
            });
            row.appendChild(btn);
            LIST.appendChild(row);
        });
    }

    function formatLocalLabel(p, index) {
        var parts = [];
        var logr = (p.loc_endereco || '').trim();
        var num = (p.loc_numero || '').trim();
        if (logr) {
            parts.push(num ? logr + ', ' + num : logr);
        } else if (num) {
            parts.push('Nº ' + num);
        }
        var bairro = (p.loc_bairro || '').trim();
        var cidade = (p.loc_cidade || '').trim();
        var uf = (p.loc_estado || '').trim();
        if (bairro) parts.push(bairro);
        if (cidade || uf) {
            parts.push(uf ? (cidade ? cidade + '/' + uf : uf) : cidade);
        }
        var line = parts.join(', ');
        if (!line) line = 'Local sem endereço informado';
        return (index + 1) + '. ' + line;
    }

    function renderLocaisList(locais) {
        if (!locaisStatusEl || !locaisListEl) return;
        locaisListEl.innerHTML = '';
        if (!locais || locais.length === 0) {
            locaisStatusEl.textContent = 'Nenhum local pendente de envio.';
            return;
        }
        locaisStatusEl.textContent = 'Você tem ' + locais.length + ' local(is) guardado(s) no dispositivo.';
        locais.forEach(function(d, i) {
            var p = d.payload || {};
            var row = document.createElement('div');
            row.className = 'flex items-center justify-between gap-2 p-3 rounded-lg bg-gray-100 dark:bg-gray-600 text-sm text-gray-800 dark:text-gray-200';
            row.setAttribute('data-draft-id', d.id);
            var label = document.createElement('span');
            label.className = 'flex-1 min-w-0';
            label.textContent = formatLocalLabel(p, i);
            row.appendChild(label);
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'shrink-0 p-1.5 rounded text-gray-500 hover:text-red-600 hover:bg-red-100 dark:hover:text-red-400 dark:hover:bg-red-900/30 transition';
            btn.setAttribute('title', 'Excluir este local do dispositivo');
            btn.setAttribute('aria-label', 'Excluir');
            btn.appendChild(trashIconSvg());
            btn.addEventListener('click', function() {
                var id = d.id;
                removeLocalDrafts([id]).then(function() {
                    return getAllLocalDrafts().then(renderLocaisList);
                }).catch(function(err) {
                    if (RESULT) RESULT.textContent = 'Erro ao excluir: ' + (err.message || '');
                });
            });
            row.appendChild(btn);
            locaisListEl.appendChild(row);
        });
    }

    function doSync() {
        var locaisSyncUrlVal = locaisSyncUrl;
        SYNC_BTN.disabled = true;
        RESULT.textContent = 'Enviando…';

        // Sempre enviar locais primeiro quando houver pendentes; só depois as visitas (que podem depender do local criado).
        function runSyncLocaisThenVisitas() {
            if (!locaisSyncUrlVal || typeof getAllLocalDrafts !== 'function') {
                return Promise.resolve({ syncedLocalIds: [], draftIdToLocId: {}, errosLocais: [] });
            }
            return getAllLocalDrafts().then(function(localDrafts) {
                if (localDrafts.length === 0) return Promise.resolve({ syncedLocalIds: [], draftIdToLocId: {}, errosLocais: [] });
                var body = JSON.stringify({ locais: localDrafts.map(function(d) { return d.payload; }) });
                return fetch(locaisSyncUrlVal, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' },
                    body: body
                }).then(function(r) {
                    return r.text().then(function(text) {
                        if (!r.ok) throw new Error(text && text.trim().startsWith('{') ? (JSON.parse(text).message || 'Erro ' + r.status) : 'Erro ' + r.status);
                        return JSON.parse(text);
                    });
                }).then(function(data) {
                    var ids = data.ids || [];
                    var errosLocais = data.erros || [];
                    var draftIdToLocId = {};
                    localDrafts.forEach(function(d, i) {
                        if (ids[i] != null) draftIdToLocId[d.id] = ids[i];
                    });
                    var syncedLocalIds = localDrafts.filter(function(d, i) { return ids[i] != null; }).map(function(d) { return d.id; });
                    if (errosLocais.length > 0 && syncedLocalIds.length === 0) {
                        var msg = errosLocais[0].message || 'Erro ao validar local.';
                        RESULT.textContent = 'Local não enviado: ' + msg;
                        SYNC_BTN.disabled = false;
                        throw new Error(msg);
                    }
                    return { syncedLocalIds: syncedLocalIds, draftIdToLocId: draftIdToLocId, errosLocais: errosLocais };
                }).catch(function(err) {
                    RESULT.textContent = 'Erro ao sincronizar locais: ' + (err.message || '');
                    throw err;
                });
            });
        }

        runSyncLocaisThenVisitas().then(function(result) {
            var draftIdToLocId = result.draftIdToLocId || {};
            var syncedLocalIds = result.syncedLocalIds || [];
            return removeLocalDrafts(syncedLocalIds).then(function() {
                return getAllDrafts();
            }).then(function(drafts) {
                return { drafts: drafts, draftIdToLocId: draftIdToLocId, errosLocais: result.errosLocais || [] };
            });
        }).then(function(data) {
            // Locais já foram enviados acima; agora enviamos apenas as visitas (com fk_local_id já resolvido).
            var drafts = data.drafts;
            var draftIdToLocId = data.draftIdToLocId || {};
            var errosLocaisSync = data.errosLocais || [];
            var toSend = [];
            var duplicateIds = [];
            var seen = {};
            var visitasSemLocal = 0;
            drafts.forEach(function(d) {
                var payload = d.payload || {};
                var fk = payload.fk_local_id;
                if (payload.local_draft_id) {
                    if (draftIdToLocId[payload.local_draft_id] != null) {
                        payload = Object.assign({}, payload);
                        payload.fk_local_id = draftIdToLocId[payload.local_draft_id];
                        delete payload.local_draft_id;
                    } else {
                        visitasSemLocal++;
                        return;
                    }
                }
                var localId = (fk != null) ? String(fk) : '';
                var dataStr = payload.vis_data ? String(payload.vis_data).trim() : '';
                var key = localId + '|' + dataStr;
                if (!seen[key]) {
                    seen[key] = true;
                    toSend.push({ id: d.id, payload: payload });
                } else {
                    duplicateIds.push(d.id);
                }
            });

            if (toSend.length === 0) {
                renderList(drafts);
                if (locaisSection && getAllLocalDrafts) getAllLocalDrafts().then(renderLocaisList);
                var msg = 'Nada a enviar.';
                if (visitasSemLocal > 0) msg = visitasSemLocal + ' visita(s) dependem de local que não foi enviado. Envie o local primeiro (verifique os dados do local).';
                else if (drafts.length === 0) msg = 'Nada a enviar.';
                RESULT.textContent = msg;
                SYNC_BTN.disabled = !navigator.onLine;
                if (window.VisitaOfflineUpdateBanner) window.VisitaOfflineUpdateBanner();
                return;
            }

            var syncedIds = [];
            var errors = [];
            var index = 0;

            function sendNext() {
                if (index >= toSend.length) {
                    return Promise.resolve();
                }
                var d = toSend[index];
                var body = JSON.stringify({ visitas: [d.payload] });
                return fetch(syncUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: body
                })
                .then(function(r) {
                    if (r.status === 419) throw new Error('Sessão expirada. Recarregue a página e tente novamente.');
                    if (!r.ok) {
                        return r.text().then(function(text) {
                            var msg = 'Erro no servidor (' + r.status + '). Tente novamente ou verifique os dados.';
                            if (text && text.trim().startsWith('{')) {
                                try {
                                    var data = JSON.parse(text);
                                    if (data.erros && data.erros[0] && data.erros[0].message) msg = data.erros[0].message;
                                } catch(e) {}
                            }
                            throw new Error(msg);
                        });
                    }
                    return r.text().then(function(text) {
                        if (!text || !text.trim().startsWith('{')) throw new Error('Resposta inválida do servidor.');
                        return JSON.parse(text);
                    });
                })
                .then(function(data) {
                    if ((data.sincronizados || 0) > 0) {
                        syncedIds.push(d.id);
                    }
                    if ((data.erros || []).length > 0) {
                        errors.push({ index: index, message: data.erros[0].message || 'Erro' });
                    }
                    index++;
                    return sendNext();
                })
                .catch(function(err) {
                    errors.push({ index: index, message: err.message || 'Falha na rede' });
                    index++;
                    return sendNext();
                });
            }

            sendNext()
            .then(function() {
                var idsToRemove = syncedIds.slice();
                duplicateIds.forEach(function(id) { idsToRemove.push(id); });
                return removeDrafts(idsToRemove);
            })
            .then(function() {
                return getAllDrafts();
            })
            .then(function(remaining) {
                renderList(remaining);
                if (locaisSection && getAllLocalDrafts) getAllLocalDrafts().then(renderLocaisList);
                if (syncedIds.length > 0) {
                    RESULT.textContent = 'Os dados foram sincronizados.';
                    if (errors.length > 0 || (typeof errosLocaisSync !== 'undefined' && errosLocaisSync.length > 0)) {
                        RESULT.textContent += ' Alguns itens não puderam ser enviados (verifique os dados).';
                    }
                } else {
                    RESULT.textContent = errors.length > 0 ? (errors[0].message || 'Erro ao sincronizar.') : 'Nenhuma visita foi enviada.';
                    if (typeof errosLocaisSync !== 'undefined' && errosLocaisSync.length > 0) {
                        RESULT.textContent += (RESULT.textContent ? ' ' : '') + 'Local não enviado: ' + (errosLocaisSync[0].message || 'verifique os dados.');
                    }
                }
                SYNC_BTN.disabled = !navigator.onLine;
                if (typeof window.VisitaOfflineUpdateBanner === 'function') {
                    window.VisitaOfflineUpdateBanner();
                }
            })
            .catch(function(err) {
                RESULT.textContent = 'Falha: ' + (err.message || 'sem conexão ou servidor indisponível.');
                SYNC_BTN.disabled = !navigator.onLine;
            });
        }).catch(function(err) {
            RESULT.textContent = 'Falha: ' + (err.message || '');
            SYNC_BTN.disabled = !navigator.onLine;
        });
    }

    SYNC_BTN.addEventListener('click', doSync);

    CLEAR_BTN.addEventListener('click', function() {
        getAllDrafts().then(function(drafts) {
            if (drafts.length === 0) {
                RESULT.textContent = 'Nenhuma visita guardada para apagar.';
                return;
            }
            if (!confirm('Apagar as ' + drafts.length + ' visita(s) guardada(s) no dispositivo? Elas não poderão ser recuperadas.')) {
                return;
            }
            var ids = drafts.map(function(d) { return d.id; });
            CLEAR_BTN.disabled = true;
            RESULT.textContent = 'Apagando…';
            removeDrafts(ids)
                .then(function() {
                    RESULT.textContent = 'Dados apagados do dispositivo.';
                    renderList([]);
                    if (typeof window.VisitaOfflineUpdateBanner === 'function') {
                        window.VisitaOfflineUpdateBanner();
                    }
                })
                .catch(function() {
                    RESULT.textContent = 'Erro ao apagar. Tente novamente.';
                })
                .finally(function() {
                    CLEAR_BTN.disabled = false;
                });
        });
    });

    function updateOfflineWarning() {
        var offlineEl = document.getElementById('sync-offline-warning');
        if (offlineEl) {
            if (navigator.onLine) {
                offlineEl.classList.add('hidden');
                SYNC_BTN.disabled = false;
            } else {
                offlineEl.classList.remove('hidden');
                SYNC_BTN.disabled = true;
            }
        }
    }
    window.addEventListener('online', updateOfflineWarning);
    window.addEventListener('offline', updateOfflineWarning);
    updateOfflineWarning();

    getAllDrafts().then(renderList).catch(function() {
        STATUS.textContent = 'Não foi possível carregar as visitas salvas. Verifique se o navegador permite armazenamento local.';
    });
    if (locaisSection && locaisListEl && locaisStatusEl) {
        getAllLocalDrafts().then(renderLocaisList).catch(function() {
            locaisStatusEl.textContent = 'Não foi possível carregar os locais.';
        });
    }
})();
</script>
@endsection
