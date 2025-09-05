<?php

namespace App\Http\Controllers;

use App\Models\Watchlist;
use App\Models\Domain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WatchlistController extends Controller
{
    /**
     * Display the user's watchlist.
     */
    public function index()
    {
        $user = Auth::user();
        
        $watchlist = $user->watchlist()
            ->with(['domain.user', 'domain.category'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('watchlist.index', compact('watchlist'));
    }

    /**
     * Add a domain to the user's watchlist.
     */
    public function store(Request $request)
    {
        $request->validate([
            'domain_id' => 'required|exists:domains,id',
        ]);

        $user = Auth::user();
        $domain = Domain::findOrFail($request->domain_id);

        // Check if user is not the domain owner
        if ($domain->user_id === $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot add your own domain to watchlist.',
            ], 400);
        }

        // Check if already in watchlist
        if (Watchlist::isWatching($user->id, $domain->id)) {
            return response()->json([
                'success' => false,
                'message' => 'Domain is already in your watchlist.',
            ], 400);
        }

        Watchlist::addToWatchlist($user->id, $domain->id);

        return response()->json([
            'success' => true,
            'message' => 'Domain added to watchlist successfully.',
            'watchlist_count' => $user->watchlist_count,
        ]);
    }

    /**
     * Remove a domain from the user's watchlist.
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'domain_id' => 'required|exists:domains,id',
        ]);

        $user = Auth::user();
        $domain = Domain::findOrFail($request->domain_id);

        $removed = Watchlist::removeFromWatchlist($user->id, $domain->id);

        if (!$removed) {
            return response()->json([
                'success' => false,
                'message' => 'Domain was not in your watchlist.',
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Domain removed from watchlist successfully.',
            'watchlist_count' => $user->watchlist_count,
        ]);
    }

    /**
     * Toggle watchlist status for a domain.
     */
    public function toggle(Request $request)
    {
        $request->validate([
            'domain_id' => 'required|exists:domains,id',
        ]);

        $user = Auth::user();
        $domain = Domain::findOrFail($request->domain_id);

        // Check if user is not the domain owner
        if ($domain->user_id === $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot add your own domain to watchlist.',
            ], 400);
        }

        $isWatching = Watchlist::toggleWatchlist($user->id, $domain->id);

        return response()->json([
            'success' => true,
            'is_watching' => $isWatching,
            'message' => $isWatching 
                ? 'Domain added to watchlist successfully.' 
                : 'Domain removed from watchlist successfully.',
            'watchlist_count' => $user->watchlist_count,
        ]);
    }

    /**
     * Check if a domain is in the user's watchlist.
     */
    public function check(Request $request)
    {
        $request->validate([
            'domain_id' => 'required|exists:domains,id',
        ]);

        $user = Auth::user();
        $domain = Domain::findOrFail($request->domain_id);

        $isWatching = Watchlist::isWatching($user->id, $domain->id);

        return response()->json([
            'is_watching' => $isWatching,
        ]);
    }

    /**
     * Get watchlist count for the authenticated user.
     */
    public function count()
    {
        $user = Auth::user();
        $count = $user->watchlist_count;
        
        return response()->json(['count' => $count]);
    }
}