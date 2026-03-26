/* assets/js/notifications.js */

class NotificationEngine {
    constructor() {
        this.dropdown = document.getElementById('notif-dropdown');
        this.badge = document.getElementById('notif-badge');
        this.list = document.getElementById('notif-list');
        this.bell = document.getElementById('notif-bell');
        this.toastContainer = document.getElementById('toast-container');

        this.isPolling = false;
        this.lastCount = 0;

        this.init();
    }

    init() {
        if (!this.bell) return;

        // Toggle dropdown
        this.bell.addEventListener('click', (e) => {
            e.preventDefault();
            this.dropdown.classList.toggle('active');
        });

        // Close on click outside
        document.addEventListener('click', (e) => {
            if (!this.bell.contains(e.target) && !this.dropdown.contains(e.target)) {
                this.dropdown.classList.remove('active');
            }
        });

        // Start polling
        this.startPolling();

        // Initial fetch
        this.fetchNotifications();
    }

    startPolling() {
        if (this.isPolling) return;
        this.isPolling = true;

        setInterval(() => {
            this.fetchNotifications(true); // silent fetch for polling
        }, 15000); // Poll every 15 seconds
    }

    async fetchNotifications(isSilent = false) {
        try {
            const response = await fetch(`${window.location.origin}/api/notifications.php?action=get_latest`);
            const data = await response.json();

            if (data.success) {
                this.updateUI(data.notifications, isSilent);
            }
        } catch (error) {
            console.error('Fetch Notifications Error:', error);
        }
    }

    updateUI(notifications, isSilent) {
        const count = notifications.length;

        // Update badge
        if (count > 0) {
            this.badge.innerText = count > 9 ? '9+' : count;
            this.badge.classList.add('active');

            // Show toast if count increased
            if (count > this.lastCount && isSilent) {
                const latest = notifications[0];
                this.showToast(latest.title, latest.message);
            }
        } else {
            this.badge.classList.remove('active');
        }

        this.lastCount = count;

        // Update list
        if (notifications.length === 0) {
            this.list.innerHTML = '<div style="padding: 30px; text-align: center; color: var(--gray-color); font-size: 13px;">No new notifications</div>';
            return;
        }

        this.list.innerHTML = notifications.map(n => `
            <a href="${n.link || '#'}" class="notification-item ${n.is_read ? '' : 'unread'}" onclick="markNotificationRead(${n.id})">
                <div class="notification-icon notif-${n.type}">
                    <i class="fa ${this.getIcon(n.type)}"></i>
                </div>
                <div class="notification-content">
                    <h5>${n.title}</h5>
                    <p>${n.message}</p>
                    <div class="notification-time">${this.formatTime(n.created_at)}</div>
                </div>
            </a>
        `).join('');
    }

    getIcon(type) {
        switch (type) {
            case 'enrollment': return 'fa-user-plus';
            case 'announcement': return 'fa-bullhorn';
            case 'update': return 'fa-sync-alt';
            default: return 'fa-bell';
        }
    }

    formatTime(timestamp) {
        const date = new Date(timestamp);
        const now = new Date();
        const diff = Math.floor((now - date) / 1000);

        if (diff < 60) return 'Just now';
        if (diff < 3600) return Math.floor(diff / 60) + 'm ago';
        if (diff < 86400) return Math.floor(diff / 3600) + 'h ago';
        return date.toLocaleDateString();
    }

    showToast(title, message) {
        const toast = document.createElement('div');
        toast.className = 'toast-message';
        toast.innerHTML = `
            <div class="notification-icon notif-announcement" style="width: 30px; height: 30px; font-size: 14px;">
                <i class="fa fa-bell"></i>
            </div>
            <div>
                <div style="font-weight: 800; font-size: 13px;">${title}</div>
                <div style="font-size: 11px; color: var(--gray-color);">${message}</div>
            </div>
        `;

        this.toastContainer.appendChild(toast);

        // Animation cleanup
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(20px)';
            toast.style.transition = 'all 0.5s ease';
            setTimeout(() => toast.remove(), 500);
        }, 5000);
    }
}

// Global functions for inline attributes
async function markNotificationRead(id) {
    try {
        await fetch(`${window.location.origin}/api/notifications.php?action=mark_read`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: id })
        });
        // We don't refresh immediately to allow the link to execute
    } catch (e) { }
}

async function markAllRead() {
    try {
        const res = await fetch(`${window.location.origin}/api/notifications.php?action=mark_all_read`);
        const data = await res.json();
        if (data.success) {
            window.notifEngine.fetchNotifications();
        }
    } catch (e) { }
}

// Initialize on load
document.addEventListener('DOMContentLoaded', () => {
    window.notifEngine = new NotificationEngine();
});
