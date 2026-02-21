/**
 * Service Worker – Visita Aí
 * Permite uso offline das telas de visitas (após carregá-las uma vez com internet).
 * Estratégia: network-first para navegação; em caso de falha, usa cache.
 */
const CACHE_NAME = 'visitaai-v1';

self.addEventListener('install', function (event) {
    event.waitUntil(
        caches.open(CACHE_NAME).then(function () {
            return self.skipWaiting();
        })
    );
});

self.addEventListener('activate', function (event) {
    event.waitUntil(
        caches.keys().then(function (keys) {
            return Promise.all(
                keys.filter(function (key) { return key !== CACHE_NAME; }).map(function (key) {
                    return caches.delete(key);
                })
            );
        }).then(function () {
            return self.clients.claim();
        })
    );
});

self.addEventListener('fetch', function (event) {
    var url = new URL(event.request.url);
    if (event.request.method !== 'GET') return;
    if (url.origin !== self.location.origin) return;

    event.respondWith(
        fetch(event.request).then(function (response) {
            var clone = response.clone();
            if (response.status === 200 && !response.redirected && isCacheable(url)) {
                caches.open(CACHE_NAME).then(function (cache) {
                    cache.put(event.request, clone);
                });
            }
            return response;
        }).catch(function () {
            return caches.match(event.request).then(function (cached) {
                if (cached) return cached;
                if (url.pathname.indexOf('/agente/') === 0 || url.pathname.indexOf('/saude/') === 0) {
                    return caches.match('/').then(function (r) { return r || new Response('Sem conexão.', { status: 503, statusText: 'Service Unavailable' }); });
                }
                return new Response('Sem conexão.', { status: 503, statusText: 'Service Unavailable' });
            });
        })
    );
});

function isCacheable(url) {
    var p = url.pathname;
    if (p.indexOf('/build/') === 0) return true;
    if (p === '/' || p === '/agente/dashboard' || p === '/saude/dashboard') return true;
    if (p === '/agente/visitas' || p === '/agente/visitas/create' || p === '/agente/visitas-sync') return true;
    if (p === '/saude/visitas' || p === '/saude/visitas/create' || p === '/saude/visitas-sync') return true;
    return false;
}
