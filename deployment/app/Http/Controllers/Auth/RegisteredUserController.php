<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserRole;
use App\Models\AuditLog;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
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

        DB::beginTransaction();
        
        try {
            // Get default user role
            $userRole = UserRole::where('name', 'user')->first();
            
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'company_name' => $request->company_name,
                'website' => $request->website,
                'location' => $request->location,
                'role_id' => $userRole->id,
                'account_status' => 'pending_verification', // Require admin approval
            ]);

            // Log user registration
            AuditLog::create([
                'user_id' => $user->id,
                'event' => 'user_registered',
                'auditable_type' => User::class,
                'auditable_id' => $user->id,
                'new_values' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'account_status' => 'pending_verification'
                ],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            event(new Registered($user));

            // Don't auto-login, require admin approval first
            return redirect()->route('login')->with('success', 
                'Registration successful! Your account is pending admin approval. You will receive an email once approved.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['email' => 'Registration failed. Please try again.']);
        }
    }
}
