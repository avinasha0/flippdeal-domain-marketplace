<?php

namespace App\Http\Controllers;

use App\Models\Domain;
use App\Models\Bid;
use App\Models\Order;
use App\Models\Offer;
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

        $stats = [
            'my_domains' => $user->domains()->count(),
            'active_listings' => $user->domains()->where('status', 'active')->count(),
            'pending_domains' => $user->domains()->where('status', 'draft')->count(),
            'sold_domains' => $user->domains()->where('status', 'sold')->count(),
            'total_bids' => Bid::whereHas('domain', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })->count(),
            'active_auctions' => $user->domains()->where('status', 'active')->where('enable_bidding', true)->count(),
            'total_earnings' => Order::where('seller_id', $user->id)->where('status', 'completed')->sum('total_amount'),
            'wallet_balance' => $user->wallet_balance ?? 0,
        ];

        $recentDomains = $user->domains()
            ->where('status', '!=', 'draft')
            ->latest()
            ->limit(5)
            ->get();

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