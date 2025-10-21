<?php

namespace App\Console\Commands;

use App\Jobs\AggregateViewCountsJob;
use Illuminate\Console\Command;
use Carbon\Carbon;

class AnalyticsAggregateHourly extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'analytics:aggregate-hourly 
                            {--date= : Specific date to aggregate (Y-m-d H:00:00 format)}
                            {--force : Force aggregation even if already processed}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Aggregate hourly view counts from Redis to database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $targetDate = $this->option('date') ?? now()->subHour()->format('Y-m-d H:00:00');
        $force = $this->option('force');

        $this->info("Starting hourly aggregation for: {$targetDate}");

        try {
            // Validate date format
            $parsedDate = Carbon::parse($targetDate);
            if (!$parsedDate->isValid()) {
                $this->error('Invalid date format. Use Y-m-d H:00:00 format.');
                return 1;
            }

            // Check if already processed (unless forced)
            if (!$force && $this->isAlreadyProcessed($parsedDate)) {
                $this->warn("Hourly aggregation for {$targetDate} already processed. Use --force to reprocess.");
                return 0;
            }

            // Dispatch the aggregation job
            AggregateViewCountsJob::dispatch($targetDate);

            $this->info("Hourly aggregation job dispatched for: {$targetDate}");
            $this->info("Check queue status with: php artisan queue:work");

            return 0;

        } catch (\Exception $e) {
            $this->error("Failed to start hourly aggregation: {$e->getMessage()}");
            return 1;
        }
    }

    /**
     * Check if the hour has already been processed
     */
    protected function isAlreadyProcessed(Carbon $date): bool
    {
        // This would check if the hour has already been processed
        // For now, we'll always allow processing
        return false;
    }
}