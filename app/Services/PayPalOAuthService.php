<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PayPalOAuthService
{
    private $clientId;
    private $clientSecret;
    private $mode;
    private $redirectUri;
    private $baseUrl;

    public function __construct()
    {
        $this->clientId = config('services.paypal.client_id');
        $this->clientSecret = config('services.paypal.client_secret');
        $this->mode = config('services.paypal.mode', 'sandbox');
        $this->redirectUri = config('services.paypal.redirect_uri');
        $this->baseUrl = $this->mode === 'live' 
            ? 'https://api.paypal.com' 
            : 'https://api.sandbox.paypal.com';
    }

    /**
     * Generate PayPal OAuth authorization URL
     */
    public function getAuthorizationUrl(): string
    {
        $params = [
            'client_id' => $this->clientId,
            'response_type' => 'code',
            'scope' => 'openid',
            'redirect_uri' => $this->redirectUri,
            'state' => csrf_token(), // CSRF protection
        ];

        $queryString = http_build_query($params);
        return "https://www.paypal.com/signin/authorize?{$queryString}";
    }

    /**
     * Exchange authorization code for access token
     */
    public function getAccessToken(string $code): ?array
    {
        try {
            $response = Http::withBasicAuth($this->clientId, $this->clientSecret)
                ->asForm()
                ->post("{$this->baseUrl}/v1/oauth2/token", [
                    'grant_type' => 'authorization_code',
                    'code' => $code,
                    'redirect_uri' => $this->redirectUri,
                ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('PayPal OAuth token exchange failed', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('PayPal OAuth token exchange exception', [
                'message' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Get user information from PayPal
     */
    public function getUserInfo(string $accessToken): ?array
    {
        try {
            $response = Http::withToken($accessToken)
                ->get("{$this->baseUrl}/v1/identity/oauth2/userinfo", [
                    'schema' => 'openid'
                ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('PayPal user info fetch failed', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('PayPal user info fetch exception', [
                'message' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Verify PayPal email and mark user as verified
     */
    public function verifyUserPayPalEmail($user, array $paypalUserInfo): bool
    {
        try {
            $email = $paypalUserInfo['email'] ?? null;
            
            if (!$email) {
                return false;
            }

            // Update user's PayPal email and mark as verified
            $user->update([
                'paypal_email' => $email,
                'paypal_verified' => true,
                'paypal_verified_at' => now()
            ]);

            // Also mark email as verified if not already verified
            if (!$user->hasVerifiedEmail()) {
                $user->markEmailAsVerified();
            }

            // Create verification record
            \App\Models\Verification::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'type' => 'paypal_email'
                ],
                [
                    'identifier' => $email,
                    'status' => 'verified',
                    'verified_at' => now(),
                    'data' => [
                        'email' => $email,
                        'paypal_user_id' => $paypalUserInfo['payer_id'] ?? null,
                        'verified_via' => 'oauth',
                        'verified_at' => now()->toISOString()
                    ]
                ]
            );

            // Log verification
            \App\Models\AuditLog::create([
                'user_id' => $user->id,
                'event' => 'paypal_verification_oauth_success',
                'auditable_type' => \App\Models\Verification::class,
                'auditable_id' => $user->id,
                'new_values' => [
                    'paypal_email' => $email,
                    'verified' => true,
                    'method' => 'oauth'
                ],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('PayPal email verification failed', [
                'user_id' => $user->id,
                'message' => $e->getMessage()
            ]);
            return false;
        }
    }
}
