<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Models\Domain;
use Carbon\Carbon;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('auctions:update-statuses', function () {
    $now = Carbon::now();
    
    $this->info("Current time: {$now->format('Y-m-d H:i:s T')}");
    $this->info("Current timezone: " . config('app.timezone'));
    
    // Debug: Show all domains with bidding enabled
    $domains = Domain::where('enable_bidding', true)->get();
    $this->info("Found {$domains->count()} domains with bidding enabled:");
    
    foreach ($domains as $domain) {
        $this->info("- Domain: {$domain->full_domain}");
        $this->info("  Status: {$domain->auction_status}");
        $this->info("  Start: " . ($domain->auction_start ? $domain->auction_start->format('Y-m-d H:i:s T') : 'NULL'));
        $this->info("  End: " . ($domain->auction_end ? $domain->auction_end->format('Y-m-d H:i:s T') : 'NULL'));
        
        if ($domain->auction_start && $domain->auction_end) {
            $startPassed = $now->gt($domain->auction_start);
            $endNotReached = $now->lt($domain->auction_end);
            $this->info("  Start passed: " . ($startPassed ? 'YES' : 'NO'));
            $this->info("  End not reached: " . ($endNotReached ? 'YES' : 'NO'));
        }
        $this->info("");
    }
    
    // Update scheduled auctions to active
    $scheduledDomains = Domain::where('enable_bidding', true)
        ->where('auction_status', 'scheduled')
        ->get();
        
    $this->info("Found {$scheduledDomains->count()} scheduled auctions:");
    
    foreach ($scheduledDomains as $domain) {
        $startCondition = $domain->auction_start ? ($now->gte($domain->auction_start) ? 'PASSES' : 'FAILS') : 'NULL';
        $endCondition = $domain->auction_end ? ($now->lt($domain->auction_end) ? 'PASSES' : 'FAILS') : 'NULL';
        
        $this->info("- {$domain->full_domain}: Start condition: {$startCondition}, End condition: {$endCondition}");
    }
    
    $scheduledToActive = Domain::where('enable_bidding', true)
        ->where('auction_status', 'scheduled')
        ->where('auction_start', '<=', $now)
        ->where('auction_end', '>', $now)
        ->update(['auction_status' => 'active']);
        
    $this->info("Updated {$scheduledToActive} auctions from scheduled to active");
    
    // Update active auctions to ended
    $activeToEnded = Domain::where('enable_bidding', true)
        ->where('auction_status', 'active')
        ->where('auction_end', '<=', $now)
        ->update(['auction_status' => 'ended']);
        
    $this->info("Updated {$activeToEnded} auctions from active to ended");
    
    // Update draft auctions to scheduled if they have start/end dates
    $draftToScheduled = Domain::where('enable_bidding', true)
        ->where('auction_status', 'draft')
        ->whereNotNull('auction_start')
        ->whereNotNull('auction_end')
        ->where('auction_start', '>', $now)
        ->update(['auction_status' => 'scheduled']);
        
    $this->info("Updated {$draftToScheduled} auctions from draft to scheduled");
    
    $this->info('Auction statuses updated successfully!');
})->purpose('Update auction statuses based on current time');

// Generate slugs for existing domains
Artisan::command('domains:generate-slugs', function () {
    $domains = \App\Models\Domain::whereNull('slug')->orWhere('slug', '')->get();
    
    if ($domains->isEmpty()) {
        $this->info('All domains already have slugs!');
        return;
    }
    
    $this->info("Found {$domains->count()} domains without slugs. Generating...");
    
    $bar = $this->output->createProgressBar($domains->count());
    $bar->start();
    
    foreach ($domains as $domain) {
        $domain->slug = $domain->generateSlug();
        $domain->save();
        $bar->advance();
    }
    
    $bar->finish();
    $this->newLine();
    $this->info('All domain slugs generated successfully!');
})->purpose('Generate slugs for existing domains');


