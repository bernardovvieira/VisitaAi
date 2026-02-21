@extends('layouts.app')

@section('og_title', config('app.name') . ' — Sincronizar visitas')
@section('og_description', 'Envie as visitas salvas no dispositivo quando estiver sem internet.')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <x-breadcrumbs :items="[['label' => 'Página Inicial', 'url' => route('dashboard')], ['label' => 'Visitas', 'url' => $visitasIndexRoute], ['label' => 'Sincronizar']]" />
    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Sincronizar visitas</h1>

    <section class="p-6 bg-white dark:bg-gray-700 rounded-lg shadow space-y-4">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Para que serve</h2>
        <p class="text-gray-600 dark:text-gray-400">
            Use esta tela <strong>quando tiver internet</strong> para enviar as visitas que você salvou no celular ou computador sem conexão.
            As visitas ficam guardadas no seu aparelho até você clicar em <strong>Sincronizar agora</strong>.
        </p>
        <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mt-4">Passo a passo</h3>
        <ol class="list-decimal list-inside text-gray-600 dark:text-gray-400 space-y-2">
            <li><strong>Com internet (na unidade):</strong> Abra o sistema e visite esta tela e a de «Registrar visita» pelo menos uma vez — assim elas funcionam depois sem rede.</li>
            <li><strong>Sem internet (em campo):</strong> Preencha a visita e clique em <strong>Salvar para enviar depois</strong> (no formulário). Nada é enviado ainda.</li>
            <li><strong>De volta com internet:</strong> Um aviso amarelo aparecerá no topo da tela. Abra o menu <strong>Sincronizar</strong> e clique em <strong>Sincronizar agora</strong> para enviar todas.</li>
        </ol>
    </section>

    <section class="p-6 bg-white dark:bg-gray-700 rounded-lg shadow space-y-4"
             id="sync-section"
             data-sync-url="{{ $syncSubmitUrl }}"
             data-csrf-token="{{ csrf_token() }}"
             data-perfil="{{ $perfil }}">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Visitas pendentes de envio</h2>
        <p class="text-sm text-gray-600 dark:text-gray-400" id="sync-status">
            Carregando…
        </p>
        <p class="text-xs text-gray-500 dark:text-gray-500 mt-1" id="sync-empty-hint">
            As visitas que você salvar com «Salvar para enviar depois» (na tela de registrar visita) aparecerão aqui.
        </p>
        <div id="sync-list" class="space-y-2">
            <!-- Preenchido via JS a partir do IndexedDB -->
        </div>
        <p id="sync-offline-warning" class="hidden text-sm text-amber-700 dark:text-amber-300 bg-amber-100 dark:bg-amber-900/30 px-3 py-2 rounded">
            Você está sem internet. Conecte-se para poder sincronizar.
        </p>
        <div id="sync-actions" class="hidden flex flex-wrap gap-3 items-center">
            <button type="button" id="sync-btn"
                    class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg shadow transition disabled:opacity-50 disabled:cursor-not-allowed">
                Sincronizar agora
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
        STATUS.textContent = 'Você tem ' + drafts.length + ' visita(s) salva(s) no dispositivo para transmitir.';
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
                    RESULT.textContent = syncedIds.length + ' visita(s) transmitida(s) com sucesso.';
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
