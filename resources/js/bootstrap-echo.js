import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// Configure Echo
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: process.env.MIX_BROADCASTER || 'pusher',
    key: process.env.MIX_PUSHER_APP_KEY,
    cluster: process.env.MIX_PUSHER_APP_CLUSTER,
    forceTLS: true,
    encrypted: true,
    authEndpoint: '/broadcasting/auth',
    auth: {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Authorization': `Bearer ${localStorage.getItem('auth_token') || ''}`,
        },
    },
    // Enable logging in development
    enableLogging: process.env.NODE_ENV === 'development',
    // Reconnection settings
    enabledTransports: ['ws', 'wss'],
    disableStats: false,
});

// Connection state management
window.Echo.connector.pusher.connection.bind('connected', () => {
    console.log('Echo connected');
    window.dispatchEvent(new CustomEvent('echo:connected'));
});

window.Echo.connector.pusher.connection.bind('disconnected', () => {
    console.log('Echo disconnected');
    window.dispatchEvent(new CustomEvent('echo:disconnected'));
});

window.Echo.connector.pusher.connection.bind('error', (error) => {
    console.error('Echo connection error:', error);
    window.dispatchEvent(new CustomEvent('echo:error', { detail: error }));
});

// Reconnection handling with exponential backoff
let reconnectAttempts = 0;
const maxReconnectAttempts = 5;
const baseDelay = 1000; // 1 second

function attemptReconnect() {
    if (reconnectAttempts >= maxReconnectAttempts) {
        console.error('Max reconnection attempts reached');
        window.dispatchEvent(new CustomEvent('echo:max-reconnect-attempts'));
        return;
    }

    const delay = baseDelay * Math.pow(2, reconnectAttempts);
    reconnectAttempts++;

    console.log(`Attempting to reconnect in ${delay}ms (attempt ${reconnectAttempts}/${maxReconnectAttempts})`);

    setTimeout(() => {
        if (window.Echo && window.Echo.connector.pusher.connection.state === 'disconnected') {
            window.Echo.connector.pusher.connection.connect();
        }
    }, delay);
}

window.Echo.connector.pusher.connection.bind('disconnected', () => {
    if (reconnectAttempts < maxReconnectAttempts) {
        attemptReconnect();
    }
});

// Reset reconnection attempts on successful connection
window.Echo.connector.pusher.connection.bind('connected', () => {
    reconnectAttempts = 0;
});

// Global error handler
window.addEventListener('echo:error', (event) => {
    console.error('Echo error:', event.detail);
    // You can add user notification here
});

// Export for use in other modules
export default window.Echo;
