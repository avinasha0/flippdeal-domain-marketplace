<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserRole;
use App\Models\EmailVerificationCode;
use App\Mail\EmailVerificationMail;
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
            // Generate verification code
            $verificationCode = EmailVerificationCode::generateCode($request->email);
            
            // Send verification email
            Mail::to($request->email)->send(new EmailVerificationMail($request->email, $verificationCode->code));
            
            // Store user data in session for verification
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
            
            return redirect()->route('verify-email')->with('success', 'Verification code sent to your email!');
            
        } catch (\Exception $e) {
            return back()->withErrors(['email' => 'Failed to send verification email. Please try again.']);
        }
    }

    public function showVerificationForm(): View
    {
        if (!session('pending_user')) {
            return redirect()->route('register');
        }
        
        return view('auth.verify-email');
    }

    public function verifyEmail(Request $request): RedirectResponse
    {
        $request->validate([
            'verification_code' => ['required', 'string', 'size:6']
        ]);

        $pendingUser = session('pending_user');
        
        if (!$pendingUser) {
            return redirect()->route('register');
        }

        if (EmailVerificationCode::verifyCode($pendingUser['email'], $request->verification_code)) {
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
                Auth::login($user);

                return redirect('/')->with('success', 'Registration successful! Your email has been verified.');
                
            } catch (\Exception $e) {
                return back()->withErrors(['verification_code' => 'Registration failed. Please try again.']);
            }
        } else {
            return back()->withErrors(['verification_code' => 'Invalid or expired verification code.']);
        }
    }

    public function resendCode(): RedirectResponse
    {
        $pendingUser = session('pending_user');
        
        if (!$pendingUser) {
            return redirect()->route('register');
        }

        try {
            $verificationCode = EmailVerificationCode::generateCode($pendingUser['email']);
            Mail::to($pendingUser['email'])->send(new EmailVerificationMail($pendingUser['email'], $verificationCode->code));
            
            return back()->with('success', 'New verification code sent to your email!');
        } catch (\Exception $e) {
            return back()->withErrors(['email' => 'Failed to resend verification code. Please try again.']);
        }
    }
}