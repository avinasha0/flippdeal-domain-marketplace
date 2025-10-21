<?php

namespace App\Events;

use App\Models\Bid;
use App\Models\Domain;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BidPlaced implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Bid $bid;
    public Domain $domain;

    /**
     * Create a new event instance.
     */
    public function __construct(Bid $bid)
    {
        $this->bid = $bid;
        $this->domain = $bid->domain;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('domain.' . $this->domain->id),
            new PresenceChannel('auction.' . $this->domain->id),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        // Get current highest bid
        $currentHighest = $this->domain->bids()
            ->where('is_winning', true)
            ->first();

        return [
            'domain_id' => $this->domain->id,
            'bid_id' => $this->bid->id,
            'bidder_id' => $this->bid->user_id,
            'amount' => $this->bid->amount,
            'current_highest' => $currentHighest ? $currentHighest->amount : $this->bid->amount,
            'is_winning' => $this->bid->is_winning,
            'created_at' => $this->bid->created_at->toISOString(),
            'bidder' => [
                'id' => $this->bid->user->id,
                'name' => $this->bid->user->name,
            ],
            'domain' => [
                'id' => $this->domain->id,
                'name' => $this->domain->full_domain,
                'auction_end' => $this->domain->auction_end?->toISOString(),
            ],
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'bid.placed';
    }
}