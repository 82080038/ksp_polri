// PWA functionality for KSP POLRI
class KSPPWA {
    constructor() {
        this.deferredPrompt = null;
        this.isInstalled = false;
        this.init();
    }

    init() {
        // Check if PWA is supported
        if (!this.isPWACompatible()) {
            console.log('[PWA] PWA not supported in this browser');
            return;
        }

        this.registerServiceWorker();
        this.setupInstallPrompt();
        this.setupNetworkMonitoring();
        this.setupOfflineDetection();

        console.log('[PWA] PWA initialized');
    }

    isPWACompatible() {
        return 'serviceWorker' in navigator &&
               'caches' in window &&
               'fetch' in window &&
               'Notification' in window;
    }

    async registerServiceWorker() {
        if ('serviceWorker' in navigator) {
            try {
                const registration = await navigator.serviceWorker.register('/ksp_polri/frontend/sw.js', {
                    scope: '/ksp_polri/'
                });

                console.log('[PWA] Service Worker registered:', registration.scope);

                // Handle updates
                registration.addEventListener('updatefound', () => {
                    const newWorker = registration.installing;
                    newWorker.addEventListener('statechange', () => {
                        if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                            this.showUpdateNotification();
                        }
                    });
                });

                // Handle messages from service worker
                navigator.serviceWorker.addEventListener('message', event => {
                    this.handleServiceWorkerMessage(event);
                });

            } catch (error) {
                console.error('[PWA] Service Worker registration failed:', error);
            }
        }
    }

    setupInstallPrompt() {
        // Listen for the beforeinstallprompt event
        window.addEventListener('beforeinstallprompt', (event) => {
            console.log('[PWA] Install prompt available');
            event.preventDefault();
            this.deferredPrompt = event;

            // Show install button
            this.showInstallButton();
        });

        // Listen for successful installation
        window.addEventListener('appinstalled', (event) => {
            console.log('[PWA] App installed successfully');
            this.isInstalled = true;
            this.hideInstallButton();
            this.showInstallSuccess();
        });

        // Check if already installed
        if (window.matchMedia && window.matchMedia('(display-mode: standalone)').matches) {
            this.isInstalled = true;
            console.log('[PWA] App is running in standalone mode');
        }
    }

    setupNetworkMonitoring() {
        // Monitor online/offline status
        window.addEventListener('online', () => {
            console.log('[PWA] Back online');
            this.showOnlineNotification();
        });

        window.addEventListener('offline', () => {
            console.log('[PWA] Gone offline');
            this.showOfflineNotification();
        });
    }

    setupOfflineDetection() {
        // Check if we're currently offline
        if (!navigator.onLine) {
            this.showOfflineNotification();
        }
    }

    showInstallButton() {
        // Remove existing install button
        this.hideInstallButton();

        // Create install button
        const installButton = document.createElement('button');
        installButton.id = 'pwa-install-btn';
        installButton.className = 'btn btn-success btn-sm position-fixed';
        installButton.style.cssText = `
            bottom: 20px;
            right: 20px;
            z-index: 1050;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            animation: bounceIn 0.5s ease-out;
        `;
        installButton.innerHTML = '📱 Install App';
        installButton.onclick = () => this.promptInstall();

        document.body.appendChild(installButton);
    }

    hideInstallButton() {
        const existingButton = document.getElementById('pwa-install-btn');
        if (existingButton) {
            existingButton.remove();
        }
    }

    async promptInstall() {
        if (!this.deferredPrompt) {
            console.log('[PWA] No install prompt available');
            return;
        }

        // Show the install prompt
        this.deferredPrompt.prompt();

        // Wait for the user to respond to the prompt
        const { outcome } = await this.deferredPrompt.userChoice;
        console.log('[PWA] User install choice:', outcome);

        // Clear the deferred prompt
        this.deferredPrompt = null;

        // Hide the install button
        this.hideInstallButton();

        if (outcome === 'accepted') {
            this.showInstallSuccess();
        }
    }

    showInstallSuccess() {
        this.showNotification('✅ Aplikasi berhasil diinstall!', 'success');
    }

    showUpdateNotification() {
        // Create update notification
        const updateToast = document.createElement('div');
        updateToast.className = 'toast align-items-center text-white bg-primary border-0 position-fixed';
        updateToast.style.cssText = `
            bottom: 20px;
            left: 20px;
            z-index: 1060;
            min-width: 300px;
        `;
        updateToast.setAttribute('role', 'alert');
        updateToast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    🔄 Update aplikasi tersedia!
                    <div class="mt-2">
                        <button type="button" class="btn btn-light btn-sm me-2" onclick="kspPwa.updateApp()">Update</button>
                        <button type="button" class="btn btn-outline-light btn-sm" onclick="this.parentElement.parentElement.parentElement.remove()">Nanti</button>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" onclick="this.parentElement.remove()"></button>
            </div>
        `;

        document.body.appendChild(updateToast);

        // Auto-show toast
        const bsToast = new bootstrap.Toast(updateToast);
        bsToast.show();
    }

    async updateApp() {
        console.log('[PWA] Updating app...');

        // Send message to service worker to skip waiting
        if ('serviceWorker' in navigator && navigator.serviceWorker.controller) {
            navigator.serviceWorker.controller.postMessage({ type: 'SKIP_WAITING' });
        }

        // Reload the page
        window.location.reload();
    }

    showOnlineNotification() {
        this.showNotification('🌐 Koneksi internet tersambung kembali', 'success');
    }

    showOfflineNotification() {
        this.showNotification('📱 Anda sedang offline. Beberapa fitur mungkin terbatas.', 'warning');
    }

    showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        notification.style.cssText = `
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1070;
            min-width: 300px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        `;
        notification.innerHTML = `
            ${message}
            <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
        `;

        document.body.appendChild(notification);

        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 5000);
    }

    handleServiceWorkerMessage(event) {
        const { type, data } = event.data;

        switch (type) {
            case 'CACHE_UPDATED':
                this.showNotification('💾 Cache aplikasi telah diperbarui', 'info');
                break;

            case 'OFFLINE_READY':
                this.showNotification('📱 Aplikasi siap digunakan offline', 'success');
                break;

            default:
                console.log('[PWA] Service Worker message:', type, data);
        }
    }

    // Public methods for external access
    isOnline() {
        return navigator.onLine;
    }

    isInstalled() {
        return this.isInstalled || (window.matchMedia && window.matchMedia('(display-mode: standalone)').matches);
    }

    async getCacheInfo() {
        if ('caches' in window) {
            const cacheNames = await caches.keys();
            const cacheInfo = {};

            for (const cacheName of cacheNames) {
                const cache = await caches.open(cacheName);
                const keys = await cache.keys();
                cacheInfo[cacheName] = keys.length;
            }

            return cacheInfo;
        }
        return null;
    }

    async clearCache() {
        if ('caches' in window) {
            const cacheNames = await caches.keys();
            await Promise.all(cacheNames.map(name => caches.delete(name)));
            console.log('[PWA] All caches cleared');
            return true;
        }
        return false;
    }
}

// Initialize PWA when DOM is ready
let kspPwa;
document.addEventListener('DOMContentLoaded', () => {
    kspPwa = new KSPPWA();
});

// Export for global access
window.kspPwa = kspPwa;
