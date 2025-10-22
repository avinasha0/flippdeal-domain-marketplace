<?php

namespace App\Events;

use App\Models\Bid;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BidReceived implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Bid $bid;
    public $domainOwnerId;

    /**
     * Create a new event instance.
     */
    public function __construct(Bid $bid)
    {
        $this->bid = $bid;
        $this->domainOwnerId = $bid->domain->user_id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->domainOwnerId),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'activity' => [
                'id' => 'bid_' . $this->bid->id,
                'type' => 'bid_received',
                'title' => 'New Bid Received',
                'message' => "Someone bid {$this->bid->formatted_amount} on {$this->bid->domain->full_domain}",
                'time' => $this->bid->created_at->diffForHumans(),
                'created_at' => $this->bid->created_at->toISOString(),
                'unread' => true,
                'data' => [
                    'bid_id' => $this->bid->id,
                    'domain_id' => $this->bid->domain->id,
                    'bidder_name' => $this->bid->user->name,
                    'amount' => $this->bid->amount,
                    'formatted_amount' => $this->bid->formatted_amount,
                    'domain_name' => $this->bid->domain->full_domain,
                ]
            ]
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'activity.created';
    }
}
