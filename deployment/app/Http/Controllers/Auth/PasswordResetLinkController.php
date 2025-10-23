<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\PasswordResetMail;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // Check if user exists
        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'We could not find a user with that email address.']);
        }

        // Check if user account is suspended
        if ($user->isAccountSuspended()) {
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'Your account has been suspended. Please contact support for assistance.']);
        }

        // Generate password reset token
        $token = Str::random(64);
        
        // Store the token in password_resets table
        DB::table('password_resets')->updateOrInsert(
            ['email' => $request->email],
            [
                'email' => $request->email,
                'token' => Hash::make($token),
                'created_at' => now()
            ]
        );

        try {
            // Send custom password reset email
            Mail::to($request->email)->send(new PasswordResetMail($token, $request->email, $user));
            
            return back()->with('status', 'We have emailed your password reset link!');
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Password reset email failed: ' . $e->getMessage());
            
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'Unable to send password reset email. Please try again later or contact support.']);
        }
    }
}
