<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FileUpload extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'domain_id',
        'path',
        'original_name',
        'mime_type',
        'size',
        'scan_status',
        'scan_report',
        'storage_disk',
    ];

    protected $casts = [
        'scan_report' => 'array',
    ];

    /**
     * Get the user that owns the file upload.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the domain associated with the file upload.
     */
    public function domain(): BelongsTo
    {
        return $this->belongsTo(Domain::class);
    }

    /**
     * Scope to filter by scan status
     */
    public function scopeScanStatus($query, $status)
    {
        return $query->where('scan_status', $status);
    }

    /**
     * Scope to filter by user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to filter by domain
     */
    public function scopeForDomain($query, $domainId)
    {
        return $query->where('domain_id', $domainId);
    }

    /**
     * Scope to filter pending scans
     */
    public function scopePendingScan($query)
    {
        return $query->where('scan_status', 'pending');
    }

    /**
     * Scope to filter clean files
     */
    public function scopeClean($query)
    {
        return $query->where('scan_status', 'clean');
    }

    /**
     * Scope to filter infected files
     */
    public function scopeInfected($query)
    {
        return $query->where('scan_status', 'infected');
    }

    /**
     * Scope to filter files with scan errors
     */
    public function scopeScanError($query)
    {
        return $query->where('scan_status', 'error');
    }

    /**
     * Scope to filter files needing review
     */
    public function scopeNeedsReview($query)
    {
        return $query->whereIn('scan_status', ['infected', 'error']);
    }

    /**
     * Check if file is pending scan
     */
    public function isPendingScan(): bool
    {
        return $this->scan_status === 'pending';
    }

    /**
     * Check if file is clean
     */
    public function isClean(): bool
    {
        return $this->scan_status === 'clean';
    }

    /**
     * Check if file is infected
     */
    public function isInfected(): bool
    {
        return $this->scan_status === 'infected';
    }

    /**
     * Check if file has scan error
     */
    public function hasScanError(): bool
    {
        return $this->scan_status === 'error';
    }

    /**
     * Check if file needs review
     */
    public function needsReview(): bool
    {
        return in_array($this->scan_status, ['infected', 'error']);
    }

    /**
     * Get the scan status color
     */
    public function getScanStatusColorAttribute(): string
    {
        return match ($this->scan_status) {
            'pending' => 'yellow',
            'clean' => 'green',
            'infected' => 'red',
            'error' => 'orange',
            default => 'gray',
        };
    }

    /**
     * Get the scan status label
     */
    public function getScanStatusLabelAttribute(): string
    {
        return match ($this->scan_status) {
            'pending' => 'Pending Scan',
            'clean' => 'Clean',
            'infected' => 'Infected',
            'error' => 'Scan Error',
            default => 'Unknown',
        };
    }

    /**
     * Get formatted file size
     */
    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get file extension
     */
    public function getExtensionAttribute(): string
    {
        return pathinfo($this->original_name, PATHINFO_EXTENSION);
    }

    /**
     * Get file type category
     */
    public function getFileTypeCategoryAttribute(): string
    {
        if (str_starts_with($this->mime_type, 'image/')) {
            return 'image';
        }
        
        if ($this->mime_type === 'application/pdf') {
            return 'pdf';
        }
        
        return 'other';
    }

    /**
     * Get threats from scan report
     */
    public function getThreatsAttribute(): array
    {
        return $this->scan_report['threats'] ?? [];
    }

    /**
     * Get scan error message
     */
    public function getScanErrorMessageAttribute(): ?string
    {
        return $this->scan_report['error'] ?? null;
    }

    /**
     * Get scan time
     */
    public function getScanTimeAttribute(): ?float
    {
        return $this->scan_report['scan_time'] ?? null;
    }

    /**
     * Get scan date
     */
    public function getScannedAtAttribute(): ?string
    {
        return $this->scan_report['scanned_at'] ?? null;
    }

    /**
     * Get scanner version
     */
    public function getScannerVersionAttribute(): ?string
    {
        return $this->scan_report['scanner_version'] ?? null;
    }

    /**
     * Check if file is quarantined
     */
    public function isQuarantined(): bool
    {
        return $this->scan_report['quarantined'] ?? false;
    }

    /**
     * Get quarantine reason
     */
    public function getQuarantineReasonAttribute(): ?string
    {
        return $this->scan_report['reason'] ?? null;
    }

    /**
     * Get quarantine date
     */
    public function getQuarantinedAtAttribute(): ?string
    {
        return $this->scan_report['quarantined_at'] ?? null;
    }

    /**
     * Get file age in days
     */
    public function getAgeInDaysAttribute(): int
    {
        return $this->created_at->diffInDays(now());
    }

    /**
     * Check if file is recent (within 24 hours)
     */
    public function isRecent(): bool
    {
        return $this->created_at->isAfter(now()->subDay());
    }

    /**
     * Get storage URL (signed URL)
     */
    public function getStorageUrlAttribute(): string
    {
        try {
            return \Storage::disk($this->storage_disk)->temporaryUrl(
                $this->path,
                now()->addMinutes(60)
            );
        } catch (\Exception $e) {
            return '';
        }
    }
}
