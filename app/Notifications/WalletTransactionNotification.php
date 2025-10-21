<?php

namespace App\Notifications;

use App\Models\WalletTransaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WalletTransactionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $transaction;

    /**
     * Create a new notification instance.
     */
    public function __construct(WalletTransaction $transaction)
    {
        $this->transaction = $transaction;
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
        $isCredit = $this->transaction->type === WalletTransaction::TYPE_CREDIT;
        $action = $isCredit ? 'received' : 'withdrawn';
        $amount = '$' . number_format($this->transaction->amount, 2);
        
        return (new MailMessage)
            ->subject("Wallet Transaction: {$amount} {$action}")
            ->greeting("Hello {$notifiable->name},")
            ->line("You have {$action} {$amount} from your wallet.")
            ->line("Transaction Details:")
            ->line("- Amount: {$amount}")
            ->line("- Type: " . ucfirst($this->transaction->type))
            ->line("- Description: {$this->transaction->description}")
            ->line("- Status: " . ucfirst($this->transaction->status))
            ->line("- Date: " . $this->transaction->created_at->format('M j, Y \a\t g:i A'))
            ->action('View Wallet', route('wallet.index'))
            ->line('Thank you for using our platform!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $isCredit = $this->transaction->type === WalletTransaction::TYPE_CREDIT;
        $action = $isCredit ? 'received' : 'withdrawn';
        $amount = '$' . number_format($this->transaction->amount, 2);
        
        return [
            'type' => 'wallet_transaction',
            'transaction_id' => $this->transaction->id,
            'amount' => $this->transaction->amount,
            'formatted_amount' => $amount,
            'type_label' => $this->transaction->type,
            'action' => $action,
            'description' => $this->transaction->description,
            'status' => $this->transaction->status,
            'created_at' => $this->transaction->created_at->toISOString(),
        ];
    }
}