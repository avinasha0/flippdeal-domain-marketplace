<?php

namespace App\Services;

use App\Models\User;
use App\Models\Domain;
use App\Models\Bid;
use App\Models\Offer;
use App\Models\Transaction;
use App\Events\NotificationCreated;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Send a notification to a user and broadcast it.
     */
    public function notifyUser(User $user, string $type, array $data = [], string $title = null, string $message = null): void
    {
        try {
            // Create the notification
            $notification = $user->notifications()->create([
                'type' => $type,
                'data' => array_merge($data, [
                    'title' => $title,
                    'message' => $message,
                ]),
            ]);

            // Broadcast the notification
            broadcast(new NotificationCreated($notification));

            Log::info('Notification sent', [
                'user_id' => $user->id,
                'type' => $type,
                'notification_id' => $notification->id,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send notification', [
                'user_id' => $user->id,
                'type' => $type,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Notify when a new bid is placed.
     */
    public function notifyNewBid(Bid $bid): void
    {
        $domain = $bid->domain;
        $bidder = $bid->user;

        // Notify domain owner
        if ($domain->user_id !== $bidder->id) {
            $this->notifyUser(
                $domain->user,
                'bid.placed',
                [
                    'domain_id' => $domain->id,
                    'domain_name' => $domain->full_domain,
                    'bid_id' => $bid->id,
                    'bidder_id' => $bidder->id,
                    'bidder_name' => $bidder->name,
                    'amount' => $bid->amount,
                ],
                'New Bid Placed',
                "{$bidder->name} placed a bid of $" . number_format($bid->amount, 2) . " on {$domain->full_domain}"
            );
        }

        // Notify other bidders (outbid notification)
        $outbidUsers = $domain->bids()
            ->where('user_id', '!=', $bidder->id)
            ->where('amount', '<', $bid->amount)
            ->with('user')
            ->get()
            ->pluck('user')
            ->unique('id');

        foreach ($outbidUsers as $user) {
            $this->notifyUser(
                $user,
                'bid.outbid',
                [
                    'domain_id' => $domain->id,
                    'domain_name' => $domain->full_domain,
                    'bid_id' => $bid->id,
                    'bidder_id' => $bidder->id,
                    'bidder_name' => $bidder->name,
                    'amount' => $bid->amount,
                ],
                'You\'ve Been Outbid',
                "You've been outbid on {$domain->full_domain}. New highest bid: $" . number_format($bid->amount, 2)
            );
        }
    }

    /**
     * Notify when an auction is won.
     */
    public function notifyAuctionWon(Domain $domain, User $winner): void
    {
        $winningBid = $domain->bids()->where('user_id', $winner->id)->where('is_winning', true)->first();

        // Notify winner
        $this->notifyUser(
            $winner,
            'auction.won',
            [
                'domain_id' => $domain->id,
                'domain_name' => $domain->full_domain,
                'amount' => $winningBid->amount,
            ],
            'Auction Won!',
            "Congratulations! You won the auction for {$domain->full_domain} with a bid of $" . number_format($winningBid->amount, 2)
        );

        // Notify domain owner
        if ($domain->user_id !== $winner->id) {
            $this->notifyUser(
                $domain->user,
                'auction.sold',
                [
                    'domain_id' => $domain->id,
                    'domain_name' => $domain->full_domain,
                    'winner_id' => $winner->id,
                    'winner_name' => $winner->name,
                    'amount' => $winningBid->amount,
                ],
                'Domain Sold',
                "Your domain {$domain->full_domain} was sold to {$winner->name} for $" . number_format($winningBid->amount, 2)
            );
        }
    }

    /**
     * Notify when a new offer is made.
     */
    public function notifyNewOffer(Offer $offer): void
    {
        $domain = $offer->domain;
        $buyer = $offer->buyer;

        // Notify domain owner
        if ($domain->user_id !== $buyer->id) {
            $this->notifyUser(
                $domain->user,
                'offer.received',
                [
                    'domain_id' => $domain->id,
                    'domain_name' => $domain->full_domain,
                    'offer_id' => $offer->id,
                    'buyer_id' => $buyer->id,
                    'buyer_name' => $buyer->name,
                    'amount' => $offer->amount,
                ],
                'New Offer Received',
                "{$buyer->name} made an offer of $" . number_format($offer->amount, 2) . " on {$domain->full_domain}"
            );
        }
    }

    /**
     * Notify when an offer is accepted.
     */
    public function notifyOfferAccepted(Offer $offer): void
    {
        $domain = $offer->domain;
        $buyer = $offer->buyer;

        // Notify buyer
        $this->notifyUser(
            $buyer,
            'offer.accepted',
            [
                'domain_id' => $domain->id,
                'domain_name' => $domain->full_domain,
                'offer_id' => $offer->id,
                'amount' => $offer->amount,
            ],
            'Offer Accepted',
            "Your offer of $" . number_format($offer->amount, 2) . " for {$domain->full_domain} was accepted!"
        );
    }

    /**
     * Notify when a transaction state changes.
     */
    public function notifyTransactionUpdate(Transaction $transaction, string $event): void
    {
        $domain = $transaction->domain;
        $buyer = $transaction->buyer;
        $seller = $transaction->seller;

        switch ($event) {
            case 'payment_received':
                $this->notifyUser(
                    $seller,
                    'transaction.payment_received',
                    [
                        'transaction_id' => $transaction->id,
                        'domain_id' => $domain->id,
                        'domain_name' => $domain->full_domain,
                        'amount' => $transaction->amount,
                    ],
                    'Payment Received',
                    "Payment of $" . number_format($transaction->amount, 2) . " received for {$domain->full_domain}"
                );
                break;

            case 'escrow_released':
                $this->notifyUser(
                    $seller,
                    'transaction.escrow_released',
                    [
                        'transaction_id' => $transaction->id,
                        'domain_id' => $domain->id,
                        'domain_name' => $domain->full_domain,
                        'amount' => $transaction->net_amount,
                    ],
                    'Escrow Released',
                    "Escrow funds of $" . number_format($transaction->net_amount, 2) . " released for {$domain->full_domain}"
                );
                break;

            case 'escrow_refunded':
                $this->notifyUser(
                    $buyer,
                    'transaction.refunded',
                    [
                        'transaction_id' => $transaction->id,
                        'domain_id' => $domain->id,
                        'domain_name' => $domain->full_domain,
                        'amount' => $transaction->amount,
                    ],
                    'Transaction Refunded',
                    "Your payment of $" . number_format($transaction->amount, 2) . " for {$domain->full_domain} has been refunded"
                );
                break;
        }
    }

    /**
     * Notify when a domain is added to watchlist.
     */
    public function notifyDomainWatched(Domain $domain, User $user): void
    {
        if ($domain->user_id !== $user->id) {
            $this->notifyUser(
                $domain->user,
                'domain.watched',
                [
                    'domain_id' => $domain->id,
                    'domain_name' => $domain->full_domain,
                    'watcher_id' => $user->id,
                    'watcher_name' => $user->name,
                ],
                'Domain Added to Watchlist',
                "{$user->name} added {$domain->full_domain} to their watchlist"
            );
        }
    }

    /**
     * Notify when auction is ending soon.
     */
    public function notifyAuctionEndingSoon(Domain $domain, int $minutesLeft = 5): void
    {
        // Notify domain owner
        $this->notifyUser(
            $domain->user,
            'auction.ending_soon',
            [
                'domain_id' => $domain->id,
                'domain_name' => $domain->full_domain,
                'minutes_left' => $minutesLeft,
            ],
            'Auction Ending Soon',
            "Auction for {$domain->full_domain} ends in {$minutesLeft} minutes"
        );

        // Notify active bidders
        $bidders = $domain->bids()
            ->with('user')
            ->get()
            ->pluck('user')
            ->unique('id');

        foreach ($bidders as $bidder) {
            if ($bidder->id !== $domain->user_id) {
                $this->notifyUser(
                    $bidder,
                    'auction.ending_soon',
                    [
                        'domain_id' => $domain->id,
                        'domain_name' => $domain->full_domain,
                        'minutes_left' => $minutesLeft,
                    ],
                    'Auction Ending Soon',
                    "Auction for {$domain->full_domain} ends in {$minutesLeft} minutes"
                );
            }
        }
    }
}
