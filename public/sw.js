const CACHE_VERSION = 'opesbooks-v1';
const STATIC_CACHE  = `${CACHE_VERSION}-static`;
const DYNAMIC_CACHE = `${CACHE_VERSION}-dynamic`;
const OFFLINE_PAGE  = '/offline';

const STATIC_ASSETS = ['/app', '/login', '/offline', '/manifest.json', '/icon.svg'];

self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(STATIC_CACHE)
      .then(cache => cache.addAll(STATIC_ASSETS))
      .then(() => self.skipWaiting())
  );
});

self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(keys =>
      Promise.all(keys
        .filter(k => k.startsWith('opesbooks-') && k !== STATIC_CACHE && k !== DYNAMIC_CACHE)
        .map(k => caches.delete(k)))
    ).then(() => self.clients.claim())
  );
});

self.addEventListener('fetch', event => {
  const { request } = event;
  const url = new URL(request.url);

  if (request.method !== 'GET' || url.origin !== location.origin) return;
  if (url.pathname.includes('livewire')) return;

  // API: network-first, cache successful GETs for offline reads.
  if (url.pathname.startsWith('/api/')) {
    event.respondWith(
      fetch(request)
        .then(res => {
          if (res.ok) { const c = res.clone(); caches.open(DYNAMIC_CACHE).then(cache => cache.put(request, c)); }
          return res;
        })
        .catch(() => caches.match(request))
    );
    return;
  }

  // Pages: network-first, cache fallback, then offline page.
  event.respondWith(
    fetch(request)
      .then(res => { const c = res.clone(); caches.open(DYNAMIC_CACHE).then(cache => cache.put(request, c)); return res; })
      .catch(() => caches.match(request).then(cached => cached || caches.match(OFFLINE_PAGE)))
  );
});
