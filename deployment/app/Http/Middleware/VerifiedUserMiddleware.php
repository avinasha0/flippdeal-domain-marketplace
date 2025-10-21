<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class VerifiedUserMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Check if user account is active
        if (!$user->isAccountActive()) {
            if ($user->isAccountSuspended()) {
                Auth::logout();
                return redirect()->route('login')->withErrors([
                    'email' => 'Your account has been suspended. Please contact support for assistance.',
                ]);
            }

            if ($user->isAccountPendingVerification()) {
                Auth::logout();
                return redirect()->route('login')->withErrors([
                    'email' => 'Your account is pending verification. Please check your email for verification instructions.',
                ]);
            }
        }

        // Check if user is fully verified (for certain actions)
        if (!$user->isFullyVerified()) {
            return redirect()->route('profile.edit')->with('warning', 
                'Please complete your profile verification to access this feature.');
        }

        return $next($request);
    }
}