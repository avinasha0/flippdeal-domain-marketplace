<?php

namespace App\Policies;

use App\Models\Domain;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DomainPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // Anyone can view domains
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Domain $domain): bool
    {
        // Users can view active domains or their own domains
        return $domain->status === 'active' || $user->id === $domain->user_id || $user->isAdmin();
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
    public function update(User $user, Domain $domain): bool
    {
        // User can update their own domains or admin can update any
        return $user->id === $domain->user_id || $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Domain $domain): bool
    {
        // User can delete their own domains or admin can delete any
        return $user->id === $domain->user_id || $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Domain $domain): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Domain $domain): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can publish the domain.
     */
    public function publish(User $user, Domain $domain): bool
    {
        // User must own the domain and it must be verified
        return $user->id === $domain->user_id && $domain->domain_verified;
    }

    /**
     * Determine whether the user can bid on the domain.
     */
    public function bid(User $user, Domain $domain): bool
    {
        // User must be active, verified, and not the domain owner
        return $user->isAccountActive() && 
               $user->isFullyVerified() && 
               $user->id !== $domain->user_id &&
               $domain->status === 'active' &&
               $domain->enable_bidding;
    }

    /**
     * Determine whether the user can buy the domain.
     */
    public function buy(User $user, Domain $domain): bool
    {
        // User must be active, verified, and not the domain owner
        return $user->isAccountActive() && 
               $user->isFullyVerified() && 
               $user->id !== $domain->user_id &&
               $domain->status === 'active';
    }

    /**
     * Determine whether the user can verify the domain.
     */
    public function verify(User $user, Domain $domain): bool
    {
        // User must own the domain
        return $user->id === $domain->user_id;
    }

    /**
     * Determine whether the user can approve the domain (admin only).
     */
    public function approve(User $user, Domain $domain): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can reject the domain (admin only).
     */
    public function reject(User $user, Domain $domain): bool
    {
        return $user->isAdmin();
    }
}