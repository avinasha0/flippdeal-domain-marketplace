<?php

namespace App\Console\Commands;

use App\Jobs\SendAuctionEndingNotifications;
use Illuminate\Console\Command;

class SendAuctionNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auctions:send-notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notifications for auctions ending soon';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Sending auction ending notifications...');
        
        SendAuctionEndingNotifications::dispatch();
        
        $this->info('Auction notifications job dispatched successfully!');
    }
}