<?php

namespace App\Policies;

use App\Models\Bid;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BidPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // Anyone can view bids
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Bid $bid): bool
    {
        // Users can view their own bids, domain owner can view all bids on their domain, or admin
        return $user->id === $bid->user_id || 
               $user->id === $bid->domain->user_id || 
               $user->isAdmin();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // User must be active and verified
        return $user->isAccountActive() && $user->isFullyVerified();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Bid $bid): bool
    {
        // Users can only update their own bids if the auction is still active
        return $user->id === $bid->user_id && 
               $bid->domain->status === 'active' && 
               $bid->domain->auction_end > now();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Bid $bid): bool
    {
        // Users can delete their own bids if auction is still active, or admin can delete any
        return ($user->id === $bid->user_id && 
                $bid->domain->status === 'active' && 
                $bid->domain->auction_end > now()) || 
               $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Bid $bid): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Bid $bid): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can place a bid on a domain.
     */
    public function placeBid(User $user, $domain): bool
    {
        // User must be active, verified, not the domain owner, and domain must be active with bidding enabled
        return $user->isAccountActive() && 
               $user->isFullyVerified() && 
               $user->id !== $domain->user_id &&
               $domain->status === 'active' &&
               $domain->enable_bidding &&
               $domain->auction_end > now();
    }

    /**
     * Determine whether the user can retract a bid.
     */
    public function retract(User $user, Bid $bid): bool
    {
        // User can retract their own bid if auction is still active and they're not the highest bidder
        return $user->id === $bid->user_id && 
               $bid->domain->status === 'active' && 
               $bid->domain->auction_end > now() &&
               $bid->domain->current_bid !== $bid->bid_amount;
    }
}