<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Domain;
use App\Models\Order;
use App\Models\Verification;
use App\Models\AuditLog;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class AdminController extends Controller
{
    // Middleware is applied in routes/web.php

    /**
     * Show admin dashboard.
     */
    public function dashboard(): View
    {
        $stats = [
            'total_users' => User::count(),
            'verified_users' => User::where('account_status', 'active')->count(),
            'suspended_users' => User::where('account_status', 'suspended')->count(),
            'total_domains' => Domain::count(),
            'active_auctions' => Domain::where('status', 'active')->where('enable_bidding', true)->count(),
            'total_revenue' => Order::where('status', 'completed')->sum('total_amount'),
            'commission_earned' => Order::where('status', 'completed')->sum('total_amount') * 0.05, // 5% commission
            'pending_verifications' => Verification::where('status', 'pending')->count(),
        ];

        $recentTransactions = Order::with('domain')
            ->where('status', 'completed')
            ->latest()
            ->limit(5)
            ->get();

        $pendingVerifications = Verification::with('user')
            ->where('status', 'pending')
            ->latest()
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentTransactions', 'pendingVerifications'));
    }

    /**
     * Show users management page.
     */
    public function users(Request $request): View
    {
        $query = User::with('role');

        if ($request->has('status')) {
            $query->where('account_status', $request->status);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show user details.
     */
    public function showUser(User $user): View
    {
        $user->load(['role', 'domains', 'verifications', 'auditLogs']);
        
        return view('admin.users.show', compact('user'));
    }

    /**
     * Approve user verification.
     */
    public function approveUser(User $user): RedirectResponse
    {
        $user->activate();
        
        AuditLog::log('user_approved', $user, null, null, "User account approved", auth()->user());

        return redirect()->back()->with('success', 'User approved successfully.');
    }

    /**
     * Suspend user.
     */
    public function suspendUser(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        $user->suspend($request->reason);
        
        AuditLog::log('user_suspended', $user, null, null, "User suspended: {$request->reason}", auth()->user());

        return redirect()->back()->with('success', 'User suspended successfully.');
    }

    /**
     * Activate user.
     */
    public function activateUser(User $user): RedirectResponse
    {
        $user->activate();
        
        AuditLog::log('user_activated', $user, null, null, "User account activated", auth()->user());

        return redirect()->back()->with('success', 'User activated successfully.');
    }

    /**
     * Show domains management page.
     */
    public function domains(Request $request): View
    {
        $query = Domain::with('user');

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('verified')) {
            $query->where('domain_verified', $request->verified === 'true');
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('domain_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $domains = $query->paginate(20);

        return view('admin.domains.index', compact('domains'));
    }

    /**
     * Show domain details.
     */
    public function showDomain(Domain $domain): View
    {
        $domain->load(['user', 'bids', 'offers', 'orders']);
        
        return view('admin.domains.show', compact('domain'));
    }

    /**
     * Approve domain listing.
     */
    public function approveDomain(Domain $domain): RedirectResponse
    {
        $domain->update(['status' => 'active']);
        
        AuditLog::log('domain_approved', $domain, null, null, "Domain listing approved", auth()->user());

        return redirect()->back()->with('success', 'Domain approved successfully.');
    }

    /**
     * Reject domain listing.
     */
    public function rejectDomain(Request $request, Domain $domain): RedirectResponse
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        $domain->update([
            'status' => 'rejected',
            'rejection_reason' => $request->reason
        ]);
        
        AuditLog::log('domain_rejected', $domain, null, null, "Domain listing rejected: {$request->reason}", auth()->user());

        return redirect()->back()->with('success', 'Domain rejected successfully.');
    }

    /**
     * Show verifications management page.
     */
    public function verifications(Request $request): View
    {
        $query = Verification::with(['user', 'verifier']);

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $verifications = $query->latest()->paginate(20);

        return view('admin.verifications.index', compact('verifications'));
    }

    /**
     * Approve verification.
     */
    public function approveVerification(Verification $verification): RedirectResponse
    {
        $verification->markAsVerified(auth()->user());
        
        // Update user verification status based on type
        if ($verification->type === 'paypal_email') {
            $verification->user->markPayPalAsVerified();
        } elseif ($verification->type === 'government_id') {
            $verification->user->markGovernmentIdAsVerified();
        }
        
        AuditLog::log('verification_approved', $verification, null, null, "Verification approved", auth()->user());

        return redirect()->back()->with('success', 'Verification approved successfully.');
    }

    /**
     * Reject verification.
     */
    public function rejectVerification(Request $request, Verification $verification): RedirectResponse
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        $verification->markAsRejected($request->reason, auth()->user());
        
        AuditLog::log('verification_rejected', $verification, null, null, "Verification rejected: {$request->reason}", auth()->user());

        return redirect()->back()->with('success', 'Verification rejected successfully.');
    }

    /**
     * Show site settings page.
     */
    public function settings(): View
    {
        $settings = SiteSetting::all()->groupBy('group');
        
        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Update site settings.
     */
    public function updateSettings(Request $request): RedirectResponse
    {
        $request->validate([
            'settings' => 'required|array',
            'settings.*' => 'required'
        ]);

        foreach ($request->settings as $key => $value) {
            $setting = SiteSetting::where('key', $key)->first();
            if ($setting) {
                $setting->update(['value' => $value]);
            }
        }
        
        AuditLog::log('settings_updated', null, null, null, "Site settings updated", auth()->user());

        return redirect()->back()->with('success', 'Settings updated successfully.');
    }

    /**
     * Show audit logs.
     */
    public function auditLogs(Request $request): View
    {
        $query = AuditLog::with('user');

        if ($request->has('action')) {
            $query->where('action', $request->action);
        }

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->latest()->paginate(50);

        return view('admin.audit-logs.index', compact('logs'));
    }

    /**
     * Get dashboard statistics for AJAX.
     */
    public function getStats(): JsonResponse
    {
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('account_status', 'active')->count(),
            'total_domains' => Domain::count(),
            'active_domains' => Domain::active()->count(),
            'total_orders' => Order::count(),
            'total_revenue' => Order::where('status', 'completed')->sum('total_amount'),
        ];

        return response()->json($stats);
    }
}