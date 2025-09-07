# Real-Time Messaging & Notification System

This document provides comprehensive setup and usage instructions for the real-time messaging and notification system implemented in FlippDeal.

## Features

- **User-to-User Messaging**: Real-time chat between buyers and sellers
- **Live Notifications**: Real-time notifications for bids, offers, transactions, and more
- **Live Auction Updates**: Real-time bid updates and countdown timers
- **Presence Channels**: Track active users in auctions and conversations
- **Secure Broadcasting**: Private and presence channels with proper authorization

## Architecture

### Backend Components

1. **Events**: Broadcastable events for real-time updates
   - `MessageSent`: When a new message is sent
   - `MessageRead`: When a message is marked as read
   - `NotificationCreated`: When a new notification is created
   - `BidPlaced`: When a new bid is placed
   - `AuctionCountdown`: Periodic countdown updates

2. **Channels**: Secure broadcasting channels
   - `private-user.{id}`: Private user notifications
   - `private-domain.{id}`: Domain-specific updates
   - `presence-auction.{id}`: Live auction participants

3. **Services**: Business logic services
   - `NotificationService`: Handles notification creation and broadcasting
   - `EscrowService`: Manages escrow transactions

4. **Jobs**: Background processing
   - `BroadcastAuctionCountdown`: Periodic countdown broadcasts
   - `ProcessPayout`: Escrow payout processing

### Frontend Components

1. **Alpine.js Components**:
   - `chat-window.blade.php`: Real-time messaging interface
   - `notification-bell.blade.php`: Notification dropdown
   - `auction-live.blade.php`: Live auction updates

2. **Echo Integration**:
   - `bootstrap-echo.js`: Laravel Echo configuration
   - Automatic reconnection handling
   - Connection state management

## Installation & Setup

### 1. Install Required Packages

```bash
# Backend packages
composer require pusher/pusher-php-server

# Frontend packages
npm install laravel-echo pusher-js
```

### 2. Environment Configuration

Add to your `.env` file:

```env
# Broadcasting
BROADCAST_DRIVER=pusher

# Pusher Configuration
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_APP_CLUSTER=your_cluster

# Optional: WebSockets (self-hosted alternative)
WEBSOCKETS_ENABLED=false
WEBSOCKETS_HOST=127.0.0.1
WEBSOCKETS_PORT=6001
WEBSOCKETS_SCHEME=http

# Queue Configuration
QUEUE_CONNECTION=redis
```

### 3. Frontend Configuration

Update `resources/js/app.js`:

```javascript
import './bootstrap-echo.js';
```

Update `vite.config.js`:

```javascript
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
});
```

### 4. Build Frontend Assets

```bash
npm run dev
# or for production
npm run build
```

### 5. Run Migrations

```bash
php artisan migrate
```

### 6. Start Queue Workers

```bash
# Start queue worker
php artisan queue:work

# Start auction countdown broadcaster (in separate terminal)
php artisan auctions:broadcast-countdowns
```

## Usage

### 1. Include Components in Blade Templates

#### Navigation Bar (with notification bell)
```blade
@include('components.notification-bell')
```

#### Domain Detail Page (with live auction)
```blade
@if($domain->enable_bidding && $domain->status === 'active')
    @include('components.auction-live', ['domain' => $domain])
@endif
```

#### Chat Window (for messaging)
```blade
@include('components.chat-window', [
    'domainId' => $domain->id,
    'otherUserId' => $otherUser->id
])
```

### 2. API Endpoints

#### Messaging API
- `POST /api/messages` - Send a message
- `GET /api/messages/conversations/{userId}` - Get conversation
- `PATCH /api/messages/{id}/read` - Mark message as read
- `GET /api/messages/unread-count` - Get unread count

#### Notifications API
- `GET /api/notifications` - Get user notifications
- `GET /api/notifications/unread-count` - Get unread count
- `PATCH /api/notifications/{id}/read` - Mark notification as read

### 3. Broadcasting Events

#### Send a Message
```php
use App\Events\MessageSent;

$message = Message::create([...]);
broadcast(new MessageSent($message));
```

#### Create Notification
```php
use App\Services\NotificationService;

$notificationService = new NotificationService();
$notificationService->notifyUser($user, 'bid.placed', $data);
```

#### Place a Bid
```php
use App\Events\BidPlaced;

$bid = Bid::create([...]);
broadcast(new BidPlaced($bid));
```

