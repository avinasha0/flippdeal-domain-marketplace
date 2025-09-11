<?php

namespace App\Events;

use App\Models\Domain;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AuctionCountdown implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Domain $domain;
    public int $secondsLeft;

    /**
     * Create a new event instance.
     */
    public function __construct(Domain $domain, int $secondsLeft)
    {
        $this->domain = $domain;
        $this->secondsLeft = $secondsLeft;
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
        return [
            'domain_id' => $this->domain->id,
            'seconds_left' => $this->secondsLeft,
            'is_ending' => $this->secondsLeft <= 60, // Last minute warning
            'is_ended' => $this->secondsLeft <= 0,
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
        return 'auction.countdown';
    }
}