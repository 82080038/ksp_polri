// Service Worker for KSP POLRI PWA
const CACHE_NAME = 'ksp-polri-v1.0.0';
const STATIC_CACHE = 'ksp-polri-static-v1.0.0';
const DYNAMIC_CACHE = 'ksp-polri-dynamic-v1.0.0';

// Resources to cache immediately
const STATIC_ASSETS = [
  '/',
  '/ksp_polri/frontend/pages/dashboard_anggota.html',
  '/ksp_polri/frontend/pages/dashboard_pengurus.html',
  '/ksp_polri/frontend/assets/css/bootstrap-custom.css',
  '/ksp_polri/frontend/assets/css/style.css',
  '/ksp_polri/frontend/assets/js/jquery-3.6.0.min.js',
  'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js',
  'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css',
  '/ksp_polri/frontend/manifest.json'
];

// API endpoints to cache for offline viewing (with short TTL)
const API_ENDPOINTS = [
  'dashboard/stats',
  'dashboard/myData',
  'anggota/getProfile'
];

// Install event - cache static assets
self.addEventListener('install', event => {
  console.log('[SW] Installing Service Worker');
  event.waitUntil(
    caches.open(STATIC_CACHE)
      .then(cache => {
        console.log('[SW] Caching static assets');
        return cache.addAll(STATIC_ASSETS);
      })
      .then(() => {
        console.log('[SW] Service Worker installed');
        return self.skipWaiting();
      })
  );
});

// Activate event - clean up old caches
self.addEventListener('activate', event => {
  console.log('[SW] Activating Service Worker');
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.map(cacheName => {
          if (cacheName !== STATIC_CACHE && cacheName !== DYNAMIC_CACHE) {
            console.log('[SW] Deleting old cache:', cacheName);
            return caches.delete(cacheName);
          }
        })
      );
    }).then(() => {
      console.log('[SW] Service Worker activated');
      return self.clients.claim();
    })
  );
});

// Fetch event - serve cached content when offline
self.addEventListener('fetch', event => {
  const { request } = event;
  const url = new URL(request.url);

  // Handle API requests
  if (url.pathname.includes('/backend/public/index.php')) {
    event.respondWith(handleApiRequest(request));
    return;
  }

  // Handle static assets
  if (STATIC_ASSETS.includes(url.pathname) || request.destination === 'style' || request.destination === 'script') {
    event.respondWith(
      caches.match(request)
        .then(response => {
          if (response) {
            return response;
          }

          return fetch(request).then(response => {
            // Cache successful responses
            if (response.status === 200 && response.type === 'basic') {
              const responseClone = response.clone();
              caches.open(DYNAMIC_CACHE)
                .then(cache => cache.put(request, responseClone));
            }
            return response;
          });
        })
        .catch(() => {
          // Return offline fallback for HTML pages
          if (request.destination === 'document') {
            return caches.match('/ksp_polri/frontend/pages/offline.html')
              .then(response => response || new Response('Offline - Please check your connection'));
          }
        })
    );
    return;
  }

  // Default fetch for other requests
  event.respondWith(
    caches.match(request)
      .then(response => {
        if (response) {
          return response;
        }

        return fetch(request).then(response => {
          // Don't cache API responses or large files
          if (response.status === 200 &&
              response.type === 'basic' &&
              !url.pathname.includes('/api/') &&
              !url.pathname.includes('/backend/') &&
              response.headers.get('content-length') < 1024 * 1024) { // < 1MB
            const responseClone = response.clone();
            caches.open(DYNAMIC_CACHE)
              .then(cache => cache.put(request, responseClone));
          }
          return response;
        });
      })
  );
});

