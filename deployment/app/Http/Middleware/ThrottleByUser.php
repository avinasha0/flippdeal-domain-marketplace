<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use App\Services\AuditService;

class ThrottleByUser
{
    protected $limiter;
    protected $auditService;

    public function __construct(RateLimiter $limiter, AuditService $auditService)
    {
        $this->limiter = $limiter;
        $this->auditService = $auditService;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $key, int $maxAttempts = 60, int $decayMinutes = 1)
    {
        $user = $request->user();
        $identifier = $user ? "user:{$user->id}" : "ip:{$request->ip()}";
        $throttleKey = "{$key}:{$identifier}";

        if ($this->limiter->tooManyAttempts($throttleKey, $maxAttempts)) {
            $this->handleThrottleExceeded($request, $throttleKey, $maxAttempts, $decayMinutes);
        }

        $this->limiter->hit($throttleKey, $decayMinutes * 60);

        $response = $next($request);

        return $this->addHeaders(
            $response,
            $maxAttempts,
            $this->calculateRemainingAttempts($throttleKey, $maxAttempts)
        );
    }

    /**
     * Handle throttle exceeded
     */
    protected function handleThrottleExceeded(Request $request, string $throttleKey, int $maxAttempts, int $decayMinutes)
    {
        $retryAfter = $this->limiter->availableIn($throttleKey);

        // Log the throttle event
        $this->auditService->logRateLimit(
            $request->user(),
            $throttleKey,
            [
                'max_attempts' => $maxAttempts,
                'decay_minutes' => $decayMinutes,
                'retry_after' => $retryAfter,
                'ip' => $request->ip(),
            ]
        );

        throw new ThrottleRequestsException(
            "Too many attempts. Please try again in {$retryAfter} seconds.",
            null,
            $this->getHeaders($maxAttempts, $this->calculateRemainingAttempts($throttleKey, $maxAttempts), $retryAfter)
        );
    }

    /**
     * Calculate remaining attempts
     */
    protected function calculateRemainingAttempts(string $throttleKey, int $maxAttempts): int
    {
        return max(0, $maxAttempts - $this->limiter->attempts($throttleKey));
    }

    /**
     * Add throttle headers to response
     */
    protected function addHeaders($response, int $maxAttempts, int $remainingAttempts)
    {
        $response->headers->set('X-RateLimit-Limit', $maxAttempts);
        $response->headers->set('X-RateLimit-Remaining', $remainingAttempts);

        return $response;
    }

    /**
     * Get throttle headers for exception
     */
    protected function getHeaders(int $maxAttempts, int $remainingAttempts, int $retryAfter): array
    {
        return [
            'X-RateLimit-Limit' => $maxAttempts,
            'X-RateLimit-Remaining' => $remainingAttempts,
            'Retry-After' => $retryAfter,
        ];
    }
}
