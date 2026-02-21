/**
 * Service Worker – Visita Aí
 * Permite uso offline das telas de visitas (após carregá-las uma vez com internet).
 * Estratégia: network-first; em caso de falha, usa cache (exato ou por pathname).
 */
const CACHE_NAME = 'visitaai-v2';

var CACHEABLE_PATHS = [
    '/', '/agente/dashboard', '/saude/dashboard',
    '/agente/visitas', '/agente/visitas/create', '/agente/visitas-sync',
    '/saude/visitas', '/saude/visitas/create', '/saude/visitas-sync'
];

function pathMatches(a, b) {
    var pa = (a || '').replace(/\/$/, '') || '/';
    var pb = (b || '').replace(/\/$/, '') || '/';
    return pa === pb;
}

function isCacheablePath(pathname) {
    if (pathname.indexOf('/build/') === 0) return true;
    return CACHEABLE_PATHS.some(function (p) { return pathMatches(pathname, p); });
}

function isAppPagePath(pathname) {
    return pathname.indexOf('/agente/') === 0 || pathname.indexOf('/saude/') === 0;
}

/** Retorna uma resposta em cache cujo pathname coincide (permite trailing slash). */
function matchCacheByPathname(cache, pathname) {
    return cache.keys().then(function (requests) {
        var target = (pathname || '').replace(/\/$/, '') || '/';
        for (var i = 0; i < requests.length; i++) {
            var u = requests[i].url;
            var p = (new URL(u)).pathname.replace(/\/$/, '') || '/';
            if (p === target) return cache.match(requests[i]);
        }
        return undefined;
    });
}

/** Retorna qualquer documento em cache para /agente/ ou /saude/ (fallback para página com estilo). */
function matchAnyAppPage(cache) {
    return cache.keys().then(function (requests) {
        for (var i = 0; i < requests.length; i++) {
            var req = requests[i];
            var p = (new URL(req.url)).pathname;
            if (p.indexOf('/agente/') === 0 || p.indexOf('/saude/') === 0) {
                if (CACHEABLE_PATHS.some(function (allowed) { return pathMatches(p, allowed); })) {
                    return cache.match(req);
                }
            }
        }
        return cache.match('/');
    });
}

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
            var pathname = url.pathname;
            if (response.status === 200 && !response.redirected && isCacheablePath(pathname)) {
                caches.open(CACHE_NAME).then(function (cache) {
                    cache.put(event.request, clone);
                });
            }
            return response;
        }).catch(function () {
            var pathname = url.pathname;
            return caches.open(CACHE_NAME).then(function (cache) {
                return cache.match(event.request).then(function (cached) {
                    if (cached) return cached;
                    if (pathname.indexOf('/build/') === 0) {
                        return matchCacheByPathname(cache, pathname).then(function (r) {
                            return r || new Response('Sem conexão.', { status: 503, statusText: 'Service Unavailable' });
                        });
                    }
                    if (isAppPagePath(pathname)) {
                        return matchCacheByPathname(cache, pathname).then(function (r) {
                            if (r) return r;
                            return matchAnyAppPage(cache).then(function (r) {
                                return r || new Response('Sem conexão.', { status: 503, statusText: 'Service Unavailable' });
                            });
                        });
                    }
                    return new Response('Sem conexão.', { status: 503, statusText: 'Service Unavailable' });
                });
            });
        })
    );
});
