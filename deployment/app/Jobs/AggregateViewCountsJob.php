<?php

namespace App\Jobs;

use App\Models\Domain;
use App\Models\DomainDailyMetric;
use App\Services\DomainViewService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Carbon\Carbon;

class AggregateViewCountsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300; // 5 minutes
    public $tries = 3;
    public $backoff = [60, 120, 300]; // Retry after 1, 2, 5 minutes

    protected $targetDate;
    protected $domainViewService;

    /**
     * Create a new job instance.
     */
    public function __construct(string $targetDate = null)
    {
        $this->targetDate = $targetDate ?? now()->subHour()->format('Y-m-d H:00:00');
        $this->domainViewService = app(DomainViewService::class);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info('Starting view count aggregation', ['target_date' => $this->targetDate]);

            $targetDateTime = Carbon::parse($this->targetDate);
            $hourKey = $targetDateTime->format('YmdH');

            // Get all domain view counts from Redis
            $domainViewCounts = $this->domainViewService->getDomainViewCounts();
            
            if (empty($domainViewCounts)) {
                Log::info('No domain view counts found for aggregation');
                return;
            }

            $aggregatedData = [];
            $processedDomains = [];

            foreach ($domainViewCounts as $domainId => $viewCount) {
                if ($viewCount <= 0) {
                    continue;
                }

                $domain = Domain::find($domainId);
                if (!$domain) {
                    Log::warning('Domain not found for aggregation', ['domain_id' => $domainId]);
                    continue;
                }

                $date = $targetDateTime->format('Y-m-d');
                
                // Get or create daily metric record
                $dailyMetric = DomainDailyMetric::firstOrNew([
                    'domain_id' => $domainId,
                    'metric_date' => $date,
                ]);

                // Update view count
                $dailyMetric->views += $viewCount;
                $dailyMetric->save();

                $aggregatedData[] = [
                    'domain_id' => $domainId,
                    'views' => $viewCount,
                ];

                $processedDomains[] = $domainId;

                Log::debug('Aggregated view count', [
                    'domain_id' => $domainId,
                    'views' => $viewCount,
                    'date' => $date,
                ]);
            }

            // Clear Redis counters for processed domains using Lua script for atomicity
            if (!empty($processedDomains)) {
                $this->clearRedisCounters($processedDomains);
            }

            // Clear hourly counters
            $this->domainViewService->clearHourlyViewCounts($hourKey);

            Log::info('View count aggregation completed', [
                'processed_domains' => count($processedDomains),
                'total_views' => array_sum(array_column($aggregatedData, 'views')),
            ]);

        } catch (\Exception $e) {
            Log::error('View count aggregation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Clear Redis counters for processed domains
     */
    protected function clearRedisCounters(array $domainIds): void
    {
        try {
            $keys = array_map(fn($id) => "domain:views:{$id}", $domainIds);
            
            if (!empty($keys)) {
                Redis::del($keys);
            }
        } catch (\Exception $e) {
            Log::error('Failed to clear Redis counters', [
                'domain_ids' => $domainIds,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('AggregateViewCountsJob failed permanently', [
            'target_date' => $this->targetDate,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}