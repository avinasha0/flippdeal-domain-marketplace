<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\AuditService;

class RecaptchaMiddleware
{
    protected $auditService;

    public function __construct(AuditService $auditService)
    {
        $this->auditService = $auditService;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $action = 'default')
    {
        // Skip if ReCAPTCHA is disabled
        if (!config('recaptcha.enabled', false)) {
            return $next($request);
        }

        $recaptchaToken = $request->input('recaptcha_token') ?? $request->header('X-Recaptcha-Token');

        if (!$recaptchaToken) {
            return $this->handleFailure($request, 'Missing ReCAPTCHA token', $action);
        }

        $verificationResult = $this->verifyRecaptcha($recaptchaToken, $request->ip(), $action);

        if (!$verificationResult['success']) {
            return $this->handleFailure($request, $verificationResult['message'], $action);
        }

        // Add ReCAPTCHA score to request for logging
        $request->merge(['recaptcha_score' => $verificationResult['score']]);

        return $next($request);
    }

    /**
     * Verify ReCAPTCHA token
     */
    protected function verifyRecaptcha(string $token, string $ip, string $action): array
    {
        try {
            $secretKey = config('recaptcha.secret_key');
            $threshold = config('recaptcha.threshold', 0.5);

            $response = Http::timeout(10)->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $secretKey,
                'response' => $token,
                'remoteip' => $ip,
            ]);

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'message' => 'ReCAPTCHA verification service unavailable',
                ];
            }

            $data = $response->json();

            if (!$data['success']) {
                return [
                    'success' => false,
                    'message' => 'ReCAPTCHA verification failed: ' . implode(', ', $data['error-codes'] ?? []),
                ];
            }

            // For ReCAPTCHA v3, check score
            if (isset($data['score'])) {
                $score = $data['score'];
                
                if ($score < $threshold) {
                    return [
                        'success' => false,
                        'message' => "ReCAPTCHA score too low: {$score} (threshold: {$threshold})",
                        'score' => $score,
                    ];
                }

                return [
                    'success' => true,
                    'score' => $score,
                ];
            }

            // For ReCAPTCHA v2, just check success
            return [
                'success' => true,
                'score' => 1.0, // v2 doesn't have score
            ];

        } catch (\Exception $e) {
            Log::error('ReCAPTCHA verification exception', [
                'error' => $e->getMessage(),
                'ip' => $ip,
                'action' => $action,
            ]);

            return [
                'success' => false,
                'message' => 'ReCAPTCHA verification error',
            ];
        }
    }

    /**
     * Handle ReCAPTCHA failure
     */
    protected function handleFailure(Request $request, string $reason, string $action)
    {
        // Log the failure
        $this->auditService->logSecurityEvent(
            $request->user(),
            'recaptcha_failed',
            [
                'action' => $action,
                'reason' => $reason,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]
        );

        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'ReCAPTCHA verification failed',
                'recaptcha_error' => $reason,
            ], 422);
        }

        return redirect()->back()
            ->withErrors(['recaptcha' => 'ReCAPTCHA verification failed. Please try again.'])
            ->withInput();
    }
}
