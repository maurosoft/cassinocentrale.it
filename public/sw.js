// Service worker del B&B Cassino Centrale (PWA di base).
// Strategia: rete-prima per le pagine, con fallback alla pagina "sei offline".
// Cambiare il numero di versione quando si aggiornano le regole di cache.
const CACHE_VERSION = 'cassino-v1';
const OFFLINE_URL = '/offline';
const PRECACHE_URLS = [OFFLINE_URL, '/manifest.json', '/icons/icon.svg'];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_VERSION).then((cache) => cache.addAll(PRECACHE_URLS)),
    );
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) =>
            Promise.all(keys.filter((key) => key !== CACHE_VERSION).map((key) => caches.delete(key))),
        ),
    );
    self.clients.claim();
});

self.addEventListener('fetch', (event) => {
    const { request } = event;

    // Gestiamo solo le richieste GET dello stesso sito.
    if (request.method !== 'GET' || new URL(request.url).origin !== self.location.origin) {
        return;
    }

    // Navigazione tra pagine: prova la rete, se offline mostra la pagina di cortesia.
    if (request.mode === 'navigate') {
        event.respondWith(
            fetch(request).catch(() => caches.match(OFFLINE_URL)),
        );
        return;
    }

    // Risorse statiche (css, js, immagini, font): cache-prima, poi rete.
    event.respondWith(
        caches.match(request).then((cached) => {
            if (cached) {
                return cached;
            }
            return fetch(request).then((response) => {
                if (response.ok && response.type === 'basic') {
                    const copy = response.clone();
                    caches.open(CACHE_VERSION).then((cache) => cache.put(request, copy));
                }
                return response;
            });
        }),
    );
});

// Ricezione delle notifiche push (predisposto per la Fase 7 / VAPID).
self.addEventListener('push', (event) => {
    if (!event.data) {
        return;
    }
    let payload = { title: 'B&B Cassino Centrale', body: '', url: '/' };
    try {
        payload = { ...payload, ...event.data.json() };
    } catch (e) {
        payload.body = event.data.text();
    }
    event.waitUntil(
        self.registration.showNotification(payload.title, {
            body: payload.body,
            icon: '/icons/icon.svg',
            badge: '/icons/icon.svg',
            data: { url: payload.url },
        }),
    );
});

self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    const url = (event.notification.data && event.notification.data.url) || '/';
    event.waitUntil(clients.openWindow(url));
});
