<?php

namespace App\Events;

use App\Models\Transaction;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentReceived implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Transaction $transaction;
    public $userId;

    /**
     * Create a new event instance.
     */
    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
        $this->userId = $transaction->user_id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->userId),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'activity' => [
                'id' => 'transaction_' . $this->transaction->id,
                'type' => 'payment_received',
                'title' => 'Payment Received',
                'message' => "You received {$this->transaction->formatted_amount} for {$this->transaction->domain->full_domain}",
                'time' => $this->transaction->created_at->diffForHumans(),
                'created_at' => $this->transaction->created_at->toISOString(),
                'unread' => true,
                'data' => [
                    'transaction_id' => $this->transaction->id,
                    'domain_id' => $this->transaction->domain->id,
                    'amount' => $this->transaction->amount,
                    'formatted_amount' => $this->transaction->formatted_amount,
                    'domain_name' => $this->transaction->domain->full_domain,
                    'transaction_type' => $this->transaction->type,
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