## Channel Authorization

### User Channel
- **Channel**: `private-user.{id}`
- **Authorization**: User can only access their own channel
- **Usage**: Personal notifications and direct messages

### Domain Channel
- **Channel**: `private-domain.{id}`
- **Authorization**: Domain owner, bidders, offer makers, watchers
- **Usage**: Domain-specific updates and messages

### Auction Presence Channel
- **Channel**: `presence-auction.{id}`
- **Authorization**: Any authenticated user for active auctions
- **Usage**: Live auction participants and observers

## Security Features

1. **Rate Limiting**: Message sending limited to 5 per second per user
2. **Input Sanitization**: HTML stripped from message bodies
3. **Channel Authorization**: Server-side validation for all channels
4. **CSRF Protection**: All API endpoints protected
5. **Authentication**: Sanctum-based API authentication

## Performance Optimizations

1. **Database Indexes**: Optimized queries for messages and notifications
2. **Queue Processing**: Background job processing for broadcasting
3. **Connection Pooling**: Efficient WebSocket connections
4. **Caching**: Redis-based caching for active users

## Monitoring & Debugging

### Queue Monitoring
```bash
# Monitor queue jobs
php artisan queue:monitor

# Check failed jobs
php artisan queue:failed
```

### Broadcasting Debug
```bash
# Test broadcasting
php artisan tinker
>>> broadcast(new App\Events\MessageSent($message));
```

### Log Files
- `storage/logs/laravel.log` - General application logs
- Queue job failures are logged automatically

## Troubleshooting

### Common Issues

1. **Echo Connection Failed**
   - Check Pusher credentials in `.env`
   - Verify `BROADCAST_DRIVER=pusher`
   - Check network connectivity

2. **Messages Not Broadcasting**
   - Ensure queue worker is running
   - Check `QUEUE_CONNECTION` setting
   - Verify channel authorization

3. **Frontend Not Updating**
   - Check browser console for errors
   - Verify Echo is loaded correctly
   - Check CSRF token configuration

### Debug Mode

Enable debug logging in `config/broadcasting.php`:

```php
'pusher' => [
    // ... other config
    'options' => [
        'debug' => true,
        'encrypted' => true,
    ],
],
```

## Production Deployment

### 1. Supervisor Configuration

Create `/etc/supervisor/conf.d/flippdeal-queue.conf`:

```ini
[program:flippdeal-queue]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/flippdeal/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path/to/flippdeal/storage/logs/queue-worker.log
stopwaitsecs=3600
```

### 2. Cron Jobs

Add to crontab:

```bash
# Auction countdown broadcaster
* * * * * cd /path/to/flippdeal && php artisan schedule:run >> /dev/null 2>&1
```

### 3. Redis Configuration

Ensure Redis is properly configured for production:

```ini
# /etc/redis/redis.conf
maxmemory 256mb
maxmemory-policy allkeys-lru
```

## Testing

### Unit Tests
```bash
php artisan test --filter=MessageTest
php artisan test --filter=NotificationTest
```

### Feature Tests
```bash
php artisan test --filter=BroadcastingTest
```

### Manual Testing
1. Open two browser windows (incognito mode)
2. Login as different users
3. Send messages and verify real-time updates
4. Place bids and verify live updates
5. Check notification delivery

## API Documentation

### Message Endpoints

#### Send Message
```http
POST /api/messages
Content-Type: application/json
Authorization: Bearer {token}

{
    "to_user_id": 123,
    "domain_id": 456,
    "body": "Hello, I'm interested in this domain"
}
```

#### Get Conversation
```http
GET /api/messages/conversations/123?domain_id=456&page=1&per_page=20
Authorization: Bearer {token}
```

### Notification Endpoints

#### Get Notifications
```http
GET /api/notifications?limit=20&unread_only=false
Authorization: Bearer {token}
```

#### Mark as Read
```http
PATCH /api/notifications/123/read
Authorization: Bearer {token}
```

## Support

For issues or questions:
1. Check the logs in `storage/logs/`
2. Verify configuration in `.env`
3. Test with the provided test script
4. Check queue worker status

## Changelog

### v1.0.0
- Initial implementation
- Real-time messaging system
- Live notifications
- Auction countdown updates
- Presence channels
- Secure channel authorization
- Alpine.js frontend components
- Comprehensive API endpoints
