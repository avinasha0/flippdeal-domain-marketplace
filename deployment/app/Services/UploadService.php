<?php

namespace App\Services;

use App\Models\FileUpload;
use App\Models\User;
use App\Models\Domain;
use App\Services\AuditService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class UploadService
{
    protected $auditService;
    protected $virusScanner;
    protected $maxSize;
    protected $allowedMimes;

    public function __construct(AuditService $auditService, VirusScannerInterface $virusScanner)
    {
        $this->auditService = $auditService;
        $this->virusScanner = $virusScanner;
        $this->maxSize = config('upload.max_size_mb', 10) * 1024 * 1024; // Convert to bytes
        $this->allowedMimes = config('upload.allowed_mimes', [
            'image/jpeg',
            'image/png',
            'image/gif',
            'application/pdf',
        ]);
    }

    /**
     * Upload and process a file
     */
    public function uploadFile(UploadedFile $file, User $user, ?Domain $domain = null, string $purpose = 'verification'): array
    {
        try {
            // Validate file
            $validation = $this->validateFile($file);
            if (!$validation['valid']) {
                return $validation;
            }

            // Generate secure filename and path
            $filename = $this->generateSecureFilename($file);
            $path = $this->generateSecurePath($user, $domain, $filename);

            // Process file (strip EXIF, resize if needed)
            $processedFile = $this->processFile($file, $path);

            // Store file in private storage
            $stored = Storage::disk('s3')->put($path, $processedFile);
            
            if (!$stored) {
                return ['success' => false, 'message' => 'Failed to store file'];
            }

            // Create file upload record
            $fileUpload = FileUpload::create([
                'user_id' => $user->id,
                'domain_id' => $domain?->id,
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'scan_status' => 'pending',
                'storage_disk' => 's3',
            ]);

            // Dispatch virus scan job
            \App\Jobs\ScanUploadedFileJob::dispatch($fileUpload);

            // Log the upload
            $this->auditService->logFileUpload($user, 'uploaded', [
                'file_upload_id' => $fileUpload->id,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'purpose' => $purpose,
                'domain_id' => $domain?->id,
            ]);

            return [
                'success' => true,
                'file_upload' => $fileUpload,
                'signed_url' => $this->generateSignedUrl($fileUpload),
            ];

        } catch (\Exception $e) {
            Log::error('File upload failed', [
                'user_id' => $user->id,
                'domain_id' => $domain?->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => 'Upload failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Validate uploaded file
     */
    protected function validateFile(UploadedFile $file): array
    {
        // Check file size
        if ($file->getSize() > $this->maxSize) {
            return [
                'valid' => false,
                'message' => "File size exceeds maximum allowed size of " . ($this->maxSize / 1024 / 1024) . "MB",
            ];
        }

        // Check MIME type
        if (!in_array($file->getMimeType(), $this->allowedMimes)) {
            return [
                'valid' => false,
                'message' => "File type not allowed. Allowed types: " . implode(', ', $this->allowedMimes),
            ];
        }

        // Check file extension
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
        $extension = strtolower($file->getClientOriginalExtension());
        
        if (!in_array($extension, $allowedExtensions)) {
            return [
                'valid' => false,
                'message' => "File extension not allowed. Allowed extensions: " . implode(', ', $allowedExtensions),
            ];
        }

        // Check for malicious file types
        if (in_array($extension, ['html', 'htm', 'js', 'php', 'exe', 'bat', 'cmd', 'scr'])) {
            return [
                'valid' => false,
                'message' => "Potentially malicious file type not allowed",
            ];
        }

        return ['valid' => true];
    }

    /**
     * Generate secure filename
     */
    protected function generateSecureFilename(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        $timestamp = now()->format('Y-m-d_H-i-s');
        $random = Str::random(16);
        
        return "{$timestamp}_{$random}.{$extension}";
    }

    /**
     * Generate secure storage path
     */
    protected function generateSecurePath(User $user, ?Domain $domain, string $filename): string
    {
        $basePath = "user_{$user->id}";
        
        if ($domain) {
            $basePath .= "/domain_{$domain->id}";
        }
        
        $basePath .= "/" . now()->format('Y/m/d');
        
        return "{$basePath}/{$filename}";
    }

    /**
     * Process file (strip EXIF, resize if needed)
     */
    protected function processFile(UploadedFile $file, string $path): string
    {
        $mimeType = $file->getMimeType();
        
        // Handle images
        if (str_starts_with($mimeType, 'image/')) {
            return $this->processImage($file);
        }
        
        // Handle PDFs (no processing needed)
        if ($mimeType === 'application/pdf') {
            return file_get_contents($file->getPathname());
        }
        
        // For other file types, return as-is
        return file_get_contents($file->getPathname());
    }

    /**
     * Process image file (strip EXIF, resize if needed)
     */
    protected function processImage(UploadedFile $file): string
    {
        try {
            $image = Image::make($file->getPathname());
            
            // Strip EXIF data
            $image->orientate();
            
            // Resize if too large (max 2048x2048)
            if ($image->width() > 2048 || $image->height() > 2048) {
                $image->resize(2048, 2048, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            }
            
            // Encode with quality settings
            return $image->encode(null, 85)->encoded;
            
        } catch (\Exception $e) {
            Log::warning('Image processing failed, using original', [
                'error' => $e->getMessage(),
                'file' => $file->getClientOriginalName(),
            ]);
            
            return file_get_contents($file->getPathname());
        }
    }

    /**
     * Generate signed URL for file access
     */
    public function generateSignedUrl(FileUpload $fileUpload, int $expirationMinutes = 60): string
    {
        try {
            return Storage::disk('s3')->temporaryUrl(
                $fileUpload->path,
                now()->addMinutes($expirationMinutes)
            );
        } catch (\Exception $e) {
            Log::error('Failed to generate signed URL', [
                'file_upload_id' => $fileUpload->id,
                'error' => $e->getMessage(),
            ]);
            
            return '';
        }
    }

    /**
     * Get file upload by ID with signed URL
     */
    public function getFileWithSignedUrl(int $fileUploadId, int $expirationMinutes = 60): ?array
    {
        $fileUpload = FileUpload::find($fileUploadId);
        
        if (!$fileUpload) {
            return null;
        }

        return [
            'file_upload' => $fileUpload,
            'signed_url' => $this->generateSignedUrl($fileUpload, $expirationMinutes),
        ];
    }

    /**
     * Quarantine infected file
     */
    public function quarantineFile(FileUpload $fileUpload, string $reason): void
    {
        try {
            // Move file to quarantine folder
            $quarantinePath = "quarantine/" . $fileUpload->path;
            Storage::disk('s3')->move($fileUpload->path, $quarantinePath);
            
            // Update file upload record
            $fileUpload->update([
                'path' => $quarantinePath,
                'scan_status' => 'infected',
                'scan_report' => [
                    'quarantined' => true,
                    'reason' => $reason,
                    'quarantined_at' => now()->toISOString(),
                ],
            ]);

            // Log the quarantine
            $this->auditService->logFileUpload(
                $fileUpload->user,
                'quarantined',
                [
                    'file_upload_id' => $fileUpload->id,
                    'reason' => $reason,
                ]
            );

        } catch (\Exception $e) {
            Log::error('Failed to quarantine file', [
                'file_upload_id' => $fileUpload->id,
                'reason' => $reason,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Delete file and record
     */
    public function deleteFile(FileUpload $fileUpload): bool
    {
        try {
            // Delete from storage
            Storage::disk('s3')->delete($fileUpload->path);
            
            // Delete record
            $fileUpload->delete();

            // Log the deletion
            $this->auditService->logFileUpload(
                $fileUpload->user,
                'deleted',
                [
                    'file_upload_id' => $fileUpload->id,
                    'original_name' => $fileUpload->original_name,
                ]
            );

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to delete file', [
                'file_upload_id' => $fileUpload->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Get file uploads for user
     */
    public function getUserFiles(User $user, ?Domain $domain = null, int $perPage = 20)
    {
        $query = FileUpload::where('user_id', $user->id);
        
        if ($domain) {
            $query->where('domain_id', $domain->id);
        }
        
        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Get file uploads needing admin review
     */
    public function getFilesNeedingReview(int $perPage = 20)
    {
        return FileUpload::where('scan_status', 'infected')
            ->orWhere('scan_status', 'error')
            ->with(['user', 'domain'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
}
