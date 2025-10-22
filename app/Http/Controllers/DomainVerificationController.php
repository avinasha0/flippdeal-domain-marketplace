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

        // Auto-generate verification record if none exists
        $verification = $this->verificationService->getVerificationRecord($domain);
        if (!$verification) {
            $this->verificationService->generateVerificationRecord($domain);
        }

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
            // Check if verification record exists
            $verification = $this->verificationService->getVerificationRecord($domain);
            
            if (!$verification) {
                return response()->json([
                    'success' => false,
                    'message' => 'No verification record found. Please generate a verification record first.',
                    'verified' => false
                ]);
            }

            $isVerified = $this->verificationService->verifyDomainOwnership($domain);

            if ($isVerified) {
                return response()->json([
                    'success' => true,
                    'message' => 'Domain ownership verified successfully!',
                    'verified' => true
                ]);
            } else {
                $method = $verification->method === 'file_upload' ? 'file upload' : 'DNS';
                return response()->json([
                    'success' => false,
                    'message' => "Domain verification failed. Please ensure your {$method} verification is set up correctly.",
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
     * Verify domain via file upload method.
     */
    public function verifyFile(Domain $domain): JsonResponse
    {
        $this->authorize('update', $domain);

        try {
            // Check if file verification is available
            $instructions = $this->verificationService->getVerificationInstructions($domain);
            
            if (!isset($instructions['file_verification'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'File verification is not available for this domain. Please use DNS verification method instead.',
                    'verified' => false
                ]);
            }

            $isVerified = $this->verificationService->verifyDomainByFile($domain);

            if ($isVerified) {
                return response()->json([
                    'success' => true,
                    'message' => 'Domain ownership verified successfully via file upload!',
                    'verified' => true
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'File verification failed. Please ensure the verification file is uploaded correctly to: ' . $instructions['file_verification']['url'],
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

    /**
     * Download verification file.
     */
    public function downloadFile(Domain $domain)
    {
        $this->authorize('view', $domain);

        try {
            $verification = $this->verificationService->getVerificationRecord($domain);
            
            if (!$verification) {
                // Auto-generate verification record if none exists
                $verification = $this->verificationService->generateVerificationRecord($domain);
            }

            // Generate filename based on verification method
            if ($verification->method === 'file_upload') {
                $filename = 'verification.txt';
                $content = $verification->token;
                $contentType = 'text/plain';
            } else {
                // For DNS verification, still provide a file for manual verification
                $filename = 'verification.txt';
                $content = $verification->token;
                $contentType = 'text/plain';
            }

            return response($content)
                ->header('Content-Type', $contentType)
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to download verification file: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * Switch verification method.
     */
    public function switchMethod(Domain $domain, Request $request): JsonResponse
    {
        $this->authorize('update', $domain);

        $method = $request->input('method', 'dns_txt');
        
        if (!in_array($method, ['dns_txt', 'file_upload'])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid verification method.'
            ], 400);
        }

        try {
            // Delete existing verification records
            $this->verificationService->regenerateVerification($domain);
            
            // Create new verification with specified method
            $verification = $this->verificationService->generateVerificationRecord($domain);
            
            // Override method if needed
            if ($method !== $verification->method) {
                $verification->update(['method' => $method]);
            }
            
            $instructions = $this->verificationService->getVerificationInstructions($domain);

            return response()->json([
                'success' => true,
                'message' => 'Verification method switched successfully',
                'instructions' => $instructions
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to switch verification method: ' . $e->getMessage()
            ], 500);
        }
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