<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    /**
     * Display the password reset view.
     */
    public function create(Request $request): View
    {
        return view('auth.reset-password', ['request' => $request]);
    }

    /**
     * Handle an incoming new password request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Find the password reset record
        $passwordReset = DB::table('password_resets')
            ->where('email', $request->email)
            ->first();

        if (!$passwordReset) {
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'Invalid password reset token.']);
        }

        // Check if token is valid and not expired (60 minutes)
        if (!Hash::check($request->token, $passwordReset->token)) {
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'Invalid password reset token.']);
        }

        // Check if token is expired (60 minutes)
        if (now()->diffInMinutes($passwordReset->created_at) > 60) {
            // Clean up expired token
            DB::table('password_resets')->where('email', $request->email)->delete();
            
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'Password reset token has expired. Please request a new one.']);
        }

        // Find the user
        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'User not found.']);
        }

        // Check if user account is suspended
        if ($user->isAccountSuspended()) {
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'Your account has been suspended. Please contact support for assistance.']);
        }

        // Update the user's password
        $user->forceFill([
            'password' => Hash::make($request->password),
            'remember_token' => Str::random(60),
        ])->save();

        // Delete the password reset token
        DB::table('password_resets')->where('email', $request->email)->delete();

        // Fire the password reset event
        event(new PasswordReset($user));

        return redirect()->route('login')->with('status', 'Your password has been reset successfully! You can now log in with your new password.');
    }
}
