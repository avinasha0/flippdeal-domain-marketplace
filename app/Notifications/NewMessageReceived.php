<?php

namespace App\Notifications;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewMessageReceived extends Notification implements ShouldQueue
{
    use Queueable;

    protected $message;

    /**
     * Create a new notification instance.
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('New Message Received')
                    ->line('You have received a new message from ' . $this->message->fromUser->name)
                    ->line('Domain: ' . $this->message->domain->full_domain)
                    ->line('Message: ' . $this->message->body)
                    ->action('View Message', route('conversations.show', $this->message->conversation_id));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'message_received',
            'title' => 'New Message Received',
            'message' => 'You have received a new message from ' . $this->message->fromUser->name,
            'data' => [
                'conversation_id' => $this->message->conversation_id,
                'domain_id' => $this->message->domain_id,
                'message_id' => $this->message->id,
                'from_user' => $this->message->fromUser->name,
                'domain_name' => $this->message->domain->full_domain
            ]
        ];
    }

    /**
     * Get the broadcast representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toBroadcast(object $notifiable): array
    {
        return [
            'type' => 'message_received',
            'title' => 'New Message Received',
            'message' => 'You have received a new message from ' . $this->message->fromUser->name,
            'data' => [
                'conversation_id' => $this->message->conversation_id,
                'domain_id' => $this->message->domain_id,
                'message_id' => $this->message->id,
                'from_user' => $this->message->fromUser->name,
                'domain_name' => $this->message->domain->full_domain
            ]
        ];
    }
}
