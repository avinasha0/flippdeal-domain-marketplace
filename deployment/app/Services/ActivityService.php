<?php

namespace App\Services;

use App\Models\Bid;
use App\Models\Offer;
use App\Models\Transaction;
use App\Models\Domain;
use App\Models\Message;
use App\Events\BidReceived;
use App\Events\OfferReceived;
use App\Events\PaymentReceived;
use App\Events\ActivityCreated;
use Illuminate\Support\Facades\Log;

class ActivityService
{
    /**
     * Broadcast activity when a new bid is placed
     */
    public function broadcastBidReceived(Bid $bid): void
    {
        try {
            broadcast(new BidReceived($bid));
            Log::info('Bid received activity broadcasted', [
                'bid_id' => $bid->id,
                'domain_id' => $bid->domain->id,
                'domain_owner_id' => $bid->domain->user_id,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to broadcast bid received activity', [
                'bid_id' => $bid->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Broadcast activity when a new offer is made
     */
    public function broadcastOfferReceived(Offer $offer): void
    {
        try {
            broadcast(new OfferReceived($offer));
            Log::info('Offer received activity broadcasted', [
                'offer_id' => $offer->id,
                'domain_id' => $offer->domain->id,
                'domain_owner_id' => $offer->domain->user_id,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to broadcast offer received activity', [
                'offer_id' => $offer->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Broadcast activity when a payment is received
     */
    public function broadcastPaymentReceived(Transaction $transaction): void
    {
        try {
            broadcast(new PaymentReceived($transaction));
            Log::info('Payment received activity broadcasted', [
                'transaction_id' => $transaction->id,
                'user_id' => $transaction->user_id,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to broadcast payment received activity', [
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Broadcast activity when a domain status changes
     */
    public function broadcastDomainStatusChange(Domain $domain, string $oldStatus, string $newStatus): void
    {
        try {
            $activity = [
                'id' => 'domain_' . $domain->id . '_' . now()->timestamp,
                'type' => 'domain_' . $newStatus,
                'title' => 'Domain ' . ucfirst($newStatus),
                'message' => $this->getDomainStatusMessage($newStatus, $domain->full_domain),
                'time' => now()->diffForHumans(),
                'created_at' => now()->toISOString(),
                'unread' => $newStatus === 'approved',
                'data' => [
                    'domain_id' => $domain->id,
                    'domain_name' => $domain->full_domain,
                    'status' => $newStatus,
                    'old_status' => $oldStatus,
                ]
            ];

            broadcast(new ActivityCreated($activity, $domain->user_id));
            Log::info('Domain status change activity broadcasted', [
                'domain_id' => $domain->id,
                'user_id' => $domain->user_id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to broadcast domain status change activity', [
                'domain_id' => $domain->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Broadcast activity when a new message is received
     */
    public function broadcastNewMessage(Message $message): void
    {
        try {
            $conversation = $message->conversation;
            $otherUserId = $conversation->seller_id === $message->user_id ? 
                $conversation->buyer_id : $conversation->seller_id;

            $activity = [
                'id' => 'message_' . $conversation->id . '_' . $message->id,
                'type' => 'new_message',
                'title' => 'New Message',
                'message' => "New message from {$message->user->name} about {$conversation->domain->full_domain}",
                'time' => $message->created_at->diffForHumans(),
                'created_at' => $message->created_at->toISOString(),
                'unread' => true,
                'data' => [
                    'conversation_id' => $conversation->id,
                    'domain_id' => $conversation->domain->id,
                    'domain_name' => $conversation->domain->full_domain,
                    'sender_name' => $message->user->name,
                    'message_preview' => $message->body,
                ]
            ];

            broadcast(new ActivityCreated($activity, $otherUserId));
            Log::info('New message activity broadcasted', [
                'message_id' => $message->id,
                'conversation_id' => $conversation->id,
                'recipient_id' => $otherUserId,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to broadcast new message activity', [
                'message_id' => $message->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get domain status change message
     */
    private function getDomainStatusMessage(string $status, string $domainName): string
    {
        $messages = [
            'approved' => "Your domain listing {$domainName} has been approved",
            'rejected' => "Your domain listing {$domainName} was rejected",
            'sold' => "Your domain {$domainName} has been sold",
            'expired' => "Your domain listing {$domainName} has expired",
            'pending' => "Your domain listing {$domainName} is pending review",
            'active' => "Your domain listing {$domainName} is now active",
        ];

        return $messages[$status] ?? "Your domain {$domainName} status changed to {$status}";
    }
}
