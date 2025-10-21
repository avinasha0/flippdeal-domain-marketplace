<?php

namespace App\Jobs;

use App\Models\Domain;
use App\Models\User;
use App\Notifications\AuctionEndingSoon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendAuctionEndingNotifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Find domains with auctions ending in the next hour
        $domains = Domain::activeAuctions()
            ->where('auction_end', '>', now())
            ->where('auction_end', '<=', now()->addHour())
            ->get();

        foreach ($domains as $domain) {
            $minutesRemaining = now()->diffInMinutes($domain->auction_end);
            
            // Only send notification if auction ends in 30 minutes or less
            if ($minutesRemaining <= 30) {
                // Get all users who have favorited this domain
                $favoriteUsers = User::whereHas('favorites', function ($query) use ($domain) {
                    $query->where('domain_id', $domain->id);
                })->get();

                // Send notification to domain owner
                $domain->user->notify(new AuctionEndingSoon($domain, $minutesRemaining));

                // Send notification to users who favorited the domain
                foreach ($favoriteUsers as $user) {
                    $user->notify(new AuctionEndingSoon($domain, $minutesRemaining));
                }

                Log::info("Sent auction ending notifications for domain {$domain->full_domain} to " . ($favoriteUsers->count() + 1) . " users");
            }
        }
    }
}