// Handle API requests with offline support
async function handleApiRequest(request) {
  try {
    const response = await fetch(request);
    return response;
  } catch (error) {
    console.log('[SW] API request failed, checking cache');

    // Try to serve cached API response
    const cachedResponse = await caches.match(request);
    if (cachedResponse) {
      console.log('[SW] Serving cached API response');
      return cachedResponse;
    }

    // Return offline API response
    return new Response(JSON.stringify({
      status: false,
      message: 'Anda sedang offline. Data yang ditampilkan mungkin tidak terbaru.',
      offline: true,
      cached: true
    }), {
      headers: { 'Content-Type': 'application/json' }
    });
  }
}

// Background sync for offline actions
self.addEventListener('sync', event => {
  console.log('[SW] Background sync triggered:', event.tag);

  if (event.tag === 'background-sync-forms') {
    event.waitUntil(syncPendingForms());
  }

  if (event.tag === 'background-sync-notifications') {
    event.waitUntil(syncPendingNotifications());
  }
});

// Push notifications
self.addEventListener('push', event => {
  console.log('[SW] Push notification received');

  if (event.data) {
    const data = event.data.json();
    const options = {
      body: data.body,
      icon: '/ksp_polri/assets/icons/icon-192x192.png',
      badge: '/ksp_polri/assets/icons/icon-72x72.png',
      vibrate: [100, 50, 100],
      data: {
        url: data.url || '/ksp_polri/frontend/pages/dashboard_anggota.html'
      },
      actions: [
        {
          action: 'view',
          title: 'Lihat',
          icon: '/ksp_polri/assets/icons/icon-72x72.png'
        },
        {
          action: 'dismiss',
          title: 'Tutup'
        }
      ]
    };

    event.waitUntil(
      self.registration.showNotification(data.title || 'KSP POLRI', options)
    );
  }
});

// Handle notification clicks
self.addEventListener('notificationclick', event => {
  console.log('[SW] Notification clicked');

  event.notification.close();

  if (event.action === 'dismiss') {
    return;
  }

  const url = event.notification.data.url;

  event.waitUntil(
    clients.openWindow(url)
  );
});

// Sync pending forms (placeholder for future implementation)
async function syncPendingForms() {
  console.log('[SW] Syncing pending forms');
  // Implementation would sync any offline form submissions
}

// Sync pending notifications (placeholder for future implementation)
async function syncPendingNotifications() {
  console.log('[SW] Syncing pending notifications');
  // Implementation would check for new notifications when back online
}

// Periodic background sync (if supported)
self.addEventListener('periodicsync', event => {
  if (event.tag === 'update-cached-data') {
    event.waitUntil(updateCachedData());
  }
});

// Update cached data periodically
async function updateCachedData() {
  console.log('[SW] Updating cached data');

  try {
    // Refresh important API data
    const cache = await caches.open(DYNAMIC_CACHE);

    for (const endpoint of API_ENDPOINTS) {
      try {
        const url = `/ksp_polri/backend/public/index.php?path=${endpoint}`;
        const response = await fetch(url);

        if (response.ok) {
          await cache.put(url, response);
          console.log(`[SW] Updated cache for ${endpoint}`);
        }
      } catch (error) {
        console.log(`[SW] Failed to update cache for ${endpoint}:`, error);
      }
    }
  } catch (error) {
    console.log('[SW] Error updating cached data:', error);
  }
}

// Message handling from main thread
self.addEventListener('message', event => {
  const { type, data } = event.data;

  switch (type) {
    case 'SKIP_WAITING':
      self.skipWaiting();
      break;

    case 'GET_VERSION':
      event.ports[0].postMessage({ version: CACHE_NAME });
      break;

    case 'CLEAR_CACHE':
      clearAllCaches().then(() => {
        event.ports[0].postMessage({ success: true });
      });
      break;

    default:
      console.log('[SW] Unknown message type:', type);
  }
});

// Clear all caches
async function clearAllCaches() {
  const cacheNames = await caches.keys();
  await Promise.all(
    cacheNames.map(cacheName => caches.delete(cacheName))
  );
  console.log('[SW] All caches cleared');
}
