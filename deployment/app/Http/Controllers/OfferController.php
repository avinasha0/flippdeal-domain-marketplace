<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use App\Models\Domain;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OfferController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get offers made by the user
        $myOffers = Offer::fromUser($user->id)
            ->with(['domain', 'domain.user'])
            ->latest()
            ->paginate(15);

        // Get offers received for user's domains
        $receivedOffers = Offer::forUserDomains($user->id)
            ->with(['domain', 'buyer'])
            ->latest()
            ->paginate(15);

        return view('offers.index', compact('myOffers', 'receivedOffers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $domainId = request('domain_id');
        $domain = null;
        
        if ($domainId) {
            $domain = Domain::findOrFail($domainId);
            
            // Check if domain accepts offers
            if (!$domain->acceptsOffers()) {
                return back()->with('error', 'This domain does not accept offers.');
            }
            
            // Check if user is not offering on their own domain
            if ($domain->user_id === Auth::id()) {
                return back()->with('error', 'You cannot make an offer on your own domain.');
            }
        }

        return view('offers.create', compact('domain'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'domain_id' => 'required|exists:domains,id',
            'offer_amount' => 'required|numeric|min:0.01',
            'message' => 'nullable|string|max:1000',
            'expires_in_days' => 'nullable|integer|min:1|max:30',
        ]);

        $domain = Domain::findOrFail($request->domain_id);
        
        // Check if domain accepts offers
        if (!$domain->acceptsOffers()) {
            return back()->with('error', 'This domain does not accept offers.');
        }
        
        // Check if user is not offering on their own domain
        if ($domain->user_id === Auth::id()) {
            return back()->with('error', 'You cannot make an offer on your own domain.');
        }
        
        // Check if domain is available
        if ($domain->status !== 'active') {
            return back()->with('error', 'This domain is not available for offers.');
        }
        
        // Check minimum offer amount
        if ($domain->minimum_offer && $request->offer_amount < $domain->minimum_offer) {
            return back()->with('error', 'Offer amount must be at least $' . number_format($domain->minimum_offer, 2));
        }
        
        // Check if user already has a pending offer on this domain
        $existingOffer = Offer::where('domain_id', $domain->id)
            ->where('buyer_id', Auth::id())
            ->where('status', 'pending')
            ->first();
            
        if ($existingOffer) {
            return back()->with('error', 'You already have a pending offer on this domain.');
        }

        try {
            DB::beginTransaction();

            // Create the offer
            $offer = Offer::create([
                'domain_id' => $domain->id,
                'buyer_id' => Auth::id(),
                'offer_amount' => $request->offer_amount,
                'message' => $request->message,
                'status' => 'pending',
                'expires_at' => $request->expires_in_days ? now()->addDays($request->expires_in_days) : null,
            ]);

            // Increment domain offer count
            $domain->increment('offer_count');

            DB::commit();

            return redirect()->route('offers.show', $offer)
                ->with('success', 'Offer submitted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to submit offer. Please try again.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Offer $offer)
    {
        $user = Auth::user();
        
        // Check if user has access to this offer
        if ($offer->buyer_id !== $user->id && $offer->domain->user_id !== $user->id) {
            abort(403, 'Unauthorized access to this offer.');
        }

        $offer->load(['domain', 'buyer', 'domain.user']);

        return view('offers.show', compact('offer'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Offer $offer)
    {
        $user = Auth::user();
        
        // Only buyer can edit their own offer
        if ($offer->buyer_id !== $user->id) {
            abort(403, 'You can only edit your own offers.');
        }
        
        // Only pending offers can be edited
        if ($offer->status !== 'pending') {
            return redirect()->route('offers.show', $offer)
                ->with('error', 'This offer cannot be edited.');
        }

        return view('offers.edit', compact('offer'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Offer $offer)
    {
        $user = Auth::user();
        
        // Only buyer can edit their own offer
        if ($offer->buyer_id !== $user->id) {
            abort(403, 'You can only edit your own offers.');
        }
        
        // Only pending offers can be edited
        if ($offer->status !== 'pending') {
            return redirect()->route('offers.show', $offer)
                ->with('error', 'This offer cannot be edited.');
        }

        $request->validate([
            'offer_amount' => 'required|numeric|min:0.01',
            'message' => 'nullable|string|max:1000',
            'expires_in_days' => 'nullable|integer|min:1|max:30',
        ]);

        // Check minimum offer amount
        if ($offer->domain->minimum_offer && $request->offer_amount < $offer->domain->minimum_offer) {
            return back()->with('error', 'Offer amount must be at least $' . number_format($offer->domain->minimum_offer, 2));
        }

        $offer->update([
            'offer_amount' => $request->offer_amount,
            'message' => $request->message,
            'expires_at' => $request->expires_in_days ? now()->addDays($request->expires_in_days) : null,
        ]);

        return redirect()->route('offers.show', $offer)
            ->with('success', 'Offer updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Offer $offer)
    {
        $user = Auth::user();
        
        // Only buyer can withdraw their own offer
        if ($offer->buyer_id !== $user->id) {
            abort(403, 'You can only withdraw your own offers.');
        }
        
        // Only pending offers can be withdrawn
        if ($offer->status !== 'pending') {
            return redirect()->route('offers.show', $offer)
                ->with('error', 'This offer cannot be withdrawn.');
        }

        $offer->withdraw();

        return redirect()->route('offers.index')
            ->with('success', 'Offer withdrawn successfully.');
    }

    /**
     * Accept an offer (seller action).
     */
    public function accept(Request $request, Offer $offer)
    {
        $user = Auth::user();
        
        // Only domain owner can accept offers
        if ($offer->domain->user_id !== $user->id) {
            abort(403, 'Only the domain owner can accept offers.');
        }
        
        // Check if offer can be accepted
        if (!$offer->canBeAccepted()) {
            return back()->with('error', 'This offer cannot be accepted.');
        }

        $request->validate([
            'response_message' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            // Accept the offer
            $offer->accept($request->response_message);

            // Convert offer to order
            $order = $offer->convertToOrder();

            DB::commit();

            return redirect()->route('orders.show', $order)
                ->with('success', 'Offer accepted! Order has been created.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to accept offer. Please try again.');
        }
    }

    /**
     * Reject an offer (seller action).
     */
    public function reject(Request $request, Offer $offer)
    {
        $user = Auth::user();
        
        // Only domain owner can reject offers
        if ($offer->domain->user_id !== $user->id) {
            abort(403, 'Only the domain owner can reject offers.');
        }
        
        // Check if offer can be rejected
        if (!$offer->canBeRejected()) {
            return back()->with('error', 'This offer cannot be rejected.');
        }

        $request->validate([
            'response_message' => 'nullable|string|max:1000',
        ]);

        $offer->reject($request->response_message);

        return back()->with('success', 'Offer rejected successfully.');
    }

    /**
     * Convert offer to order (buyer action).
     */
    public function convertToOrder(Offer $offer)
    {
        $user = Auth::user();
        
        // Only buyer can convert their own offer
        if ($offer->buyer_id !== $user->id) {
            abort(403, 'You can only convert your own offers.');
        }
        
        // Check if offer can be converted
        if ($offer->status !== 'accepted') {
            return back()->with('error', 'This offer cannot be converted to an order.');
        }

        try {
            DB::beginTransaction();

            // Convert offer to order
            $order = $offer->convertToOrder();

            DB::commit();

            return redirect()->route('orders.show', $order)
                ->with('success', 'Offer converted to order successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to convert offer to order. Please try again.');
        }
    }
}
