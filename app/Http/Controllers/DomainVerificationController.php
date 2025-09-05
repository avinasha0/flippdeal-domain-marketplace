<?php

namespace App\Http\Controllers;

use App\Models\Domain;
use App\Services\DomainVerificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class DomainVerificationController extends Controller
{
    protected DomainVerificationService $verificationService;

    public function __construct(DomainVerificationService $verificationService)
    {
        $this->verificationService = $verificationService;
    }

    /**
     * Show domain verification page.
     */
    public function show(Domain $domain): View
    {
        $this->authorize('view', $domain);

        $instructions = $this->verificationService->getVerificationInstructions($domain);
        
        return view('domains.verification', compact('domain', 'instructions'));
    }

    /**
     * Generate verification record for domain.
     */
    public function generate(Domain $domain): JsonResponse
    {
        $this->authorize('update', $domain);

        try {
            $verification = $this->verificationService->generateVerificationRecord($domain);
            $instructions = $this->verificationService->getVerificationInstructions($domain);

            return response()->json([
                'success' => true,
                'message' => 'Verification record generated successfully',
                'instructions' => $instructions
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate verification record: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify domain ownership.
     */
    public function verify(Domain $domain): JsonResponse
    {
        $this->authorize('update', $domain);

        try {
            $isVerified = $this->verificationService->verifyDomainOwnership($domain);

            if ($isVerified) {
                return response()->json([
                    'success' => true,
                    'message' => 'Domain ownership verified successfully!',
                    'verified' => true
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Domain ownership verification failed. Please check your DNS settings.',
                    'verified' => false
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Verification failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Regenerate verification record.
     */
    public function regenerate(Domain $domain): JsonResponse
    {
        $this->authorize('update', $domain);

        try {
            $verification = $this->verificationService->regenerateVerification($domain);
            $instructions = $this->verificationService->getVerificationInstructions($domain);

            return response()->json([
                'success' => true,
                'message' => 'Verification record regenerated successfully',
                'instructions' => $instructions
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to regenerate verification record: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check verification status.
     */
    public function status(Domain $domain): JsonResponse
    {
        $this->authorize('view', $domain);

        $isVerified = $domain->isVerified();
        $isExpired = $this->verificationService->isVerificationExpired($domain);
        $instructions = $this->verificationService->getVerificationInstructions($domain);

        return response()->json([
            'verified' => $isVerified,
            'expired' => $isExpired,
            'instructions' => $instructions
        ]);
    }

    /**
     * Download verification file for file-based verification.
     */
    public function downloadFile(Domain $domain)
    {
        $this->authorize('view', $domain);

        $instructions = $this->verificationService->getVerificationInstructions($domain);
        
        if (empty($instructions) || !isset($instructions['file_verification'])) {
            return response()->json([
                'success' => false,
                'message' => 'File verification not available for this domain'
            ], 400);
        }

        $fileContent = $instructions['file_verification']['content'];
        $filename = $instructions['file_verification']['filename'];

        return response($fileContent)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Check if domain has active website.
     */
    public function checkWebsiteStatus(Domain $domain): JsonResponse
    {
        $this->authorize('view', $domain);

        try {
            $websiteStatus = $this->verificationService->checkDomainWebsiteStatus($domain->full_domain);
            
            return response()->json([
                'success' => true,
                'website_status' => $websiteStatus
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to check website status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify domain using file method only.
     */
    public function verifyByFile(Domain $domain): JsonResponse
    {
        $this->authorize('update', $domain);

        try {
            $isVerified = $this->verificationService->verifyDomainByFile($domain);

            if ($isVerified) {
                return response()->json([
                    'success' => true,
                    'message' => 'Domain ownership verified successfully using file verification!',
                    'verified' => true
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'File verification failed. Please ensure the verification file is uploaded correctly.',
                    'verified' => false
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'File verification failed: ' . $e->getMessage()
            ], 500);
        }
    }
}