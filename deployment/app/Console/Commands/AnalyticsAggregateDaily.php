<?php

namespace App\Console\Commands;

use App\Models\DomainDailyMetric;
use App\Models\Domain;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsAggregateDaily extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'analytics:aggregate-daily 
                            {--date= : Specific date to aggregate (Y-m-d format)}
                            {--force : Force aggregation even if already processed}
                            {--backfill : Backfill missing daily metrics from hourly data}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Aggregate daily metrics from hourly data and other sources';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $targetDate = $this->option('date') ?? now()->subDay()->format('Y-m-d');
        $force = $this->option('force');
        $backfill = $this->option('backfill');

        $this->info("Starting daily aggregation for: {$targetDate}");

        try {
            // Validate date format
            $parsedDate = Carbon::parse($targetDate);
            if (!$parsedDate->isValid()) {
                $this->error('Invalid date format. Use Y-m-d format.');
                return 1;
            }

            // Check if already processed (unless forced)
            if (!$force && $this->isAlreadyProcessed($parsedDate)) {
                $this->warn("Daily aggregation for {$targetDate} already processed. Use --force to reprocess.");
                return 0;
            }

            if ($backfill) {
                $this->backfillDailyMetrics($parsedDate);
            } else {
                $this->aggregateDailyMetrics($parsedDate);
            }

            $this->info("Daily aggregation completed for: {$targetDate}");
            return 0;

        } catch (\Exception $e) {
            $this->error("Failed to complete daily aggregation: {$e->getMessage()}");
            return 1;
        }
    }

    /**
     * Aggregate daily metrics from various sources
     */
    protected function aggregateDailyMetrics(Carbon $date)
    {
        $this->info("Aggregating daily metrics for {$date->format('Y-m-d')}...");

        $startOfDay = $date->copy()->startOfDay();
        $endOfDay = $date->copy()->endOfDay();

        // Get all domains that had activity on this date
        $domains = Domain::whereHas('dailyMetrics', function ($query) use ($startOfDay, $endOfDay) {
            $query->whereBetween('metric_date', [$startOfDay, $endOfDay]);
        })->orWhereHas('bids', function ($query) use ($startOfDay, $endOfDay) {
            $query->whereBetween('created_at', [$startOfDay, $endOfDay]);
        })->orWhereHas('offers', function ($query) use ($startOfDay, $endOfDay) {
            $query->whereBetween('created_at', [$startOfDay, $endOfDay]);
        })->get();

        $progressBar = $this->output->createProgressBar($domains->count());
        $progressBar->start();

        foreach ($domains as $domain) {
            $this->aggregateDomainDailyMetrics($domain, $date);
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();
    }

    /**
     * Backfill daily metrics from hourly data
     */
    protected function backfillDailyMetrics(Carbon $date)
    {
        $this->info("Backfilling daily metrics for {$date->format('Y-m-d')}...");

        // This would aggregate from hourly Redis data if available
        // For now, we'll just run the normal aggregation
        $this->aggregateDailyMetrics($date);
    }

    /**
     * Aggregate daily metrics for a specific domain
     */
    protected function aggregateDomainDailyMetrics(Domain $domain, Carbon $date)
    {
        $startOfDay = $date->copy()->startOfDay();
        $endOfDay = $date->copy()->endOfDay();

        // Get or create daily metric record
        $dailyMetric = DomainDailyMetric::firstOrNew([
            'domain_id' => $domain->id,
            'metric_date' => $date->format('Y-m-d'),
        ]);

        // Aggregate views from hourly data
        $hourlyMetrics = DomainDailyMetric::where('domain_id', $domain->id)
            ->whereBetween('metric_date', [$startOfDay, $endOfDay])
            ->get();

        $totalViews = $hourlyMetrics->sum('views');
        $totalUniqueVisitors = $hourlyMetrics->sum('unique_visitors');

        // Aggregate bids
        $bidsCount = $domain->bids()
            ->whereBetween('created_at', [$startOfDay, $endOfDay])
            ->count();

        // Aggregate offers
        $offersCount = $domain->offers()
            ->whereBetween('created_at', [$startOfDay, $endOfDay])
            ->count();

        // Aggregate favorites/watchers
        $favoritesCount = $domain->watchlists()
            ->whereBetween('created_at', [$startOfDay, $endOfDay])
            ->count();

        // Get current watchers count
        $watchersCount = $domain->watchlists()->count();

        // Calculate revenue (from completed transactions)
        $revenue = $domain->transactions()
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startOfDay, $endOfDay])
            ->sum('amount');

        // Update daily metric
        $dailyMetric->fill([
            'views' => $totalViews,
            'bids' => $bidsCount,
            'offers' => $offersCount,
            'favorites' => $favoritesCount,
            'watchers' => $watchersCount,
            'unique_visitors' => $totalUniqueVisitors,
            'revenue' => $revenue,
        ]);

        $dailyMetric->save();
    }

    /**
     * Check if the date has already been processed
     */
    protected function isAlreadyProcessed(Carbon $date): bool
    {
        return DomainDailyMetric::where('metric_date', $date->format('Y-m-d'))
            ->where('updated_at', '>', $date->endOfDay())
            ->exists();
    }
}