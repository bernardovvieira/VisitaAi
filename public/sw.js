/**
 * Service Worker: Visita Aí
 * Permite uso offline das telas de visitas e locais (após carregá-las uma vez com internet).
 * Estratégia: network-first; em caso de falha, usa cache (exato ou por pathname).
 */
const CACHE_NAME = 'visitaai-v4';

var CACHEABLE_PATHS = [
    '/', '/agente/dashboard', '/saude/dashboard',
    '/agente/visitas', '/agente/visitas/create', '/agente/visitas-sync',
    '/saude/visitas', '/saude/visitas/create', '/saude/visitas-sync',
    '/agente/locais', '/agente/locais/create'
];

function pathMatches(a, b) {
    var pa = (a || '').replace(/\/$/, '') || '/';
    var pb = (b || '').replace(/\/$/, '') || '/';
    return pa === pb;
}

function isCacheablePath(pathname) {
    if (pathname.indexOf('/build/') === 0) return true;
    if (pathname.indexOf('/images/') === 0) return true;
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
                    if (pathname.indexOf('/images/') === 0) {
                        return matchCacheByPathname(cache, pathname).then(function (r) {
                            return r || new Response('Sem conexão.', { status: 503, statusText: 'Service Unavailable' });
                        });
                    }
                    if (isAppPagePath(pathname)) {
                        return matchCacheByPathname(cache, pathname).then(function (r) {
                            if (r) return r;
                            if (pathname.indexOf('/visitas/create') !== -1) {
                                var indexPath = pathname.indexOf('/agente/') === 0 ? '/agente/visitas' : '/saude/visitas';
                                var html = '<!DOCTYPE html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Offline</title></head><body style="font-family:sans-serif;max-width:420px;margin:2rem auto;padding:1.5rem;text-align:center;background:#fef3c7;color:#92400e;border-radius:8px;"><p style="margin:0 0 1rem;">Esta p&aacute;gina (Registrar visita) ainda n&atilde;o foi guardada para uso offline.</p><p style="margin:0 0 1rem;font-size:0.9em;">Abra <strong>Registrar visita</strong> uma vez com internet para poder us&aacute;-la sem conex&atilde;o.</p><a href="' + indexPath + '" style="display:inline-block;padding:0.5rem 1rem;background:#d97706;color:#fff;text-decoration:none;border-radius:6px;font-weight:600;">Voltar para Visitas</a></body></html>';
                                return new Response(html, { status: 503, statusText: 'Service Unavailable', headers: { 'Content-Type': 'text/html; charset=utf-8' } });
                            }
                            if (pathname.indexOf('/agente/') === 0 && pathname.indexOf('/locais/create') !== -1) {
                                var html = '<!DOCTYPE html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Offline</title></head><body style="font-family:sans-serif;max-width:420px;margin:2rem auto;padding:1.5rem;text-align:center;background:#fef3c7;color:#92400e;border-radius:8px;"><p style="margin:0 0 1rem;">Esta p&aacute;gina (Adicionar local) ainda n&atilde;o foi guardada para uso offline.</p><p style="margin:0 0 1rem;font-size:0.9em;">Abra <strong>Cadastrar local</strong> uma vez com internet para poder us&aacute;-la sem conex&atilde;o.</p><a href="/agente/locais" style="display:inline-block;padding:0.5rem 1rem;background:#d97706;color:#fff;text-decoration:none;border-radius:6px;font-weight:600;">Voltar para Locais</a></body></html>';
                                return new Response(html, { status: 503, statusText: 'Service Unavailable', headers: { 'Content-Type': 'text/html; charset=utf-8' } });
                            }
                            if (pathname.replace(/\/$/, '') === '/agente/locais') {
                                var html = '<!DOCTYPE html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Offline</title></head><body style="font-family:sans-serif;max-width:420px;margin:2rem auto;padding:1.5rem;text-align:center;background:#fef3c7;color:#92400e;border-radius:8px;"><p style="margin:0 0 1rem;">Esta p&aacute;gina (Locais) ainda n&atilde;o foi guardada para uso offline.</p><p style="margin:0 0 1rem;font-size:0.9em;">Abra <strong>Locais</strong> uma vez com internet para poder us&aacute;-la sem conex&atilde;o.</p><a href="/agente/visitas" style="display:inline-block;padding:0.5rem 1rem;background:#d97706;color:#fff;text-decoration:none;border-radius:6px;font-weight:600;">Ir para Visitas</a></body></html>';
                                return new Response(html, { status: 503, statusText: 'Service Unavailable', headers: { 'Content-Type': 'text/html; charset=utf-8' } });
                            }
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
