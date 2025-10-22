<?php

namespace App\Http\Controllers;

use App\Models\Domain;
use App\Models\Bid;
use App\Models\Order;
use App\Models\Offer;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    // Middleware is applied in routes/web.php

    /**
     * Show the user dashboard.
     */
    public function index(): View
    {
        $user = auth()->user();

        // Get domains with completed transactions
        $domainsWithCompletedTransactions = $user->domains()
            ->whereHas('transactions', function($query) {
                $query->where('escrow_state', 'released');
            })
            ->pluck('id');

        $stats = [
            'my_domains' => $user->domains()->count(),
            'active_listings' => $user->domains()->where('status', 'active')->whereNotIn('id', $domainsWithCompletedTransactions)->count(),
            'draft_domains' => $user->domains()->where('status', 'draft')->count(),
            'sold_domains' => $domainsWithCompletedTransactions->count(),
            'total_bids' => Bid::whereHas('domain', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })->count(),
            'active_auctions' => $user->domains()->where('status', 'active')->where('enable_bidding', true)->whereNotIn('id', $domainsWithCompletedTransactions)->count(),
            'total_earnings' => Transaction::where('seller_id', $user->id)->where('escrow_state', 'released')->sum('amount'),
            'wallet_balance' => $user->wallet_balance ?? 0,
        ];

        // Get recent domains with proper status handling
        $recentDomains = $user->domains()
            ->with(['transactions' => function($query) {
                $query->where('escrow_state', 'released');
            }])
            ->latest()
            ->limit(5)
            ->get()
            ->map(function($domain) {
                // If domain has a completed transaction, mark it as sold
                if ($domain->transactions->isNotEmpty()) {
                    $domain->status = 'sold';
                    $domain->sold_at = $domain->transactions->first()->updated_at;
                }
                return $domain;
            });

        $draftDomains = $user->domains()
            ->where('status', 'draft')
            ->latest()
            ->limit(5)
            ->get();

        $activeAuctions = $user->domains()
            ->where('status', 'active')
            ->where('enable_bidding', true)
            ->where('auction_end', '>', now())
            ->with(['bids' => function($query) {
                $query->latest()->limit(1);
            }])
            ->limit(3)
            ->get();

        return view('dashboard', compact('stats', 'recentDomains', 'draftDomains', 'activeAuctions'));
    }
}