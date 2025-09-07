<?php

namespace App\Console\Commands;

use App\Models\Domain;
use App\Jobs\BroadcastAuctionCountdown;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class BroadcastAuctionCountdowns extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auctions:broadcast-countdowns {--interval=30 : Broadcast interval in seconds}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Broadcast countdown updates for active auctions';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $interval = (int) $this->option('interval');
        
        $this->info("Starting auction countdown broadcasts (interval: {$interval}s)");
        
        while (true) {
            try {
                $this->broadcastCountdowns();
                sleep($interval);
            } catch (\Exception $e) {
                $this->error("Error broadcasting countdowns: " . $e->getMessage());
                Log::error('Auction countdown broadcast error', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                sleep($interval);
            }
        }

        return 0;
    }

    /**
     * Broadcast countdowns for active auctions.
     */
    private function broadcastCountdowns(): void
    {
        $activeAuctions = Domain::where('status', 'active')
            ->where('enable_bidding', true)
            ->whereNotNull('auction_end')
            ->where('auction_end', '>', now())
            ->get();

        $this->info("Found {$activeAuctions->count()} active auctions");

        foreach ($activeAuctions as $domain) {
            // Dispatch countdown job
            BroadcastAuctionCountdown::dispatch($domain);
            
            $this->line("Dispatched countdown for domain: {$domain->full_domain}");
        }
    }
}