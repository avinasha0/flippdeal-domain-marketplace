<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'domain_id',
        'order_id',
        'subject',
        'message',
        'type',
        'is_read',
        'read_at',
        'metadata'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'metadata' => 'array'
    ];

    /**
     * Get the sender of the message.
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Get the receiver of the message.
     */
    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    /**
     * Get the domain this message is related to.
     */
    public function domain(): BelongsTo
    {
        return $this->belongsTo(Domain::class);
    }

    /**
     * Get the order this message is related to.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Mark the message as read.
     */
    public function markAsRead(): void
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now()
            ]);
        }
    }

    /**
     * Check if the message is unread.
     */
    public function isUnread(): bool
    {
        return !$this->is_read;
    }

    /**
     * Get the conversation partner for a user.
     */
    public function getConversationPartner(int $userId): ?User
    {
        if ($this->sender_id === $userId) {
            return $this->receiver;
        }
        return $this->sender;
    }

    /**
     * Scope a query to only include unread messages.
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope a query to only include messages for a specific user.
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('sender_id', $userId)
              ->orWhere('receiver_id', $userId);
        });
    }

    /**
     * Scope a query to only include messages between two users.
     */
    public function scopeBetweenUsers($query, int $user1Id, int $user2Id)
    {
        return $query->where(function ($q) use ($user1Id, $user2Id) {
            $q->where(function ($subQ) use ($user1Id, $user2Id) {
                $subQ->where('sender_id', $user1Id)
                      ->where('receiver_id', $user2Id);
            })->orWhere(function ($subQ) use ($user1Id, $user2Id) {
                $subQ->where('sender_id', $user2Id)
                      ->where('receiver_id', $user1Id);
            });
        });
    }

    /**
     * Scope a query to only include messages related to a specific domain.
     */
    public function scopeForDomain($query, int $domainId)
    {
        return $query->where('domain_id', $domainId);
    }

    /**
     * Scope a query to only include messages related to a specific order.
     */
    public function scopeForOrder($query, int $orderId)
    {
        return $query->where('order_id', $orderId);
    }

    /**
     * Scope a query to only include messages of a specific type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }
}
