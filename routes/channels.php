<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Auth;
use App\Models\Domain;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

// Private user channel - only the user themselves can listen
Broadcast::channel('user.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Private domain channel - users involved with the domain can listen
Broadcast::channel('domain.{domainId}', function ($user, $domainId) {
    $domain = Domain::find($domainId);
    
    if (!$domain) {
        return false;
    }

    // Domain owner can always listen
    if ($domain->user_id === $user->id) {
        return true;
    }

    // Users who have bid on this domain can listen
    if ($domain->bids()->where('user_id', $user->id)->exists()) {
        return true;
    }

    // Users who have made offers can listen
    if ($domain->offers()->where('buyer_id', $user->id)->exists()) {
        return true;
    }

    // Users who have this domain in their watchlist can listen
    if ($domain->watchlist()->where('user_id', $user->id)->exists()) {
        return true;
    }

    // For active domains, any authenticated user can listen (public auctions)
    if ($domain->status === 'active') {
        return true;
    }

    return false;
});

// Presence auction channel - for live auction participants
Broadcast::channel('auction.{domainId}', function ($user, $domainId) {
    $domain = Domain::find($domainId);
    
    if (!$domain) {
        return false;
    }

    // Must be an active auction
    if ($domain->status !== 'active' || !$domain->enable_bidding) {
        return false;
    }

    // Auction must not have ended
    if ($domain->auction_end && $domain->auction_end->isPast()) {
        return false;
    }

    // Domain owner can join
    if ($domain->user_id === $user->id) {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'avatar' => $user->avatar,
            'role' => 'owner'
        ];
    }

    // Users who have bid can join
    if ($domain->bids()->where('user_id', $user->id)->exists()) {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'avatar' => $user->avatar,
            'role' => 'bidder'
        ];
    }

    // Users who have this domain in their watchlist can join
    if ($domain->watchlist()->where('user_id', $user->id)->exists()) {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'avatar' => $user->avatar,
            'role' => 'watcher'
        ];
    }

    // Any authenticated user can join public auctions
    return [
        'id' => $user->id,
        'name' => $user->name,
        'avatar' => $user->avatar,
        'role' => 'observer'
    ];
});

// Admin channel - only admin users can listen
Broadcast::channel('admin', function ($user) {
    return $user->isAdmin();
});

// System channel - for system-wide announcements
Broadcast::channel('system', function ($user) {
    return true; // Any authenticated user
});
