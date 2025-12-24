// Service Worker for Push Notifications
self.addEventListener('install', function(event) {
    self.skipWaiting();
});

self.addEventListener('activate', function(event) {
    event.waitUntil(self.clients.claim());
});

// Handle push notifications
self.addEventListener('push', function(event) {
    const data = event.data ? event.data.json() : {};
    
    const options = {
        body: data.body || 'You have a habit reminder!',
        icon: data.icon || '/favicon.ico',
        badge: data.badge || '/favicon.ico',
        tag: data.tag || 'habit-reminder',
        data: data.data || {},
        requireInteraction: false,
        vibrate: [200, 100, 200],
    };

    event.waitUntil(
        self.registration.showNotification(data.title || 'Habit Reminder', options)
    );
});

// Handle notification clicks
self.addEventListener('notificationclick', function(event) {
    event.notification.close();

    const urlToOpen = event.notification.data.url || '/habits';

    event.waitUntil(
        clients.matchAll({
            type: 'window',
            includeUncontrolled: true
        }).then(function(clientList) {
            // Check if there's already a window/tab open with the target URL
            for (let i = 0; i < clientList.length; i++) {
                const client = clientList[i];
                if (client.url === urlToOpen && 'focus' in client) {
                    return client.focus();
                }
            }
            // If not, open a new window/tab
            if (clients.openWindow) {
                return clients.openWindow(urlToOpen);
            }
        })
    );
});