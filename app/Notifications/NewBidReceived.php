<?php

namespace App\Notifications;

use App\Models\Bid;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewBidReceived extends Notification implements ShouldQueue
{
    use Queueable;

    protected Bid $bid;

    /**
     * Create a new notification instance.
     */
    public function __construct(Bid $bid)
    {
        $this->bid = $bid;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Bid Received - ' . $this->bid->domain->full_domain)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('You have received a new bid on your domain **' . $this->bid->domain->full_domain . '**.')
            ->line('**Bid Amount:** $' . number_format($this->bid->bid_amount, 2))
            ->line('**Bidder:** ' . $this->bid->bidder->name)
            ->line('**Current Highest Bid:** $' . number_format($this->bid->domain->current_bid, 2))
            ->line('**Auction Ends:** ' . $this->bid->domain->auction_end->format('M j, Y \a\t g:i A'))
            ->action('View Domain', route('domains.show', $this->bid->domain))
            ->line('Good luck with your auction!')
            ->salutation('Best regards, The Domain Marketplace Team');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'new_bid_received',
            'bid_id' => $this->bid->id,
            'domain_id' => $this->bid->domain->id,
            'domain_name' => $this->bid->domain->full_domain,
            'bid_amount' => $this->bid->bid_amount,
            'bidder_name' => $this->bid->bidder->name,
            'message' => 'New bid of $' . number_format($this->bid->bid_amount, 2) . ' received on ' . $this->bid->domain->full_domain,
            'action_url' => route('domains.show', $this->bid->domain)
        ];
    }
}