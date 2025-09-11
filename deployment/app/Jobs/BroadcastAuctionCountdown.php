<?php

namespace App\Jobs;

use App\Models\Domain;
use App\Events\AuctionCountdown;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class BroadcastAuctionCountdown implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Domain $domain;

    /**
     * Create a new job instance.
     */
    public function __construct(Domain $domain)
    {
        $this->domain = $domain;
    }

    /**
     * Execute the job.
     */
    public function handle(NotificationService $notificationService): void
    {
        try {
            // Check if auction has ended
            if (!$this->domain->auction_end || $this->domain->auction_end->isPast()) {
                Log::info('Auction countdown job skipped - auction ended', [
                    'domain_id' => $this->domain->id,
                    'auction_end' => $this->domain->auction_end?->toISOString(),
                ]);
                return;
            }

            // Calculate seconds left
            $secondsLeft = max(0, $this->domain->auction_end->diffInSeconds(now()));

            // Broadcast countdown event
            broadcast(new AuctionCountdown($this->domain, $secondsLeft));

            // Send notifications for ending soon
            if ($secondsLeft <= 300 && $secondsLeft > 0) { // 5 minutes
                $notificationService->notifyAuctionEndingSoon($this->domain, 5);
            } elseif ($secondsLeft <= 60 && $secondsLeft > 0) { // 1 minute
                $notificationService->notifyAuctionEndingSoon($this->domain, 1);
            }

            Log::info('Auction countdown broadcasted', [
                'domain_id' => $this->domain->id,
                'seconds_left' => $secondsLeft,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to broadcast auction countdown', [
                'domain_id' => $this->domain->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Auction countdown job failed', [
            'domain_id' => $this->domain->id,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}