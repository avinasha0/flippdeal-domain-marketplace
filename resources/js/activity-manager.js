/**
 * Activity Manager - Handles real-time activity updates
 */
class ActivityManager {
    constructor() {
        this.container = null;
        this.echo = null;
        this.refreshInterval = null;
        this.isInitialized = false;
    }

    /**
     * Initialize the activity manager
     */
    init() {
        if (this.isInitialized) {
            return;
        }

        this.container = document.getElementById('activity-container');
        if (!this.container) {
            console.warn('Activity container not found');
            return;
        }

        // Initialize Laravel Echo if available
        this.initializeEcho();
        
        // Set up auto-refresh as fallback
        this.setupAutoRefresh();
        
        // Load initial activities
        this.loadActivities();
        
        this.isInitialized = true;
    }

    /**
     * Initialize Laravel Echo for real-time updates
     */
    initializeEcho() {
        if (typeof window.Echo !== 'undefined') {
            this.echo = window.Echo;
            
            // Listen for activity updates
            this.echo.private(`user.${this.getUserId()}`)
                .listen('.activity.created', (e) => {
                    this.handleNewActivity(e.activity);
                });
                
            console.log('Echo initialized for activity updates');
        } else {
            console.warn('Laravel Echo not available, using polling fallback');
        }
    }

    /**
     * Set up auto-refresh as fallback
     */
    setupAutoRefresh() {
        // Refresh every 30 seconds if Echo is not available
        if (!this.echo) {
            this.refreshInterval = setInterval(() => {
                this.loadActivities();
            }, 30000);
        }
    }

    /**
     * Load activities from API
     */
    async loadActivities() {
        try {
            console.log('Loading activities...');
            
            // Try the alternative route first (without CSRF)
            let response = await fetch('/activity-data', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });
            
            console.log('Response status:', response.status);
            
            if (!response.ok) {
                console.log('Trying API route with CSRF...');
                // Fallback to API route with CSRF
                response = await fetch('/api/activity', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.getCsrfToken(),
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                });
                
                console.log('API Response status:', response.status);
            }
            
