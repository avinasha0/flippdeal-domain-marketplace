<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'domain_id',
        'buyer_id',
        'seller_id',
        'subject',
        'last_message_at',
        'buyer_unread_count',
        'seller_unread_count',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
        'buyer_unread_count' => 'integer',
        'seller_unread_count' => 'integer',
    ];

    /**
     * Get the domain that this conversation is about.
     */
    public function domain(): BelongsTo
    {
        return $this->belongsTo(Domain::class);
    }

    /**
     * Get the buyer in this conversation.
     */
    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    /**
     * Get the seller in this conversation.
     */
    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    /**
     * Get all messages in this conversation.
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class)->orderBy('created_at', 'asc');
    }

    /**
     * Get the latest message in this conversation.
     */
    public function latestMessage(): HasOne
    {
        return $this->hasOne(Message::class, 'conversation_id', 'id')->latest('created_at');
    }

    /**
     * Get unread count for a specific user.
     */
    public function getUnreadCountForUser(int $userId): int
    {
        if ($this->buyer_id === $userId) {
            return $this->buyer_unread_count;
        }
        
        if ($this->seller_id === $userId) {
            return $this->seller_unread_count;
        }
        
        return 0;
    }

    /**
     * Mark messages as read for a specific user.
     */
    public function markAsReadForUser(int $userId): void
    {
        if ($this->buyer_id === $userId) {
            $this->update(['buyer_unread_count' => 0]);
        } elseif ($this->seller_id === $userId) {
            $this->update(['seller_unread_count' => 0]);
        }
        
        // Mark all messages as read
        $this->messages()->where('sender_id', '!=', $userId)->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * Increment unread count for a specific user.
     */
    public function incrementUnreadForUser(int $userId): void
    {
        if ($this->buyer_id === $userId) {
            $this->increment('buyer_unread_count');
        } elseif ($this->seller_id === $userId) {
            $this->increment('seller_unread_count');
        }
    }
}