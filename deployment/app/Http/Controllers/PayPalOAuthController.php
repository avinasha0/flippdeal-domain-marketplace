<?php

namespace App\Http\Controllers;

use App\Services\PayPalOAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PayPalOAuthController extends Controller
{
    protected $paypalService;

    public function __construct(PayPalOAuthService $paypalService)
    {
        $this->paypalService = $paypalService;
    }

    /**
     * Redirect to PayPal for OAuth authorization
     */
    public function redirect()
    {
        try {
            // Check if PayPal credentials are configured
            if (config('services.paypal.client_id') === 'your_paypal_client_id' || 
                empty(config('services.paypal.client_id'))) {
                
                // Use mock mode for testing
                return $this->mockRedirect();
            }
            
            $authUrl = $this->paypalService->getAuthorizationUrl();
            
            // Store state in session for CSRF protection
            session(['paypal_oauth_state' => csrf_token()]);
            
            return redirect($authUrl);
        } catch (\Exception $e) {
            Log::error('PayPal OAuth redirect failed', [
                'user_id' => Auth::id(),
                'message' => $e->getMessage()
            ]);
            
            return redirect()->route('profile.edit')
                ->with('error', 'Unable to connect to PayPal. Please try again.');
        }
    }

    /**
     * Mock PayPal redirect for testing when credentials not configured
     */
    private function mockRedirect()
    {
        try {
            $user = Auth::user();
            
            // Mock PayPal user info
            $mockPayPalInfo = [
                'email' => $user->email . '_paypal@example.com',
                'payer_id' => 'mock_payer_' . $user->id,
                'name' => $user->name,
                'verified_email' => true
            ];

            // Update user's PayPal email and mark as verified
            $user->update([
                'paypal_email' => $mockPayPalInfo['email'],
                'paypal_verified' => true,
                'paypal_verified_at' => now()
            ]);

            // Create verification record
            \App\Models\Verification::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'type' => 'paypal_email'
                ],
                [
                    'identifier' => $mockPayPalInfo['email'],
                    'status' => 'verified',
                    'verified_at' => now(),
                    'data' => json_encode([
                        'email' => $mockPayPalInfo['email'],
                        'paypal_user_id' => $mockPayPalInfo['payer_id'],
                        'verified_via' => 'mock_test',
                        'verified_at' => now()->toISOString()
                    ])
                ]
            );

            // Log verification
            \App\Models\AuditLog::create([
                'user_id' => $user->id,
                'event' => 'paypal_verification_mock_success',
                'auditable_type' => \App\Models\Verification::class,
                'auditable_id' => $user->id,
                'new_values' => [
                    'paypal_email' => $mockPayPalInfo['email'],
                    'verified' => true,
                    'method' => 'mock_test'
                ],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            return redirect()->route('profile.edit')
                ->with('success', 'PayPal account connected successfully! (Test Mode - No real PayPal credentials configured)');

        } catch (\Exception $e) {
            Log::error('PayPal mock redirect failed', [
                'user_id' => Auth::id(),
                'message' => $e->getMessage()
            ]);
            
            return redirect()->route('profile.edit')
                ->with('error', 'Mock PayPal connection failed. Please try again.');
        }
    }

    /**
     * Handle PayPal OAuth callback
     */
    public function callback(Request $request)
    {
        try {
            // Verify state parameter for CSRF protection
            $state = $request->query('state');
            $sessionState = session('paypal_oauth_state');
            
            if (!$state || $state !== $sessionState) {
                Log::warning('PayPal OAuth state mismatch', [
                    'user_id' => Auth::id(),
                    'provided_state' => $state,
                    'session_state' => $sessionState
                ]);
                
                return redirect()->route('profile.edit')
                    ->with('error', 'Invalid PayPal authorization. Please try again.');
            }

            // Clear the state from session
            session()->forget('paypal_oauth_state');

            // Get authorization code
            $code = $request->query('code');
            if (!$code) {
                return redirect()->route('profile.edit')
                    ->with('error', 'PayPal authorization was cancelled or failed.');
            }

            // Exchange code for access token
            $tokenData = $this->paypalService->getAccessToken($code);
            if (!$tokenData) {
                return redirect()->route('profile.edit')
                    ->with('error', 'Failed to authenticate with PayPal. Please try again.');
            }

            // Get user information from PayPal
            $userInfo = $this->paypalService->getUserInfo($tokenData['access_token']);
            if (!$userInfo) {
                return redirect()->route('profile.edit')
                    ->with('error', 'Failed to retrieve PayPal account information. Please try again.');
            }

            // Verify and update user's PayPal email
            $user = Auth::user();
            $success = $this->paypalService->verifyUserPayPalEmail($user, $userInfo);
            
            if ($success) {
                return redirect()->route('profile.edit')
                    ->with('success', 'PayPal account connected and verified successfully!');
            } else {
                return redirect()->route('profile.edit')
                    ->with('error', 'Failed to verify PayPal account. Please try again.');
            }

        } catch (\Exception $e) {
            Log::error('PayPal OAuth callback failed', [
                'user_id' => Auth::id(),
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('profile.edit')
                ->with('error', 'An error occurred while connecting to PayPal. Please try again.');
        }
    }

    /**
     * Disconnect PayPal account
     */
    public function disconnect()
    {
        try {
            $user = Auth::user();
            
            // Remove PayPal verification
            $user->update([
                'paypal_email' => null,
                'paypal_verified' => false,
                'paypal_verified_at' => null
            ]);

            // Remove verification records
            \App\Models\Verification::where('user_id', $user->id)
                ->where('type', 'paypal_email')
                ->delete();

            // Log disconnection
            \App\Models\AuditLog::create([
                'user_id' => $user->id,
                'event' => 'paypal_verification_disconnected',
                'auditable_type' => \App\Models\User::class,
                'auditable_id' => $user->id,
                'new_values' => ['paypal_email' => null, 'verified' => false],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            return redirect()->route('profile.edit')
                ->with('success', 'PayPal account disconnected successfully.');

        } catch (\Exception $e) {
            Log::error('PayPal disconnect failed', [
                'user_id' => Auth::id(),
                'message' => $e->getMessage()
            ]);
            
            return redirect()->route('profile.edit')
                ->with('error', 'Failed to disconnect PayPal account. Please try again.');
        }
    }
}
