<?php

namespace App\Http\Controllers;

use App\Models\Verification;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class VerificationController extends Controller
{
    /**
     * Display the verification page.
     */
    public function index()
    {
        $user = Auth::user();
        $verifications = $user->verifications()->with('approver')->get();
        
        return view('verification.index', compact('user', 'verifications'));
    }

    /**
     * Show PayPal email verification form.
     */
    public function showPayPalForm()
    {
        $user = Auth::user();
        $paypalVerification = $user->getVerificationByType('paypal_email');
        
        return view('verification.paypal', compact('user', 'paypalVerification'));
    }

    /**
     * Submit PayPal email verification.
     */
    public function submitPayPalVerification(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'paypal_email' => 'required|email|max:255',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Check if PayPal email is already verified
        if ($user->isPayPalVerified()) {
            return back()->with('error', 'Your PayPal email is already verified.');
        }

        // Create or update PayPal verification record
        $verification = Verification::updateOrCreate(
            [
                'user_id' => $user->id,
                'type' => 'paypal_email'
            ],
            [
                'identifier' => $request->paypal_email,
                'status' => 'verified',
                'verified_at' => now(),
                'data' => [
                    'email' => $request->paypal_email,
                    'submitted_at' => now()->toISOString(),
                    'auto_verified' => true
                ]
            ]
        );

        // Update user's PayPal email and mark as verified
        $user->update([
            'paypal_email' => $request->paypal_email,
            'paypal_verified' => true,
            'paypal_verified_at' => now()
        ]);

        // Log verification submission
        AuditLog::create([
            'user_id' => $user->id,
            'event' => 'paypal_verification_auto_verified',
            'auditable_type' => Verification::class,
            'auditable_id' => $verification->id,
            'new_values' => ['paypal_email' => $request->paypal_email, 'verified' => true],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', 'PayPal email has been added and verified successfully!');
    }

    /**
     * Show government ID verification form.
     */
    public function showGovernmentIdForm()
    {
        $user = Auth::user();
        $governmentIdVerification = $user->getVerificationByType('government_id');
        
        return view('verification.government-id', compact('user', 'governmentIdVerification'));
    }

    /**
     * Submit government ID verification.
     */
    public function submitGovernmentIdVerification(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'government_id' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120', // 5MB max
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Check if government ID is already verified
        if ($user->isGovernmentIdVerified()) {
            return back()->with('error', 'Your government ID is already verified.');
        }

        // Store the uploaded file
        $file = $request->file('government_id');
        $filename = 'government_id_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('verifications/government_id', $filename, 'private');

        // Create or update government ID verification record
        $verification = Verification::updateOrCreate(
            [
                'user_id' => $user->id,
                'type' => 'government_id'
            ],
            [
                'identifier' => $filename,
                'status' => 'pending',
                'data' => [
                    'filename' => $filename,
                    'original_name' => $file->getClientOriginalName(),
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'submitted_at' => now()->toISOString()
                ]
            ]
        );

        // Update user's government ID path
        $user->update(['government_id_path' => $path]);

        // Log verification submission
        AuditLog::create([
            'user_id' => $user->id,
            'event' => 'government_id_verification_submitted',
            'auditable_type' => Verification::class,
            'auditable_id' => $verification->id,
            'new_values' => ['filename' => $filename],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', 'Government ID verification submitted successfully. Please wait for admin approval.');
    }

    /**
     * Download government ID file (admin only).
     */
    public function downloadGovernmentId(Verification $verification)
    {
        // Only admin can download government ID files
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        if ($verification->type !== 'government_id' || !$verification->data['filename']) {
            abort(404, 'File not found.');
        }

        $filePath = 'verifications/government_id/' . $verification->data['filename'];
        
        if (!Storage::disk('private')->exists($filePath)) {
            abort(404, 'File not found.');
        }

        return Storage::disk('private')->download($filePath, $verification->data['original_name']);
    }

    /**
     * Get verification status for API.
     */
    public function getStatus()
    {
        $user = Auth::user();
        
        return response()->json([
            'paypal_verified' => $user->isPayPalVerified(),
            'government_id_verified' => $user->isGovernmentIdVerified(),
            'fully_verified' => $user->isFullyVerified(),
            'account_status' => $user->account_status,
            'verifications' => $user->verifications()->select('type', 'status', 'created_at')->get()
        ]);
    }
}