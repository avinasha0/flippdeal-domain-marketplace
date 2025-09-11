<?php

namespace App\Notifications;

use App\Models\Domain;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DomainVerificationRequired extends Notification implements ShouldQueue
{
    use Queueable;

    protected Domain $domain;

    /**
     * Create a new notification instance.
     */
    public function __construct(Domain $domain)
    {
        $this->domain = $domain;
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
            ->subject('Domain Verification Required - ' . $this->domain->full_domain)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Your domain listing for **' . $this->domain->full_domain . '** requires verification before it can be published.')
            ->line('To verify your domain ownership, please follow these steps:')
            ->line('1. Go to your domain listing')
            ->line('2. Click on "Verify Domain"')
            ->line('3. Add the required DNS record to your domain')
            ->line('4. Click "Verify Domain" again to confirm')
            ->action('Verify Domain', route('domains.verification', $this->domain))
            ->line('If you have any questions, please contact our support team.')
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
            'type' => 'domain_verification_required',
            'domain_id' => $this->domain->id,
            'domain_name' => $this->domain->full_domain,
            'message' => 'Domain verification required for ' . $this->domain->full_domain,
            'action_url' => route('domains.verification', $this->domain)
        ];
    }
}