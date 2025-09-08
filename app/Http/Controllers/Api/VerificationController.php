<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Domain;
use App\Services\DomainVerificationService;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

class VerificationController extends Controller
{
    protected $verificationService;
    protected $auditService;

    public function __construct(DomainVerificationService $verificationService, AuditService $auditService)
    {
        $this->verificationService = $verificationService;
        $this->auditService = $auditService;
    }

    /**
     * Get verification status for a domain
     */
    public function getStatus(Domain $domain): JsonResponse
    {
        $user = Auth::user();
        
        // Check if user can view this domain
        if (!$user->can('view', $domain)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $verification = $domain->verifications()->latest()->first();
        
        $stepperData = [
            'current_step' => $this->getCurrentStep($domain, $verification),
            'steps' => $this->getStepsData($domain, $verification),
            'can_retry' => $verification ? $verification->canRetry() : false,
            'retry_after' => $this->getRetryAfter($domain),
        ];

        return response()->json($stepperData);
    }

    /**
     * Retry verification for a domain
     */
    public function retry(Domain $domain): JsonResponse
    {
        $user = Auth::user();
        
        // Check if user can modify this domain
        if (!$user->can('update', $domain)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Rate limiting
        $key = "verification_retry:domain:{$domain->id}:user:{$user->id}";
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $retryAfter = RateLimiter::availableIn($key);
            return response()->json([
                'error' => 'Too many retry attempts',
                'retry_after' => $retryAfter
            ], 429);
        }

        RateLimiter::hit($key, 300); // 5 minutes

        $verification = $domain->verifications()->latest()->first();
        
        if (!$verification) {
            return response()->json(['error' => 'No verification found'], 404);
        }

        if (!$verification->canRetry()) {
            return response()->json(['error' => 'Verification cannot be retried'], 400);
        }

        // Retry verification
        $result = $this->verificationService->checkDnsVerification($verification);

        // Log the retry attempt
        $this->auditService->log($user, 'domain.verification.retry', $domain, [
            'verification_id' => $verification->id,
            'result' => $result,
        ]);

        return response()->json([
            'success' => true,
            'result' => $result,
            'verification' => $verification->fresh(),
        ]);
    }

    /**
     * Create new verification for a domain
     */
    public function create(Request $request, Domain $domain): JsonResponse
    {
        $user = Auth::user();
        
        // Check if user can modify this domain
        if (!$user->can('update', $domain)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Rate limiting
        if ($this->verificationService->isRateLimited($domain, $user)) {
            return response()->json(['error' => 'Rate limit exceeded'], 429);
        }

        $request->validate([
            'method' => 'required|in:dns_txt,dns_cname,file_upload,whois',
        ]);

        $method = $request->input('method');

        try {
            if ($method === 'dns_txt') {
                $result = $this->verificationService->createDnsVerification($domain, $user);
            } elseif ($method === 'whois') {
                $result = $this->verificationService->checkWhoisVerification($domain, $user);
            } else {
                return response()->json(['error' => 'Method not implemented'], 400);
            }

            if ($result['success']) {
                return response()->json($result);
            } else {
                return response()->json(['error' => $result['message']], 400);
            }

        } catch (\Exception $e) {
            return response()->json(['error' => 'Verification failed'], 500);
        }
    }

    /**
     * Get current step in verification process
     */
    protected function getCurrentStep(Domain $domain, $verification): string
    {
        if (!$verification) {
            return 'add_txt';
        }

        if ($verification->status === 'verified') {
            if ($domain->status === 'active') {
                return 'publish';
            } else {
                return 'admin_approve';
            }
        } elseif ($verification->status === 'needs_admin') {
            return 'admin_approve';
        } else {
            return 'add_txt';
        }
    }

    /**
     * Get steps data for stepper
     */
    protected function getStepsData(Domain $domain, $verification): array
    {
        $steps = [
            'add_txt' => [
                'title' => 'Add TXT Record',
                'status' => 'pending',
                'timestamp' => null,
            ],
            'verified' => [
                'title' => 'Verified',
                'status' => 'pending',
                'timestamp' => null,
            ],
            'admin_approve' => [
                'title' => 'Admin Approve',
                'status' => 'pending',
                'timestamp' => null,
            ],
            'publish' => [
                'title' => 'Publish',
                'status' => 'pending',
                'timestamp' => null,
            ],
        ];

        if ($verification) {
            if ($verification->status === 'verified') {
                $steps['add_txt']['status'] = 'completed';
                $steps['verified']['status'] = 'completed';
                $steps['verified']['timestamp'] = $verification->updated_at->toISOString();
                
                if ($domain->status === 'active') {
                    $steps['admin_approve']['status'] = 'completed';
                    $steps['publish']['status'] = 'completed';
                    $steps['publish']['timestamp'] = $domain->published_at ?? $domain->updated_at->toISOString();
                } else {
                    $steps['admin_approve']['status'] = 'current';
                }
            } elseif ($verification->status === 'needs_admin') {
                $steps['add_txt']['status'] = 'completed';
                $steps['verified']['status'] = 'completed';
                $steps['admin_approve']['status'] = 'current';
            } else {
                $steps['add_txt']['status'] = 'current';
            }
        } else {
            $steps['add_txt']['status'] = 'current';
        }

        return $steps;
    }

    /**
     * Get retry after time
     */
    protected function getRetryAfter(Domain $domain): ?int
    {
        $key = "verification_retry:domain:{$domain->id}:user:" . Auth::id();
        return RateLimiter::availableIn($key);
    }
}