            if (!response.ok) {
                const errorText = await response.text();
                console.error('Error response:', errorText);
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            const data = await response.json();
            console.log('Activities data:', data);
            this.displayActivities(data.activities);
            
        } catch (error) {
            console.error('Error loading activities:', error);
            this.displayError('Failed to load recent activities: ' + error.message);
        }
    }

    /**
     * Handle new activity from real-time updates
     */
    handleNewActivity(activity) {
        // Add new activity to the top of the list
        const currentActivities = this.getCurrentActivities();
        const updatedActivities = [activity, ...currentActivities];
        
        // Limit to 10 activities
        if (updatedActivities.length > 10) {
            updatedActivities.splice(10);
        }
        
        this.displayActivities(updatedActivities);
        
        // Show notification if user is not focused on the page
        if (document.hidden) {
            this.showNotification(activity);
        }
    }

    /**
     * Display activities in the UI
     */
    displayActivities(activities) {
        if (!this.container) return;
        
        if (activities.length === 0) {
            this.container.innerHTML = `
                <div class="p-4 text-center text-gray-500 dark:text-gray-400">
                    <p>No recent activity</p>
                </div>
            `;
            return;
        }
        
        this.container.innerHTML = activities.map(activity => this.renderActivity(activity)).join('');
    }

    /**
     * Render a single activity item
     */
    renderActivity(activity) {
        const typeClass = this.getActivityTypeClass(activity.type);
        const iconClass = this.getActivityIconClass(activity.type);
        const icon = this.getActivityIcon(activity.type);
        
        return `
            <div class="p-4 border-l-4 ${typeClass} ${activity.unread ? 'bg-opacity-100' : 'bg-opacity-50'}">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <div class="${iconClass}">
                            ${icon}
                        </div>
                    </div>
                    <div class="ml-3 flex-1">
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white">${activity.title}</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">${activity.message}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">${activity.time}</p>
                    </div>
                    ${activity.unread ? `
                        <div class="flex-shrink-0">
                            <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                        </div>
                    ` : ''}
                </div>
            </div>
        `;
    }

    /**
     * Get current activities from DOM
     */
    getCurrentActivities() {
        // This is a simplified implementation
        // In a real app, you might want to maintain a state object
        return [];
    }

    /**
     * Show browser notification
     */
    showNotification(activity) {
        if ('Notification' in window && Notification.permission === 'granted') {
            new Notification(activity.title, {
                body: activity.message,
                icon: '/favicon.ico'
            });
        }
    }

    /**
     * Display error message
     */
    displayError(message) {
        if (!this.container) return;
        
        this.container.innerHTML = `
            <div class="p-4 text-center text-red-500 dark:text-red-400">
                <p>${message}</p>
                <button onclick="activityManager.loadActivities()" class="mt-2 text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                    Try Again
                </button>
            </div>
        `;
    }

    /**
     * Get activity type CSS class
     */
    getActivityTypeClass(type) {
        const typeClasses = {
            'bid_received': 'bg-green-50 border-green-200 dark:bg-green-900/20 dark:border-green-800',
            'offer_received': 'bg-blue-50 border-blue-200 dark:bg-blue-900/20 dark:border-blue-800',
            'payment_received': 'bg-green-50 border-green-200 dark:bg-green-900/20 dark:border-green-800',
            'domain_approved': 'bg-green-50 border-green-200 dark:bg-green-900/20 dark:border-green-800',
            'domain_rejected': 'bg-red-50 border-red-200 dark:bg-red-900/20 dark:border-red-800',
            'new_message': 'bg-blue-50 border-blue-200 dark:bg-blue-900/20 dark:border-blue-800',
            'auction_ending': 'bg-yellow-50 border-yellow-200 dark:bg-yellow-900/20 dark:border-yellow-800',
            'info': 'bg-blue-50 border-blue-200 dark:bg-blue-900/20 dark:border-blue-800',
        };
        return typeClasses[type] || 'bg-gray-50 border-gray-200 dark:bg-gray-900/20 dark:border-gray-800';
    }

    /**
     * Get activity icon CSS class
     */
    getActivityIconClass(type) {
        const iconClasses = {
            'bid_received': 'text-green-500 dark:text-green-400',
            'offer_received': 'text-blue-500 dark:text-blue-400',
            'payment_received': 'text-green-500 dark:text-green-400',
            'domain_approved': 'text-green-500 dark:text-green-400',
            'domain_rejected': 'text-red-500 dark:text-red-400',
            'new_message': 'text-blue-500 dark:text-blue-400',
            'auction_ending': 'text-yellow-500 dark:text-yellow-400',
            'info': 'text-blue-500 dark:text-blue-400',
        };
        return iconClasses[type] || 'text-gray-500 dark:text-gray-400';
    }

    /**
     * Get activity icon SVG
     */
    getActivityIcon(type) {
        const icons = {
            'bid_received': '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path></svg>',
            'offer_received': '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
            'payment_received': '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path></svg>',
            'domain_approved': '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
            'domain_rejected': '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>',
            'new_message': '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>',
            'auction_ending': '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg>',
            'info': '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
        };
        return icons[type] || '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
    }

    /**
     * Get user ID from meta tag or data attribute
     */
    getUserId() {
        const meta = document.querySelector('meta[name="user-id"]');
        const userId = meta ? meta.getAttribute('content') : null;
        console.log('User ID:', userId);
        return userId;
    }

    /**
     * Get CSRF token
     */
    getCsrfToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        const token = meta ? meta.getAttribute('content') : '';
        console.log('CSRF Token:', token);
        return token;
    }

    /**
     * Refresh activities manually
     */
    refresh() {
        this.loadActivities();
    }

    /**
     * Clean up resources
     */
    destroy() {
        if (this.refreshInterval) {
            clearInterval(this.refreshInterval);
        }
        
        if (this.echo) {
            this.echo.leave(`user.${this.getUserId()}`);
        }
        
        this.isInitialized = false;
    }
}

// Create global instance
window.activityManager = new ActivityManager();

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    window.activityManager.init();
});

// Clean up when page unloads
window.addEventListener('beforeunload', function() {
    window.activityManager.destroy();
});
