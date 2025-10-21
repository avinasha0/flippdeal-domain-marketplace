<?php

namespace App\Http\Controllers;

use App\Models\Bid;
use App\Models\Offer;
use App\Models\Domain;
use App\Models\Transaction;
use App\Models\Message;
use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RecentActivityController extends Controller
{
    /**
     * Get recent activity for the authenticated user
     */
    public function index(Request $request)
    {
        \Log::info('Activity API called', [
            'user_authenticated' => Auth::check(),
            'user_id' => Auth::id(),
            'session_id' => session()->getId(),
            'request_headers' => $request->headers->all()
        ]);
        
        $user = Auth::user();
        
        if (!$user) {
            \Log::warning('User not authenticated for activity API');
            return response()->json(['error' => 'User not authenticated'], 401);
        }
        
        $limit = $request->get('limit', 10);
        
        try {
            $activities = collect();

            // Get recent bids on user's domains (for sellers)
            try {
                $recentBids = Bid::whereHas('domain', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->with(['domain', 'user'])
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get()
                ->map(function ($bid) {
                    return [
                        'id' => 'bid_' . $bid->id,
                        'type' => 'bid_received',
                        'title' => 'New Bid Received',
                        'message' => "Someone bid {$bid->formatted_amount} on {$bid->domain->full_domain}",
                        'time' => $bid->created_at->diffForHumans(),
                        'created_at' => $bid->created_at->toISOString(),
                        'unread' => true,
                        'data' => [
                            'bid_id' => $bid->id,
                            'domain_id' => $bid->domain->id,
                            'bidder_name' => $bid->user->name,
                            'amount' => $bid->amount,
                            'formatted_amount' => $bid->formatted_amount,
                            'domain_name' => $bid->domain->full_domain,
                        ]
                    ];
                });
                $activities = $activities->merge($recentBids);
            } catch (\Exception $e) {
                \Log::warning('Error fetching bids', ['error' => $e->getMessage()]);
            }

            // Get recent offers on user's domains (for sellers)
            try {
                $recentOffers = Offer::whereHas('domain', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->with(['domain', 'buyer'])
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get()
                ->map(function ($offer) {
                    return [
                        'id' => 'offer_' . $offer->id,
                        'type' => 'offer_received',
                        'title' => 'New Offer Received',
                        'message' => "Someone offered {$offer->formatted_amount} for {$offer->domain->full_domain}",
                        'time' => $offer->created_at->diffForHumans(),
                        'created_at' => $offer->created_at->toISOString(),
                        'unread' => true,
                        'data' => [
                            'offer_id' => $offer->id,
                            'domain_id' => $offer->domain->id,
                            'buyer_name' => $offer->buyer->name,
                            'amount' => $offer->amount,
                            'formatted_amount' => $offer->formatted_amount,
                            'domain_name' => $offer->domain->full_domain,
                            'status' => $offer->status,
                        ]
                    ];
                });
                $activities = $activities->merge($recentOffers);
            } catch (\Exception $e) {
                \Log::warning('Error fetching offers', ['error' => $e->getMessage()]);
            }

            // Get recent bids placed by user (for buyers)
            try {
                $userBids = Bid::where('user_id', $user->id)
                    ->with(['domain'])
                    ->orderBy('created_at', 'desc')
                    ->limit($limit)
                    ->get()
                    ->map(function ($bid) {
                        return [
                            'id' => 'user_bid_' . $bid->id,
                            'type' => 'bid_placed',
                            'title' => 'Bid Placed',
                            'message' => "You bid {$bid->formatted_amount} on {$bid->domain->full_domain}",
                            'time' => $bid->created_at->diffForHumans(),
                            'created_at' => $bid->created_at->toISOString(),
                            'unread' => false,
                            'data' => [
                                'bid_id' => $bid->id,
                                'domain_id' => $bid->domain->id,
                                'amount' => $bid->amount,
                                'formatted_amount' => $bid->formatted_amount,
                                'domain_name' => $bid->domain->full_domain,
                            ]
                        ];
                    });
                $activities = $activities->merge($userBids);
            } catch (\Exception $e) {
                \Log::warning('Error fetching user bids', ['error' => $e->getMessage()]);
            }

            // Get recent offers placed by user (for buyers)
            try {
                $userOffers = Offer::where('buyer_id', $user->id)
                    ->with(['domain'])
                    ->orderBy('created_at', 'desc')
                    ->limit($limit)
                    ->get()
                    ->map(function ($offer) {
                        return [
                            'id' => 'user_offer_' . $offer->id,
                            'type' => 'offer_placed',
                            'title' => 'Offer Placed',
                            'message' => "You offered {$offer->formatted_amount} for {$offer->domain->full_domain}",
                            'time' => $offer->created_at->diffForHumans(),
                            'created_at' => $offer->created_at->toISOString(),
                            'unread' => false,
                            'data' => [
                                'offer_id' => $offer->id,
                                'domain_id' => $offer->domain->id,
                                'amount' => $offer->amount,
                                'formatted_amount' => $offer->formatted_amount,
                                'domain_name' => $offer->domain->full_domain,
                                'status' => $offer->status,
                            ]
                        ];
                    });
                $activities = $activities->merge($userOffers);
            } catch (\Exception $e) {
                \Log::warning('Error fetching user offers', ['error' => $e->getMessage()]);
            }

            // Get recent transactions
            try {
                $recentTransactions = Transaction::where('user_id', $user->id)
                    ->with(['domain'])
                    ->orderBy('created_at', 'desc')
                    ->limit($limit)
                    ->get()
                    ->map(function ($transaction) {
                        $type = $transaction->type === 'sale' ? 'payment_received' : 'payment_made';
                        $title = $transaction->type === 'sale' ? 'Payment Received' : 'Payment Made';
                        
                        return [
                            'id' => 'transaction_' . $transaction->id,
                            'type' => $type,
                            'title' => $title,
                            'message' => $transaction->type === 'sale' 
                                ? "You received {$transaction->formatted_amount} for {$transaction->domain->full_domain}"
                                : "You paid {$transaction->formatted_amount} for {$transaction->domain->full_domain}",
                            'time' => $transaction->created_at->diffForHumans(),
                            'created_at' => $transaction->created_at->toISOString(),
                            'unread' => false,
                            'data' => [
                                'transaction_id' => $transaction->id,
                                'domain_id' => $transaction->domain->id,
                                'amount' => $transaction->amount,
                                'formatted_amount' => $transaction->formatted_amount,
                                'domain_name' => $transaction->domain->full_domain,
                                'transaction_type' => $transaction->type,
                            ]
                        ];
                    });
                $activities = $activities->merge($recentTransactions);
            } catch (\Exception $e) {
                \Log::warning('Error fetching transactions', ['error' => $e->getMessage()]);
            }

            // Get recent domain status changes
            try {
                $recentDomainChanges = Domain::where('user_id', $user->id)
                    ->where('updated_at', '>', now()->subDays(7))
                    ->orderBy('updated_at', 'desc')
                    ->limit($limit)
                    ->get()
                    ->map(function ($domain) {
                        $statusMessages = [
                            'approved' => 'Your domain listing has been approved',
                            'rejected' => 'Your domain listing was rejected',
                            'sold' => 'Your domain has been sold',
                            'expired' => 'Your domain listing has expired',
                        ];

                        return [
                            'id' => 'domain_' . $domain->id . '_' . $domain->updated_at->timestamp,
                            'type' => 'domain_' . $domain->status,
                            'title' => 'Domain ' . ucfirst($domain->status),
                            'message' => $statusMessages[$domain->status] ?? "Your domain {$domain->full_domain} status changed to {$domain->status}",
                            'time' => $domain->updated_at->diffForHumans(),
                            'created_at' => $domain->updated_at->toISOString(),
                            'unread' => $domain->status === 'approved',
                            'data' => [
                                'domain_id' => $domain->id,
                                'domain_name' => $domain->full_domain,
                                'status' => $domain->status,
                            ]
                        ];
                    });
                $activities = $activities->merge($recentDomainChanges);
            } catch (\Exception $e) {
                \Log::warning('Error fetching domain changes', ['error' => $e->getMessage()]);
            }

            // Get recent messages
            try {
                $recentMessages = Conversation::where(function ($query) use ($user) {
                    $query->where('seller_id', $user->id)
                          ->orWhere('buyer_id', $user->id);
                })
                ->with(['domain', 'latestMessage', 'seller', 'buyer'])
                ->whereHas('latestMessage')
                ->orderBy('last_message_at', 'desc')
                ->limit($limit)
                ->get()
                ->map(function ($conversation) use ($user) {
                    $otherUser = $conversation->seller_id === $user->id ? $conversation->buyer : $conversation->seller;
                    $isUnread = $conversation->latestMessage && 
                               $conversation->latestMessage->user_id !== $user->id && 
                               !$conversation->latestMessage->read_at;

                    return [
                        'id' => 'message_' . $conversation->id . '_' . $conversation->latestMessage->id,
                        'type' => 'new_message',
                        'title' => 'New Message',
                        'message' => "New message from {$otherUser->name} about {$conversation->domain->full_domain}",
                        'time' => $conversation->last_message_at->diffForHumans(),
                        'created_at' => $conversation->last_message_at->toISOString(),
                        'unread' => $isUnread,
                        'data' => [
                            'conversation_id' => $conversation->id,
                            'domain_id' => $conversation->domain->id,
                            'domain_name' => $conversation->domain->full_domain,
                            'sender_name' => $otherUser->name,
                            'message_preview' => $conversation->latestMessage->body,
                        ]
                    ];
                });
                $activities = $activities->merge($recentMessages);
            } catch (\Exception $e) {
                \Log::warning('Error fetching messages', ['error' => $e->getMessage()]);
            }

            // If no real activities found, show a helpful message
            if ($activities->isEmpty()) {
                $activities = collect([
                    [
                        'id' => 'no_activity',
                        'type' => 'info',
                        'title' => 'No Recent Activity',
                        'message' => 'You don\'t have any recent activity yet. Start by listing a domain or making an offer!',
                        'time' => 'Just now',
                        'created_at' => now()->toISOString(),
                        'unread' => false,
                        'data' => []
                    ]
                ]);
            }

            // Sort all activities by created_at and limit
            $activities = $activities->sortByDesc('created_at')->take($limit);

            return response()->json([
                'activities' => $activities->values(),
                'total_count' => $activities->count(),
                'unread_count' => $activities->where('unread', true)->count(),
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching recent activities', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Failed to load activities',
                'activities' => [],
                'total_count' => 0,
                'unread_count' => 0,
            ], 500);
        }
    }

    /**
     * Mark activity as read
     */
    public function markAsRead(Request $request)
    {
        $activityId = $request->input('activity_id');
        $user = Auth::user();

        // This would typically update a read status in the database
        // For now, we'll just return success
        return response()->json(['success' => true]);
    }

    /**
     * Mark all activities as read
     */
    public function markAllAsRead()
    {
        $user = Auth::user();
        
        // This would typically update all unread activities for the user
        // For now, we'll just return success
        return response()->json(['success' => true]);
    }
}
