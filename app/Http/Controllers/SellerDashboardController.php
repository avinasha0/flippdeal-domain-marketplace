<?php

namespace App\Http\Controllers;

use App\Models\Domain;
use App\Models\Bid;
use App\Models\Offer;
use App\Models\Conversation;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SellerDashboardController extends Controller
{
    /**
     * Display the seller dashboard.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $tab = $request->get('tab', 'listings');
        
        // Get user's domains
        $domains = $user->domains()->with(['bids', 'offers'])->get();

        // Get statistics
        $stats = [
            'total_listings' => $domains->count(),
            'active_listings' => $domains->where('status', 'active')->count(),
            'draft_listings' => $domains->where('status', 'draft')->count(),
            'total_bids' => $domains->sum(function ($domain) {
                return $domain->bids->count();
            }),
            'total_offers' => $domains->sum(function ($domain) {
                return $domain->offers->count();
            }),
            'total_messages' => $user->sellerConversations()->sum('seller_unread_count'),
            'total_sales' => $domains->where('status', 'sold')->sum('asking_price'),
        ];

        // Get data based on selected tab
        $data = [];
        
        switch ($tab) {
            case 'listings':
                $data = $domains->where('status', 'active')->sortByDesc('created_at');
                break;
                
            case 'drafts':
                $data = $domains->where('status', 'draft')->sortByDesc('created_at');
                break;
                
            case 'bids':
                $data = Bid::whereHas('domain', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->with(['domain', 'user'])
                ->orderBy('created_at', 'desc')
                ->paginate(20);
                break;
                
            case 'offers':
                $data = Offer::whereHas('domain', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->with(['domain', 'buyer'])
                ->orderBy('created_at', 'desc')
                ->paginate(20);
                break;
                
            case 'messages':
                $data = $user->sellerConversations()
                    ->with(['domain', 'buyer', 'latestMessage'])
                    ->orderBy('last_message_at', 'desc')
                    ->paginate(20);
                break;
        }

        return view('dashboard.seller', compact('stats', 'data', 'tab'));
    }

    /**
     * Get dashboard statistics via AJAX.
     */
    public function stats()
    {
        $user = Auth::user();
        $domains = $user->domains()->with(['bids', 'offers'])->get();

        $stats = [
            'total_listings' => $domains->count(),
            'active_listings' => $domains->where('status', 'active')->count(),
            'draft_listings' => $domains->where('status', 'draft')->count(),
            'total_bids' => $domains->sum(function ($domain) {
                return $domain->bids->count();
            }),
            'total_offers' => $domains->sum(function ($domain) {
                return $domain->offers->count();
            }),
            'total_messages' => $user->sellerConversations()->sum('seller_unread_count'),
            'total_sales' => $domains->where('status', 'sold')->sum('asking_price'),
        ];

        return response()->json($stats);
    }

    /**
     * Get recent activity for the seller.
     */
    public function recentActivity()
    {
        $user = Auth::user();
        
        $activities = collect();

        // Recent bids
        $recentBids = Bid::whereHas('domain', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->with(['domain', 'user'])
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get()
        ->map(function ($bid) {
            return [
                'type' => 'bid',
                'message' => "New bid of {$bid->formatted_amount} on {$bid->domain->full_domain}",
                'user' => $bid->user,
                'domain' => $bid->domain,
                'created_at' => $bid->created_at,
            ];
        });

        // Recent offers
        $recentOffers = Offer::whereHas('domain', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->with(['domain', 'buyer'])
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get()
        ->map(function ($offer) {
            return [
                'type' => 'offer',
                'message' => "New offer of {$offer->formatted_amount} on {$offer->domain->full_domain}",
                'user' => $offer->buyer,
                'domain' => $offer->domain,
                'created_at' => $offer->created_at,
            ];
        });

        // Recent messages
        $recentMessages = $user->sellerConversations()
            ->with(['domain', 'buyer', 'latestMessage'])
            ->orderBy('last_message_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($conversation) {
                return [
                    'type' => 'message',
                    'message' => "New message about {$conversation->domain->full_domain}",
                    'user' => $conversation->buyer,
                    'domain' => $conversation->domain,
                    'created_at' => $conversation->last_message_at,
                ];
            });

        $activities = $activities
            ->merge($recentBids)
            ->merge($recentOffers)
            ->merge($recentMessages)
            ->sortByDesc('created_at')
            ->take(10);

        return response()->json($activities->values());
    }
}