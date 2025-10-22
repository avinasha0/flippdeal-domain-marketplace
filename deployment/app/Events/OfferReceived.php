<?php

namespace App\Events;

use App\Models\Offer;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OfferReceived implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Offer $offer;
    public $domainOwnerId;

    /**
     * Create a new event instance.
     */
    public function __construct(Offer $offer)
    {
        $this->offer = $offer;
        $this->domainOwnerId = $offer->domain->user_id;
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
                'id' => 'offer_' . $this->offer->id,
                'type' => 'offer_received',
                'title' => 'New Offer Received',
                'message' => "Someone offered {$this->offer->formatted_amount} for {$this->offer->domain->full_domain}",
                'time' => $this->offer->created_at->diffForHumans(),
                'created_at' => $this->offer->created_at->toISOString(),
                'unread' => true,
                'data' => [
                    'offer_id' => $this->offer->id,
                    'domain_id' => $this->offer->domain->id,
                    'buyer_name' => $this->offer->buyer->name,
                    'amount' => $this->offer->amount,
                    'formatted_amount' => $this->offer->formatted_amount,
                    'domain_name' => $this->offer->domain->full_domain,
                    'status' => $this->offer->status,
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
