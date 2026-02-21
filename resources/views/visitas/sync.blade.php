@extends('layouts.app')

@section('og_title', config('app.name') . ' — Sincronizar visitas')
@section('og_description', 'Envie as visitas salvas no dispositivo quando estiver sem internet.')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <x-breadcrumbs :items="[['label' => 'Página Inicial', 'url' => route('dashboard')], ['label' => 'Visitas', 'url' => $visitasIndexRoute], ['label' => 'Enviar visitas salvas']]" />
    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Enviar visitas salvas no dispositivo</h1>

    <section class="p-6 bg-white dark:bg-gray-700 rounded-lg shadow space-y-4">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">O que é esta tela?</h2>
        <p class="text-gray-600 dark:text-gray-400">
            Quando você está <strong>sem internet</strong> (em campo), pode guardar a visita no dispositivo e enviar depois.
            Esta tela serve para <strong>enviar</strong> essas visitas guardadas quando você estiver com conexão.
        </p>
        <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mt-4">Como usar</h3>
        <ol class="list-decimal list-inside text-gray-600 dark:text-gray-400 space-y-2">
            <li><strong>Na unidade (com internet):</strong> Abra o sistema e entre em «Visitas» e em «Registrar nova visita» pelo menos uma vez. Assim o dispositivo guarda a tela para usar sem rede.</li>
            <li><strong>Em campo (sem internet):</strong> Preencha a visita e use o botão <strong>«Guardar no dispositivo para enviar depois»</strong>. A visita fica só no seu dispositivo.</li>
            <li><strong>Quando tiver internet de novo:</strong> Entre em «Visitas» e clique em <strong>«Enviar visitas salvas no dispositivo»</strong>. Aqui você verá a lista e o botão para enviar tudo.</li>
        </ol>
    </section>

    <section class="p-6 bg-white dark:bg-gray-700 rounded-lg shadow space-y-4"
             id="sync-section"
             data-sync-url="{{ $syncSubmitUrl }}"
             data-csrf-token="{{ csrf_token() }}"
             data-perfil="{{ $perfil }}">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Visitas guardadas no aparelho</h2>
        <p class="text-sm text-gray-600 dark:text-gray-400" id="sync-status">
            Carregando…
        </p>
        <p class="text-xs text-gray-500 dark:text-gray-500 mt-1" id="sync-empty-hint">
            Quando você usar «Guardar no dispositivo para enviar depois» na tela de registrar visita, as visitas aparecerão aqui para você enviar.
        </p>
        <div id="sync-list" class="space-y-2">
            <!-- Preenchido via JS a partir do IndexedDB -->
        </div>
        <p id="sync-offline-warning" class="hidden text-sm text-amber-700 dark:text-amber-300 bg-amber-100 dark:bg-amber-900/30 px-3 py-2 rounded">
            Você está sem internet. Conecte o dispositivo à internet (Wi‑Fi ou dados) para poder enviar as visitas.
        </p>
        <div id="sync-actions" class="hidden flex flex-wrap gap-3 items-center">
            <button type="button" id="sync-btn"
                    class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg shadow transition disabled:opacity-50 disabled:cursor-not-allowed">
                Enviar todas agora
            </button>
            <span class="text-sm text-gray-500 dark:text-gray-400" id="sync-result"></span>
        </div>
        <div class="flex flex-wrap gap-3 mt-4">
            <a href="{{ $visitasIndexRoute }}" class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-semibold text-sm rounded-lg shadow transition">
                Voltar para Visitas
            </a>
            <a href="{{ $visitasCreateRoute }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-lg shadow transition">
                Registrar nova visita
            </a>
        </div>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
            Depois de enviar, as visitas saem da lista e passam a aparecer na sua lista de visitas normalmente.
        </p>
    </section>
</div>

<script>
(function() {
    const DB_NAME = 'VisitaAiOffline';
    const STORE_NAME = 'visitas_rascunho';
    const SECTION = document.getElementById('sync-section');
    if (!SECTION) return;
    const STATUS = document.getElementById('sync-status');
    const LIST = document.getElementById('sync-list');
    const ACTIONS = document.getElementById('sync-actions');
    const SYNC_BTN = document.getElementById('sync-btn');
    const RESULT = document.getElementById('sync-result');

    const syncUrl = SECTION.getAttribute('data-sync-url');
    const csrfToken = SECTION.getAttribute('data-csrf-token');
    const perfil = SECTION.getAttribute('data-perfil') || 'agente';

    function openDB() {
        return new Promise(function(resolve, reject) {
            const r = indexedDB.open(DB_NAME, 1);
            r.onerror = function() { reject(r.error); };
            r.onsuccess = function() { resolve(r.result); };
            r.onupgradeneeded = function(e) {
                var db = e.target.result;
                if (!db.objectStoreNames.contains(STORE_NAME)) {
                    db.createObjectStore(STORE_NAME, { keyPath: 'id' });
                }
            };
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

    function formatDraftLabel(draft) {
        var p = draft.payload || {};
        var data = p.vis_data || '';
        var localId = p.fk_local_id || '';
        return 'Visita em ' + data + ' (local ID ' + localId + ')';
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
        STATUS.textContent = 'Você tem ' + drafts.length + ' visita(s) guardada(s) no dispositivo. Clique no botão abaixo para enviar.';
        if (emptyHint) emptyHint.classList.add('hidden');
        ACTIONS.classList.remove('hidden');
        RESULT.textContent = '';
        drafts.forEach(function(d) {
            var div = document.createElement('div');
            div.className = 'p-3 bg-gray-100 dark:bg-gray-600 rounded text-sm text-gray-800 dark:text-gray-200';
            div.textContent = formatDraftLabel(d);
            div.setAttribute('data-draft-id', d.id);
            LIST.appendChild(div);
        });
    }

    function doSync() {
        getAllDrafts().then(function(drafts) {
            if (drafts.length === 0) {
                renderList([]);
                return;
            }
            SYNC_BTN.disabled = true;
            RESULT.textContent = 'Enviando…';

            var syncedIds = [];
            var errors = [];
            var index = 0;

            function sendNext() {
                if (index >= drafts.length) {
                    return Promise.resolve();
                }
                var d = drafts[index];
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
                    if (!r.ok) throw new Error('Rede: ' + r.status);
                    return r.json();
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
                return removeDrafts(syncedIds);
            })
            .then(function() {
                if (syncedIds.length > 0) {
                    RESULT.textContent = syncedIds.length + ' visita(s) enviada(s) com sucesso.';
                    if (errors.length > 0) {
                        RESULT.textContent += ' ' + errors.length + ' não enviada(s) (verifique os dados).';
                    }
                } else {
                    RESULT.textContent = errors.length > 0 ? (errors[0].message || 'Erro ao sincronizar.') : 'Nenhuma visita foi enviada.';
                }
                return getAllDrafts();
            })
            .then(function(remaining) {
                renderList(remaining);
                SYNC_BTN.disabled = !navigator.onLine;
                if (typeof window.VisitaOfflineUpdateBanner === 'function') {
                    window.VisitaOfflineUpdateBanner();
                }
            })
            .catch(function(err) {
                RESULT.textContent = 'Falha: ' + (err.message || 'sem conexão ou servidor indisponível.');
                SYNC_BTN.disabled = !navigator.onLine;
            });
        });
    }

    SYNC_BTN.addEventListener('click', doSync);

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
})();
</script>
@endsection
