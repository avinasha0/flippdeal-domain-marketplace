<?php

namespace App\Jobs;

use App\Models\FileUpload;
use App\Services\UploadService;
use App\Services\AuditService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ScanUploadedFileJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300; // 5 minutes
    public $tries = 3;
    public $backoff = [60, 120, 300]; // Retry after 1, 2, 5 minutes

    protected $fileUploadId;
    protected $virusScanner;
    protected $uploadService;
    protected $auditService;

    /**
     * Create a new job instance.
     */
    public function __construct(int $fileUploadId)
    {
        $this->fileUploadId = $fileUploadId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $fileUpload = FileUpload::find($this->fileUploadId);
            
            if (!$fileUpload) {
                Log::warning('File upload not found for scanning', [
                    'file_upload_id' => $this->fileUploadId,
                ]);
                return;
            }

            if ($fileUpload->scan_status !== 'pending') {
                Log::info('File already scanned or in progress', [
                    'file_upload_id' => $this->fileUploadId,
                    'current_status' => $fileUpload->scan_status,
                ]);
                return;
            }

            Log::info('Starting virus scan', [
                'file_upload_id' => $this->fileUploadId,
                'file_path' => $fileUpload->path,
            ]);

            // Get virus scanner
            $virusScanner = app(\App\Contracts\VirusScannerInterface::class);
            $uploadService = app(UploadService::class);
            $auditService = app(AuditService::class);

            // Check if file exists in storage
            if (!Storage::disk('s3')->exists($fileUpload->path)) {
                $fileUpload->update([
                    'scan_status' => 'error',
                    'scan_report' => [
                        'error' => 'File not found in storage',
                        'scanned_at' => now()->toISOString(),
                    ],
                ]);

                $auditService->logFileUpload(
                    $fileUpload->user,
                    'scan_failed',
                    [
                        'file_upload_id' => $fileUpload->id,
                        'reason' => 'File not found in storage',
                    ]
                );

                return;
            }

            // Download file to temporary location for scanning
            $tempPath = $this->downloadFileForScanning($fileUpload);

            if (!$tempPath) {
                $fileUpload->update([
                    'scan_status' => 'error',
                    'scan_report' => [
                        'error' => 'Failed to download file for scanning',
                        'scanned_at' => now()->toISOString(),
                    ],
                ]);

                return;
            }

            // Perform virus scan
            $scanResult = $virusScanner->scanFile($tempPath);

            // Clean up temporary file
            $this->cleanupTempFile($tempPath);

            // Update file upload record
            $fileUpload->update([
                'scan_status' => $scanResult['infected'] ? 'infected' : ($scanResult['clean'] ? 'clean' : 'error'),
                'scan_report' => array_merge($scanResult, [
                    'scanned_at' => now()->toISOString(),
                    'scanner_version' => $virusScanner->getVersion(),
                ]),
            ]);

            // Handle infected files
            if ($scanResult['infected']) {
                $uploadService->quarantineFile($fileUpload, 'Virus detected: ' . implode(', ', $scanResult['threats']));
                
                // Notify admin and user
                $this->notifyInfectedFile($fileUpload, $scanResult);
            }

            // Log scan completion
            $auditService->logFileUpload(
                $fileUpload->user,
                'scanned',
                [
                    'file_upload_id' => $fileUpload->id,
                    'scan_status' => $fileUpload->scan_status,
                    'threats' => $scanResult['threats'] ?? [],
                ]
            );

            Log::info('Virus scan completed', [
                'file_upload_id' => $this->fileUploadId,
                'scan_status' => $fileUpload->scan_status,
                'threats' => $scanResult['threats'] ?? [],
            ]);

        } catch (\Exception $e) {
            Log::error('Virus scan job failed', [
                'file_upload_id' => $this->fileUploadId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Update file upload as error
            $fileUpload = FileUpload::find($this->fileUploadId);
            if ($fileUpload) {
                $fileUpload->update([
                    'scan_status' => 'error',
                    'scan_report' => [
                        'error' => 'Scan job failed: ' . $e->getMessage(),
                        'scanned_at' => now()->toISOString(),
                    ],
                ]);
            }

            throw $e;
        }
    }

    /**
     * Download file to temporary location for scanning
     */
    protected function downloadFileForScanning(FileUpload $fileUpload): ?string
    {
        try {
            $tempPath = sys_get_temp_dir() . '/' . uniqid('scan_') . '_' . basename($fileUpload->path);
            
            $fileContent = Storage::disk('s3')->get($fileUpload->path);
            
            if (file_put_contents($tempPath, $fileContent) === false) {
                return null;
            }

            return $tempPath;

        } catch (\Exception $e) {
            Log::error('Failed to download file for scanning', [
                'file_upload_id' => $fileUpload->id,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Clean up temporary file
     */
    protected function cleanupTempFile(string $tempPath): void
    {
        try {
            if (file_exists($tempPath)) {
                unlink($tempPath);
            }
        } catch (\Exception $e) {
            Log::warning('Failed to cleanup temporary file', [
                'temp_path' => $tempPath,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Notify about infected file
     */
    protected function notifyInfectedFile(FileUpload $fileUpload, array $scanResult): void
    {
        try {
            // Notify user
            $fileUpload->user->notify(new \App\Notifications\FileInfectedNotification($fileUpload, $scanResult));

            // Notify admins
            // This would dispatch a notification job to notify all admins
            \App\Jobs\NotifyAdminsJob::dispatch('file_infected', [
                'file_upload_id' => $fileUpload->id,
                'user_id' => $fileUpload->user_id,
                'threats' => $scanResult['threats'] ?? [],
                'file_name' => $fileUpload->original_name,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to notify about infected file', [
                'file_upload_id' => $fileUpload->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('ScanUploadedFileJob failed permanently', [
            'file_upload_id' => $this->fileUploadId,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);

        // Update file upload as error
        $fileUpload = FileUpload::find($this->fileUploadId);
        if ($fileUpload) {
            $fileUpload->update([
                'scan_status' => 'error',
                'scan_report' => [
                    'error' => 'Scan job failed permanently: ' . $exception->getMessage(),
                    'failed_at' => now()->toISOString(),
                ],
            ]);
        }
    }
}