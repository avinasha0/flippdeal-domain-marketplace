<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Message $message;

    /**
     * Create a new event instance.
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        $channels = [
            new PrivateChannel('user.' . $this->message->to_user_id),
        ];

        // Also broadcast to domain channel if message is related to a domain
        if ($this->message->domain_id) {
            $channels[] = new PrivateChannel('domain.' . $this->message->domain_id);
        }

        return $channels;
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->message->id,
            'from_user_id' => $this->message->from_user_id,
            'to_user_id' => $this->message->to_user_id,
            'domain_id' => $this->message->domain_id,
            'body' => $this->message->body,
            'created_at' => $this->message->created_at->toISOString(),
            'from_user' => [
                'id' => $this->message->fromUser->id,
                'name' => $this->message->fromUser->name,
                'avatar' => $this->message->fromUser->avatar,
            ],
            'domain' => $this->message->domain ? [
                'id' => $this->message->domain->id,
                'name' => $this->message->domain->full_domain,
            ] : null,
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'message.sent';
    }
}