<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserRole;
use App\Models\EmailActivationToken;
use App\Mail\EmailActivationMail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class], 
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => ['nullable', 'string', 'max:20'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            // Generate activation token
            $activationToken = EmailActivationToken::generateToken($request->email);
            
            // Log the attempt
            \Log::info('Attempting to send activation email', [
                'email' => $request->email,
                'token_id' => $activationToken->id,
                'environment' => config('app.env'),
                'mail_driver' => config('mail.default')
            ]);
            
            // Send activation email
            Mail::to($request->email)->send(new EmailActivationMail($request->email, $activationToken->token));
            
            // Log success
            \Log::info('Activation email sent successfully', [
                'email' => $request->email,
                'token_id' => $activationToken->id
            ]);
            
            // Store user data in session for activation
            session([
                'pending_user' => [
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'phone' => $request->phone,
                    'company_name' => $request->company_name,
                    'website' => $request->website,
                    'location' => $request->location,
                ]
            ]);
            
            return redirect()->route('register.activate-email')->with('success', 'Activation email sent to your email address!');
            
        } catch (\Exception $e) {
            // Log detailed error information
            \Log::error('Failed to send activation email', [
                'email' => $request->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'environment' => config('app.env'),
                'mail_driver' => config('mail.default'),
                'smtp_host' => config('mail.mailers.smtp.host'),
                'smtp_port' => config('mail.mailers.smtp.port')
            ]);
            
            return back()->withErrors(['email' => 'Failed to send activation email. Please try again. If the problem persists, contact support.']);
        }
    }

    public function showActivationForm(): View|RedirectResponse
    {
        // Check if user is already logged in
        if (Auth::check()) {
            return redirect()->route('profile.edit')->with('info', 'You are already logged in.');
        }
        
        // Check if there's a pending user registration
        if (!session('pending_user')) {
            return redirect()->route('register')->with('error', 'No pending registration found. Please register first.');
        }
        
        return view('auth.activate-email');
    }

    public function activateAccount(Request $request, $token, $email): RedirectResponse
    {
        // Check if user is already logged in
        if (Auth::check()) {
            return redirect()->route('profile.edit')->with('info', 'You are already logged in.');
        }

        $pendingUser = session('pending_user');
        
        if (!$pendingUser) {
            return redirect()->route('register')->with('error', 'No pending registration found. Please register first.');
        }

        // Verify the token matches the pending user's email
        if ($pendingUser['email'] !== $email) {
            return redirect()->route('register')->with('error', 'Invalid activation link.');
        }

        if (EmailActivationToken::verifyToken($email, $token)) {
            try {
                $userRole = UserRole::where('name', 'user')->first();
                
                $user = User::create([
                    'name' => $pendingUser['name'],
                    'email' => $pendingUser['email'],
                    'password' => $pendingUser['password'],
                    'phone' => $pendingUser['phone'],
                    'company_name' => $pendingUser['company_name'],
                    'website' => $pendingUser['website'],
                    'location' => $pendingUser['location'],
                    'role_id' => $userRole->id,
                    'account_status' => 'active',
                    'email_verified_at' => now()
                ]);

                // Clear pending user data
                session()->forget('pending_user');
                
                event(new Registered($user));
                // Don't auto-login, redirect to login page instead
                // Auth::login($user);

                return redirect()->route('login')->with('success', 'Account activated successfully! Please log in to continue.');
                
            } catch (\Exception $e) {
                return redirect()->route('register')->with('error', 'Account activation failed. Please try registering again.');
            }
        } else {
            return redirect()->route('register.activate-email')->with('error', 'Invalid or expired activation link. Please request a new one.');
        }
    }

    public function resendActivation(): RedirectResponse
    {
        $pendingUser = session('pending_user');
        
        if (!$pendingUser) {
            return redirect()->route('register');
        }

        try {
            $activationToken = EmailActivationToken::generateToken($pendingUser['email']);
            Mail::to($pendingUser['email'])->send(new EmailActivationMail($pendingUser['email'], $activationToken->token));
            
            return back()->with('success', 'New activation email sent to your email address!');
        } catch (\Exception $e) {
            return back()->withErrors(['email' => 'Failed to resend activation email. Please try again.']);
        }
    }
}