<?php

namespace App\Services;

use App\Models\Domain;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;

class DomainViewService
{
    /**
     * Record a domain view in Redis for scalable analytics
     */
    public function recordView(Domain $domain, $user = null): void
    {
        try {
            // Only record views for active domains and not by the owner
            if ($domain->status !== 'active' || ($user && $domain->user_id === $user->id)) {
                return;
            }

            $domainId = $domain->id;
            $hourKey = 'domain:views:hour:' . now()->format('YmdH');
            $domainKey = "domain:views:{$domainId}";
            
            // Use Redis pipeline for atomic operations
            Redis::pipeline(function ($pipe) use ($domainKey, $hourKey) {
                // Increment domain-specific counter
                $pipe->incr($domainKey);
                $pipe->expire($domainKey, 86400 * 7); // Keep for 7 days
                
                // Increment hourly global counter
                $pipe->incr($hourKey);
                $pipe->expire($hourKey, 86400 * 30); // Keep for 30 days
            });

            // Record unique visitor if user is logged in
            if ($user) {
                $uniqueKey = "domain:unique_visitors:{$domainId}:" . now()->format('Ymd');
                Redis::sadd($uniqueKey, $user->id);
                Redis::expire($uniqueKey, 86400 * 30); // Keep for 30 days
            }

        } catch (\Exception $e) {
            Log::error('Failed to record domain view', [
                'domain_id' => $domain->id,
                'user_id' => $user?->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get current view count for a domain from Redis
     */
    public function getViewCount(Domain $domain): int
    {
        try {
            return (int) Redis::get("domain:views:{$domain->id}") ?? 0;
        } catch (\Exception $e) {
            Log::error('Failed to get domain view count', [
                'domain_id' => $domain->id,
                'error' => $e->getMessage()
            ]);
            return 0;
        }
    }

    /**
     * Get hourly view counts for aggregation
     */
    public function getHourlyViewCounts(string $hour): array
    {
        try {
            $pattern = "domain:views:hour:{$hour}*";
            $keys = Redis::keys($pattern);
            
            $counts = [];
            foreach ($keys as $key) {
                $counts[$key] = (int) Redis::get($key) ?? 0;
            }
            
            return $counts;
        } catch (\Exception $e) {
            Log::error('Failed to get hourly view counts', [
                'hour' => $hour,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Get domain-specific view counts for aggregation
     */
    public function getDomainViewCounts(): array
    {
        try {
            $pattern = "domain:views:*";
            $keys = Redis::keys($pattern);
            
            $counts = [];
            foreach ($keys as $key) {
                if (strpos($key, 'domain:views:hour:') === false) {
                    $domainId = str_replace('domain:views:', '', $key);
                    $counts[$domainId] = (int) Redis::get($key) ?? 0;
                }
            }
            
            return $counts;
        } catch (\Exception $e) {
            Log::error('Failed to get domain view counts', [
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Clear view counts for a specific domain (used after aggregation)
     */
    public function clearDomainViewCount(Domain $domain): void
    {
        try {
            Redis::del("domain:views:{$domain->id}");
        } catch (\Exception $e) {
            Log::error('Failed to clear domain view count', [
                'domain_id' => $domain->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Clear hourly view counts (used after aggregation)
     */
    public function clearHourlyViewCounts(string $hour): void
    {
        try {
            $pattern = "domain:views:hour:{$hour}*";
            $keys = Redis::keys($pattern);
            
            if (!empty($keys)) {
                Redis::del($keys);
            }
        } catch (\Exception $e) {
            Log::error('Failed to clear hourly view counts', [
                'hour' => $hour,
                'error' => $e->getMessage()
            ]);
        }
    }
}
