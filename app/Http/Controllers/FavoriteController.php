<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Domain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        
        $favorites = Favorite::forUser($user->id)
            ->with(['domain', 'domain.user'])
            ->latest()
            ->paginate(20);

        return view('favorites.index', compact('favorites'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'domain_id' => 'required|exists:domains,id',
            'notes' => 'nullable|string|max:1000',
            'notify_on_price_change' => 'boolean',
            'notify_on_status_change' => 'boolean',
        ]);

        $domain = Domain::findOrFail($request->domain_id);
        
        // Check if user is not favoriting their own domain
        if ($domain->user_id === Auth::id()) {
            return back()->with('error', 'You cannot add your own domain to favorites.');
        }

        // Check if domain is already in favorites
        $existingFavorite = Favorite::where('user_id', Auth::id())
            ->where('domain_id', $domain->id)
            ->first();

        if ($existingFavorite) {
            return back()->with('error', 'This domain is already in your favorites.');
        }

        // Create favorite
        Favorite::create([
            'user_id' => Auth::id(),
            'domain_id' => $domain->id,
            'notes' => $request->notes,
            'notify_on_price_change' => $request->get('notify_on_price_change', true),
            'notify_on_status_change' => $request->get('notify_on_status_change', true),
        ]);

        // Increment domain favorite count
        $domain->increment('favorite_count');

        return back()->with('success', 'Domain added to favorites successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Favorite $favorite)
    {
        $user = Auth::user();
        
        // Check if user owns this favorite
        if ($favorite->user_id !== $user->id) {
            abort(403, 'Unauthorized access to this favorite.');
        }

        return view('favorites.show', compact('favorite'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Favorite $favorite)
    {
        $user = Auth::user();
        
        // Check if user owns this favorite
        if ($favorite->user_id !== $user->id) {
            abort(403, 'Unauthorized access to this favorite.');
        }

        return view('favorites.edit', compact('favorite'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Favorite $favorite)
    {
        $user = Auth::user();
        
        // Check if user owns this favorite
        if ($favorite->user_id !== $user->id) {
            abort(403, 'Unauthorized access to this favorite.');
        }

        $request->validate([
            'notes' => 'nullable|string|max:1000',
            'notify_on_price_change' => 'boolean',
            'notify_on_status_change' => 'boolean',
        ]);

        $favorite->update([
            'notes' => $request->notes,
            'notify_on_price_change' => $request->get('notify_on_price_change', true),
            'notify_on_status_change' => $request->get('notify_on_status_change', true),
        ]);

        return redirect()->route('favorites.show', $favorite)
            ->with('success', 'Favorite updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Favorite $favorite)
    {
        $user = Auth::user();
        
        // Check if user owns this favorite
        if ($favorite->user_id !== $user->id) {
            abort(403, 'Unauthorized access to this favorite.');
        }

        // Decrement domain favorite count
        $favorite->domain->decrement('favorite_count');

        $favorite->delete();

        return redirect()->route('favorites.index')
            ->with('success', 'Domain removed from favorites successfully.');
    }

    /**
     * Toggle notification settings for a favorite.
     */
    public function toggleNotifications(Favorite $favorite)
    {
        $user = Auth::user();
        
        // Check if user owns this favorite
        if ($favorite->user_id !== $user->id) {
            abort(403, 'Unauthorized access to this favorite.');
        }

        $notificationType = request('type');
        
        if ($notificationType === 'price') {
            $favorite->togglePriceNotifications();
        } elseif ($notificationType === 'status') {
            $favorite->toggleStatusNotifications();
        }

        return response()->json([
            'success' => true,
            'notify_on_price_change' => $favorite->notify_on_price_change,
            'notify_on_status_change' => $favorite->notify_on_status_change,
        ]);
    }

    /**
     * Add domain to favorites via AJAX.
     */
    public function addToFavorites(Request $request)
    {
        $request->validate([
            'domain_id' => 'required|exists:domains,id',
        ]);

        $domain = Domain::findOrFail($request->domain_id);
        
        // Check if user is not favoriting their own domain
        if ($domain->user_id === Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot add your own domain to favorites.'
            ], 400);
        }

        // Check if domain is already in favorites
        $existingFavorite = Favorite::where('user_id', Auth::id())
            ->where('domain_id', $domain->id)
            ->first();

        if ($existingFavorite) {
            return response()->json([
                'success' => false,
                'message' => 'This domain is already in your favorites.'
            ], 400);
        }

        // Create favorite
        $favorite = Favorite::create([
            'user_id' => Auth::id(),
            'domain_id' => $domain->id,
            'notify_on_price_change' => true,
            'notify_on_status_change' => true,
        ]);

        // Increment domain favorite count
        $domain->increment('favorite_count');

        return response()->json([
            'success' => true,
            'message' => 'Domain added to favorites successfully.',
            'favorite_id' => $favorite->id
        ]);
    }

    /**
     * Remove domain from favorites via AJAX.
     */
    public function removeFromFavorites(Request $request)
    {
        $request->validate([
            'domain_id' => 'required|exists:domains,id',
        ]);

        $favorite = Favorite::where('user_id', Auth::id())
            ->where('domain_id', $request->domain_id)
            ->first();

        if (!$favorite) {
            return response()->json([
                'success' => false,
                'message' => 'Domain not found in favorites.'
            ], 404);
        }

        // Decrement domain favorite count
        $favorite->domain->decrement('favorite_count');

        $favorite->delete();

        return response()->json([
            'success' => true,
            'message' => 'Domain removed from favorites successfully.'
        ]);
    }

    /**
     * Check if a domain is in user's favorites.
     */
    public function checkFavorite(Request $request)
    {
        $request->validate([
            'domain_id' => 'required|exists:domains,id',
        ]);

        $favorite = Favorite::where('user_id', Auth::id())
            ->where('domain_id', $request->domain_id)
            ->first();

        return response()->json([
            'is_favorite' => $favorite ? true : false,
            'favorite_id' => $favorite ? $favorite->id : null
        ]);
    }
}
