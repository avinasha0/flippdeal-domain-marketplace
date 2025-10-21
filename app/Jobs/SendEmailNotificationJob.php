<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendEmailNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300; // 5 minutes
    public $tries = 3;
    public $backoff = [60, 120, 300]; // Retry after 1, 2, 5 minutes

    protected $userId;
    protected $emailType;
    protected $data;
    protected $priority;

    /**
     * Create a new job instance.
     */
    public function __construct(int $userId, string $emailType, array $data = [], string $priority = 'normal')
    {
        $this->userId = $userId;
        $this->emailType = $emailType;
        $this->data = $data;
        $this->priority = $priority;
        
        // Set queue based on priority
        $this->onQueue($this->getQueueName($priority));
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $user = User::find($this->userId);
            
            if (!$user) {
                Log::warning('User not found for email notification', [
                    'user_id' => $this->userId,
                    'email_type' => $this->emailType,
                ]);
                return;
            }

            if (!$user->email) {
                Log::warning('User has no email address', [
                    'user_id' => $this->userId,
                    'email_type' => $this->emailType,
                ]);
                return;
            }

            Log::info('Sending email notification', [
                'user_id' => $this->userId,
                'email' => $user->email,
                'email_type' => $this->emailType,
                'priority' => $this->priority,
            ]);

            $this->sendEmail($user);

            Log::info('Email notification sent successfully', [
                'user_id' => $this->userId,
                'email_type' => $this->emailType,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send email notification', [
                'user_id' => $this->userId,
                'email_type' => $this->emailType,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Send the actual email
     */
    protected function sendEmail(User $user): void
    {
        switch ($this->emailType) {
            case 'new_message':
                $this->sendNewMessageEmail($user);
                break;
            
            case 'new_bid':
                $this->sendNewBidEmail($user);
                break;
            
            case 'auction_ending':
                $this->sendAuctionEndingEmail($user);
                break;
            
            case 'domain_sold':
                $this->sendDomainSoldEmail($user);
                break;
            
            case 'kyc_required':
                $this->sendKycRequiredEmail($user);
                break;
            
            case 'kyc_approved':
                $this->sendKycApprovedEmail($user);
                break;
            
            case 'kyc_rejected':
                $this->sendKycRejectedEmail($user);
                break;
            
            case 'aml_flag':
                $this->sendAmlFlagEmail($user);
                break;
            
            default:
                Log::warning('Unknown email type', [
                    'email_type' => $this->emailType,
                    'user_id' => $this->userId,
                ]);
        }
    }

    /**
     * Send new message email
     */
    protected function sendNewMessageEmail(User $user): void
    {
        $senderName = $this->data['sender_name'] ?? 'Someone';
        $domainName = $this->data['domain_name'] ?? 'a domain';
        $messagePreview = $this->data['message_preview'] ?? '';

        Mail::send('emails.new-message', [
            'user' => $user,
            'sender_name' => $senderName,
            'domain_name' => $domainName,
            'message_preview' => $messagePreview,
            'conversation_url' => $this->data['conversation_url'] ?? route('conversations.index'),
        ], function ($message) use ($user, $senderName, $domainName) {
            $message->to($user->email, $user->name)
                   ->subject("New message from {$senderName} about {$domainName}");
        });
    }

    /**
     * Send new bid email
     */
    protected function sendNewBidEmail(User $user): void
    {
        $domainName = $this->data['domain_name'] ?? 'your domain';
        $bidAmount = $this->data['bid_amount'] ?? 0;
        $bidderName = $this->data['bidder_name'] ?? 'Someone';

        Mail::send('emails.new-bid', [
            'user' => $user,
            'domain_name' => $domainName,
            'bid_amount' => $bidAmount,
            'bidder_name' => $bidderName,
            'domain_url' => $this->data['domain_url'] ?? route('domains.public.index'),
        ], function ($message) use ($user, $domainName) {
            $message->to($user->email, $user->name)
                   ->subject("New bid received for {$domainName}");
        });
    }

    /**
     * Send auction ending email
     */
    protected function sendAuctionEndingEmail(User $user): void
    {
        $domainName = $this->data['domain_name'] ?? 'the domain';
        $timeLeft = $this->data['time_left'] ?? 'soon';

        Mail::send('emails.auction-ending', [
            'user' => $user,
            'domain_name' => $domainName,
            'time_left' => $timeLeft,
            'domain_url' => $this->data['domain_url'] ?? route('domains.public.index'),
        ], function ($message) use ($user, $domainName) {
            $message->to($user->email, $user->name)
                   ->subject("Auction ending soon for {$domainName}");
        });
    }

    /**
     * Send domain sold email
     */
    protected function sendDomainSoldEmail(User $user): void
    {
        $domainName = $this->data['domain_name'] ?? 'your domain';
        $salePrice = $this->data['sale_price'] ?? 0;

        Mail::send('emails.domain-sold', [
            'user' => $user,
            'domain_name' => $domainName,
            'sale_price' => $salePrice,
            'dashboard_url' => route('dashboard'),
        ], function ($message) use ($user, $domainName) {
            $message->to($user->email, $user->name)
                   ->subject("Congratulations! {$domainName} has been sold");
        });
    }

    /**
     * Send KYC required email
     */
    protected function sendKycRequiredEmail(User $user): void
    {
        $amount = $this->data['amount'] ?? 0;
        $transactionId = $this->data['transaction_id'] ?? '';

        Mail::send('emails.kyc-required', [
            'user' => $user,
            'amount' => $amount,
            'transaction_id' => $transactionId,
            'kyc_url' => route('kyc.submit'),
        ], function ($message) use ($user) {
            $message->to($user->email, $user->name)
                   ->subject('KYC Verification Required');
        });
    }

    /**
     * Send KYC approved email
     */
    protected function sendKycApprovedEmail(User $user): void
    {
        Mail::send('emails.kyc-approved', [
            'user' => $user,
            'dashboard_url' => route('dashboard'),
        ], function ($message) use ($user) {
            $message->to($user->email, $user->name)
                   ->subject('KYC Verification Approved');
        });
    }

    /**
     * Send KYC rejected email
     */
    protected function sendKycRejectedEmail(User $user): void
    {
        $reason = $this->data['reason'] ?? 'Please contact support for details';

        Mail::send('emails.kyc-rejected', [
            'user' => $user,
            'reason' => $reason,
            'kyc_url' => route('kyc.submit'),
        ], function ($message) use ($user) {
            $message->to($user->email, $user->name)
                   ->subject('KYC Verification Rejected');
        });
    }

    /**
     * Send AML flag email
     */
    protected function sendAmlFlagEmail(User $user): void
    {
        $flagType = $this->data['flag_type'] ?? 'suspicious activity';
        $description = $this->data['description'] ?? '';

        Mail::send('emails.aml-flag', [
            'user' => $user,
            'flag_type' => $flagType,
            'description' => $description,
            'support_url' => route('support'),
        ], function ($message) use ($user) {
            $message->to($user->email, $user->name)
                   ->subject('Account Review Required');
        });
    }

    /**
     * Get queue name based on priority
     */
    protected function getQueueName(string $priority): string
    {
        return match ($priority) {
            'high' => 'emails-high',
            'low' => 'emails-low',
            default => 'emails',
        };
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('SendEmailNotificationJob failed permanently', [
            'user_id' => $this->userId,
            'email_type' => $this->emailType,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}