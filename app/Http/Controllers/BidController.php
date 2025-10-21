<?php

namespace App\Http\Controllers;

use App\Models\Bid;
use App\Models\Domain;
use App\Models\AuditLog;
use App\Notifications\NewBidReceived;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class BidController extends Controller
{
    /**
     * Display a listing of bids for a domain.
     */
    public function index(Domain $domain)
    {
        $bids = $domain->bids()
            ->with('bidder')
            ->orderBy('bid_amount', 'desc')
            ->paginate(20);

        return view('bids.index', compact('domain', 'bids'));
    }

    /**
     * Show the form for placing a bid.
     */
    public function create(Domain $domain)
    {
        // Check if user can bid on this domain
        if (!Gate::allows('bid', $domain)) {
            return back()->with('error', 'You cannot bid on this domain.');
        }

        if (!$domain->hasBidding()) {
            return back()->with('error', 'Bidding is not enabled for this domain.');
        }

        if ($domain->auction_status !== 'active') {
            if ($domain->auction_status === 'scheduled') {
                return back()->with('error', 'This auction has not started yet. It will begin on ' . $domain->auction_start->format('M j, Y g:i A'));
            } elseif ($domain->auction_status === 'ended') {
                return back()->with('error', 'This auction has already ended.');
            } else {
                return back()->with('error', 'This auction is not currently active. Status: ' . $domain->auction_status);
            }
        }

        // Check if user already has the highest bid
        $userHighestBid = $domain->bids()
            ->where('bidder_id', Auth::id())
            ->active()
            ->first();

        return view('bids.create', compact('domain', 'userHighestBid'));
    }

    /**
     * Store a newly created bid.
     */
    public function store(Request $request, Domain $domain)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'bid_amount' => 'required|numeric|min:0.01',
            'bidder_note' => 'nullable|string|max:500',
            'is_auto_bid' => 'boolean',
            'max_auto_bid' => 'nullable|required_if:is_auto_bid,1|numeric|min:0.01|gt:bid_amount',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Check if user can bid on this domain
        if (!Gate::allows('placeBid', $domain)) {
            return back()->with('error', 'You cannot bid on this domain.');
        }

        if (!$domain->hasBidding()) {
            return back()->with('error', 'Bidding is not enabled for this domain.');
        }

        if ($domain->auction_status !== 'active') {
            if ($domain->auction_status === 'scheduled') {
                return back()->with('error', 'This auction has not started yet. It will begin on ' . $domain->auction_start->format('M j, Y g:i A'));
            } elseif ($domain->auction_status === 'ended') {
                return back()->with('error', 'This auction has already ended.');
            } else {
                return back()->with('error', 'This auction is not currently active.');
            }
        }

        $bidAmount = $request->bid_amount;
        $nextMinimumBid = $domain->next_minimum_bid;

        // Check if bid meets minimum requirement
        if ($bidAmount < $nextMinimumBid) {
            return back()->with('error', 'Bid must be at least ' . $domain->formatted_next_minimum_bid);
        }

        // Check if user already has the highest bid
        $userHighestBid = $domain->bids()
            ->where('bidder_id', Auth::id())
            ->active()
            ->first();

        if ($userHighestBid && $bidAmount <= $userHighestBid->bid_amount) {
            return back()->with('error', 'Your bid must be higher than your current highest bid.');
        }

        try {
            DB::beginTransaction();

            // Mark all other bids as outbid
            $domain->bids()
                ->where('status', 'active')
                ->update([
                    'status' => 'outbid',
                    'outbid_at' => now()
                ]);

            // Create the new bid
            $bid = Bid::create([
                'domain_id' => $domain->id,
                'bidder_id' => Auth::id(),
                'bid_amount' => $bidAmount,
                'status' => 'active',
                'bid_at' => now(),
                'is_auto_bid' => $request->boolean('is_auto_bid'),
                'max_auto_bid' => $request->max_auto_bid,
                'bidder_note' => $request->bidder_note,
            ]);

            // Update domain with new current bid and count
            $domain->update([
                'current_bid' => $bidAmount,
                'bid_count' => $domain->bid_count + 1,
                'reserve_met' => $domain->isReserveMet(),
            ]);

            // Check if auto-extend should be triggered
            if ($domain->auto_extend && $domain->isAuctionEndingSoon()) {
                $newEndTime = $domain->auction_end->addMinutes($domain->auto_extend_minutes);
                $domain->update(['auction_end' => $newEndTime]);
            }

            // Log bid placement
            AuditLog::create([
                'user_id' => Auth::id(),
                'event' => 'bid_placed',
                'auditable_type' => Bid::class,
                'auditable_id' => $bid->id,
                'new_values' => [
                    'domain_id' => $domain->id,
                    'bid_amount' => $bidAmount,
                    'is_auto_bid' => $request->boolean('is_auto_bid')
                ],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Send notification to domain owner
            $domain->user->notify(new NewBidReceived($bid));

            DB::commit();

            return redirect()->route('domains.show', $domain)
                ->with('success', 'Bid placed successfully! You are now the highest bidder.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to place bid. Please try again.');
        }
    }

    /**
     * Display the specified bid.
     */
    public function show(Bid $bid)
    {
        // Check if user has access to this bid
        if ($bid->bidder_id !== Auth::id() && $bid->domain->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this bid.');
        }

        return view('bids.show', compact('bid'));
    }

    /**
     * Show the form for editing the specified bid.
     */
    public function edit(Bid $bid)
    {
        // Only bidder can edit their own bid
        if ($bid->bidder_id !== Auth::id()) {
            abort(403, 'You can only edit your own bids.');
        }

        // Only active bids can be edited
        if ($bid->status !== 'active') {
            return back()->with('error', 'This bid cannot be edited.');
        }

        return view('bids.edit', compact('bid'));
    }

    /**
     * Update the specified bid.
     */
    public function update(Request $request, Bid $bid)
    {
        // Only bidder can edit their own bid
        if ($bid->bidder_id !== Auth::id()) {
            abort(403, 'You can only edit your own bids.');
        }

        // Only active bids can be edited
        if ($bid->status !== 'active') {
            return back()->with('error', 'This bid cannot be edited.');
        }

        $validator = Validator::make($request->all(), [
            'bid_amount' => 'required|numeric|min:' . $bid->domain->starting_bid,
            'max_auto_bid' => 'nullable|numeric|min:0.01|gte:' . $request->bid_amount,
            'bidder_note' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            // Update the bid
            $oldBidAmount = $bid->bid_amount;
            $bid->update([
                'bid_amount' => $request->bid_amount,
                'max_auto_bid' => $request->max_auto_bid,
                'bidder_note' => $request->bidder_note,
            ]);

            // Update domain's current bid if this becomes the highest
            if ($request->bid_amount > $bid->domain->current_bid) {
                $bid->domain->update([
                    'current_bid' => $request->bid_amount,
                    'reserve_met' => $bid->domain->isReserveMet(),
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update bid. Please try again.');
        }

        return redirect()->route('bids.show', $bid)
            ->with('success', 'Bid updated successfully.');
    }

    /**
     * Remove the specified bid.
     */
    public function destroy(Bid $bid)
    {
        // Only bidder can cancel their own bid
        if ($bid->bidder_id !== Auth::id()) {
            abort(403, 'You can only cancel your own bids.');
        }

        // Only active bids can be cancelled
        if ($bid->status !== 'active') {
            return back()->with('error', 'This bid cannot be cancelled.');
        }

        try {
            DB::beginTransaction();

            // Cancel the bid
            $bid->update(['status' => 'cancelled']);

            // Update domain bid count
            $bid->domain->decrement('bid_count');

            // If this was the highest bid, update the current bid
            if ($bid->domain->current_bid == $bid->bid_amount) {
                $newHighestBid = $bid->domain->bids()
                    ->where('status', 'active')
                    ->orderBy('bid_amount', 'desc')
                    ->first();

                $bid->domain->update([
                    'current_bid' => $newHighestBid ? $newHighestBid->bid_amount : null,
                    'reserve_met' => $newHighestBid ? $bid->domain->isReserveMet() : false,
                ]);
            }

            DB::commit();

            return redirect()->route('domains.show', $bid->domain)
                ->with('success', 'Bid cancelled successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to cancel bid. Please try again.');
        }
    }

    /**
     * Get bid history for a domain (AJAX).
     */
    public function history(Domain $domain)
    {
        $bids = $domain->bids()
            ->with('bidder')
            ->orderBy('bid_amount', 'desc')
            ->limit(10)
            ->get();

        return response()->json($bids);
    }

    /**
     * Place an auto-bid (AJAX).
     */
    public function autoBid(Request $request, Domain $domain)
    {
        $validator = Validator::make($request->all(), [
            'max_amount' => 'required|numeric|min:0.01',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        // Check if user can bid
        if (Auth::id() === $domain->user_id) {
            return response()->json(['error' => 'You cannot bid on your own domain.'], 403);
        }

        if (!$domain->isAuctionActive()) {
            return response()->json(['error' => 'This auction is not currently active.'], 400);
        }

        $maxAmount = $request->max_amount;
        $nextMinimumBid = $domain->next_minimum_bid;

        // Check if max amount meets minimum requirement
        if ($maxAmount < $nextMinimumBid) {
            return response()->json([
                'error' => 'Maximum bid must be at least ' . $domain->formatted_next_minimum_bid
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Place the auto-bid
            $bid = Bid::create([
                'domain_id' => $domain->id,
                'bidder_id' => Auth::id(),
                'bid_amount' => $nextMinimumBid,
                'status' => 'active',
                'bid_at' => now(),
                'is_auto_bid' => true,
                'max_auto_bid' => $maxAmount,
            ]);

            // Mark other bids as outbid
            $domain->bids()
                ->where('id', '!=', $bid->id)
                ->where('status', 'active')
                ->update([
                    'status' => 'outbid',
                    'outbid_at' => now()
                ]);

            // Update domain
            $domain->update([
                'current_bid' => $bid->bid_amount,
                'bid_count' => $domain->bid_count + 1,
                'reserve_met' => $domain->isReserveMet(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Auto-bid placed successfully!',
                'bid' => $bid
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to place auto-bid.'], 500);
        }
    }
}
