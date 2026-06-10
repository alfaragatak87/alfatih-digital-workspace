const CACHE_NAME = 'alfatih-ws-v5.0';

// Daftar file statis yang akan disimpan di memori cache browser
const urlsToCache = [
    './',
    './index.php',
    './assets/css/dashboard.css',
    './assets/css/portfolio.css',
    './assets/js/app.js',
    './assets/images/LOGO_GAWE.svg'
];

// ── 1. INSTALL SERVICE WORKER ──
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => {
                console.log('Cache dibuka');
                return cache.addAll(urlsToCache);
            })
    );
    self.skipWaiting();
});

// ── 2. ACTIVATE & BERSIHKAN CACHE LAMA ──
self.addEventListener('activate', event => {
    const cacheWhitelist = [CACHE_NAME];
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.map(cacheName => {
                    if (cacheWhitelist.indexOf(cacheName) === -1) {
                        return caches.delete(cacheName); // Hapus cache versi lama
                    }
                })
            );
        })
    );
    self.clients.claim();
});

// ── 3. FETCH (STRATEGI: NETWORK FIRST, FALLBACK TO CACHE) ──
// Karena ini aplikasi CMS/File Manager yang dinamis, kita utamakan 
// mengambil data terbaru dari server (Network). Jika server mati/offline,
// baru kita ambil tampilan dari Cache.
self.addEventListener('fetch', event => {
    // Hanya proses request GET, abaikan POST (seperti form upload/login)
    if (event.request.method !== 'GET') return;

    event.respondWith(
        fetch(event.request)
            .then(response => {
                // Pastikan response valid
                if (!response || response.status !== 200 || response.type !== 'basic') {
                    return response;
                }

                // Simpan salinan response terbaru ke cache
                const responseToCache = response.clone();
                caches.open(CACHE_NAME)
                    .then(cache => {
                        cache.put(event.request, responseToCache);
                    });

                return response;
            })
            .catch(() => {
                // Jika jaringan gagal (Offline), ambil dari Cache
                return caches.match(event.request);
            })
    );
});