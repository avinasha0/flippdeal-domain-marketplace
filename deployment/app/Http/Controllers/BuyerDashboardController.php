<?php

namespace App\Http\Controllers;

use App\Models\Domain;
use App\Models\Bid;
use App\Models\Offer;
use App\Models\Watchlist;
use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BuyerDashboardController extends Controller
{
    /**
     * Display the buyer dashboard.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $tab = $request->get('tab', 'won');

        // Get statistics
        $stats = [
            'total_bids' => $user->bids()->count(),
            'winning_bids' => $user->bids()->winning()->count(),
            'outbid_bids' => $user->bids()->outbid()->count(),
            'total_offers' => $user->offers()->count(),
            'accepted_offers' => $user->offers()->where('status', 'accepted')->count(),
            'watchlist_count' => $user->watchlist_count,
            'total_spent' => $user->bids()->winning()->sum('amount') + 
                           $user->offers()->where('status', 'accepted')->sum('offer_amount'),
        ];

        // Get data based on selected tab
        $data = [];
        
        switch ($tab) {
            case 'won':
                $data = $user->bids()
                    ->winning()
                    ->with(['domain.user'])
                    ->orderBy('created_at', 'desc')
                    ->paginate(20);
                break;
                
            case 'bids':
                $data = $user->bids()
                    ->with(['domain.user'])
                    ->orderBy('created_at', 'desc')
                    ->paginate(20);
                break;
                
            case 'offers':
                $data = $user->offers()
                    ->with(['domain.user'])
                    ->orderBy('created_at', 'desc')
                    ->paginate(20);
                break;
                
            case 'watching':
                $data = $user->watchlist()
                    ->with(['domain.user'])
                    ->orderBy('created_at', 'desc')
                    ->paginate(20);
                break;
                
            case 'messages':
                $data = $user->buyerConversations()
                    ->with(['domain', 'seller', 'latestMessage'])
                    ->orderBy('last_message_at', 'desc')
                    ->paginate(20);
                break;
        }

        return view('dashboard.buyer', compact('stats', 'data', 'tab'));
    }

    /**
     * Get dashboard statistics via AJAX.
     */
    public function stats()
    {
        $user = Auth::user();

        $stats = [
            'total_bids' => $user->bids()->count(),
            'winning_bids' => $user->bids()->winning()->count(),
            'outbid_bids' => $user->bids()->outbid()->count(),
            'total_offers' => $user->offers()->count(),
            'accepted_offers' => $user->offers()->where('status', 'accepted')->count(),
            'watchlist_count' => $user->watchlist_count,
            'total_spent' => $user->bids()->winning()->sum('amount') + 
                           $user->offers()->where('status', 'accepted')->sum('offer_amount'),
        ];

        return response()->json($stats);
    }

    /**
     * Get recent activity for the buyer.
     */
    public function recentActivity()
    {
        $user = Auth::user();
        
        $activities = collect();

        // Recent bids
        $recentBids = $user->bids()
            ->with(['domain.user'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($bid) {
                $status = $bid->is_winning ? 'winning' : ($bid->is_outbid ? 'outbid' : 'active');
                return [
                    'type' => 'bid',
                    'message' => "Bid of {$bid->formatted_amount} on {$bid->domain->full_domain} ({$status})",
                    'domain' => $bid->domain,
                    'status' => $status,
                    'created_at' => $bid->created_at,
                ];
            });

        // Recent offers
        $recentOffers = $user->offers()
            ->with(['domain.user'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($offer) {
                return [
                    'type' => 'offer',
                    'message' => "Offer of {$offer->formatted_amount} on {$offer->domain->full_domain} ({$offer->status})",
                    'domain' => $offer->domain,
                    'status' => $offer->status,
                    'created_at' => $offer->created_at,
                ];
            });

        // Recent watchlist additions
        $recentWatchlist = $user->watchlist()
            ->with(['domain.user'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($watchlist) {
                return [
                    'type' => 'watchlist',
                    'message' => "Added {$watchlist->domain->full_domain} to watchlist",
                    'domain' => $watchlist->domain,
                    'created_at' => $watchlist->created_at,
                ];
            });

        // Recent messages
        $recentMessages = $user->buyerConversations()
            ->with(['domain.user', 'latestMessage'])
            ->orderBy('last_message_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($conversation) {
                return [
                    'type' => 'message',
                    'message' => "Message about {$conversation->domain->full_domain}",
                    'domain' => $conversation->domain,
                    'created_at' => $conversation->last_message_at,
                ];
            });

        $activities = $activities
            ->merge($recentBids)
            ->merge($recentOffers)
            ->merge($recentWatchlist)
            ->merge($recentMessages)
            ->sortByDesc('created_at')
            ->take(10);

        return response()->json($activities->values());
    }
}