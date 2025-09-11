<?php

namespace App\Notifications;

use App\Models\Domain;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AuctionEndingSoon extends Notification implements ShouldQueue
{
    use Queueable;

    protected Domain $domain;
    protected int $minutesRemaining;

    /**
     * Create a new notification instance.
     */
    public function __construct(Domain $domain, int $minutesRemaining)
    {
        $this->domain = $domain;
        $this->minutesRemaining = $minutesRemaining;
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
        $timeRemaining = $this->minutesRemaining < 60 
            ? $this->minutesRemaining . ' minutes'
            : round($this->minutesRemaining / 60, 1) . ' hours';

        return (new MailMessage)
            ->subject('Auction Ending Soon - ' . $this->domain->full_domain)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('The auction for **' . $this->domain->full_domain . '** is ending in **' . $timeRemaining . '**!')
            ->line('**Current Highest Bid:** $' . number_format($this->domain->current_bid, 2))
            ->line('**Number of Bids:** ' . $this->domain->bid_count)
            ->line('**Auction Ends:** ' . $this->domain->auction_end->format('M j, Y \a\t g:i A'))
            ->action('Place Your Bid', route('domains.show', $this->domain))
            ->line('Don\'t miss out on this opportunity!')
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
            'type' => 'auction_ending_soon',
            'domain_id' => $this->domain->id,
            'domain_name' => $this->domain->full_domain,
            'minutes_remaining' => $this->minutesRemaining,
            'current_bid' => $this->domain->current_bid,
            'message' => 'Auction for ' . $this->domain->full_domain . ' ending in ' . $this->minutesRemaining . ' minutes',
            'action_url' => route('domains.show', $this->domain)
        ];
    }
}