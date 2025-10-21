<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DomainDailyMetric extends Model
{
    use HasFactory;

    protected $fillable = [
        'domain_id',
        'metric_date',
        'views',
        'bids',
        'offers',
        'favorites',
        'watchers',
        'unique_visitors',
        'revenue',
    ];

    protected $casts = [
        'metric_date' => 'date',
        'views' => 'integer',
        'bids' => 'integer',
        'offers' => 'integer',
        'favorites' => 'integer',
        'watchers' => 'integer',
        'unique_visitors' => 'integer',
        'revenue' => 'decimal:2',
    ];

    /**
     * Get the domain that owns the metric.
     */
    public function domain(): BelongsTo
    {
        return $this->belongsTo(Domain::class);
    }

    /**
     * Scope to filter by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('metric_date', [$startDate, $endDate]);
    }

    /**
     * Scope to filter by domain
     */
    public function scopeForDomain($query, $domainId)
    {
        return $query->where('domain_id', $domainId);
    }

    /**
     * Get total views for a domain in a date range
     */
    public static function getTotalViews($domainId, $startDate, $endDate)
    {
        return static::forDomain($domainId)
            ->dateRange($startDate, $endDate)
            ->sum('views');
    }

    /**
     * Get total revenue for a domain in a date range
     */
    public static function getTotalRevenue($domainId, $startDate, $endDate)
    {
        return static::forDomain($domainId)
            ->dateRange($startDate, $endDate)
            ->sum('revenue');
    }

    /**
     * Get daily metrics for charts
     */
    public static function getChartData($domainId, $startDate, $endDate)
    {
        return static::forDomain($domainId)
            ->dateRange($startDate, $endDate)
            ->orderBy('metric_date')
            ->get()
            ->map(function ($metric) {
                return [
                    'date' => $metric->metric_date->format('Y-m-d'),
                    'views' => $metric->views,
                    'bids' => $metric->bids,
                    'offers' => $metric->offers,
                    'revenue' => $metric->revenue,
                ];
            });
    }
}
