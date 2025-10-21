<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use App\Models\Domain;
use App\Events\MessageSent;
use App\Events\MessageRead;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class MessageController extends Controller
{
    /**
     * Send a message to another user.
     */
    public function store(Request $request): JsonResponse
    {
        // Rate limiting: 5 messages per second per user
        $key = 'messages:' . Auth::id();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'message' => "Too many messages. Please wait {$seconds} seconds."
            ]);
        }

        $request->validate([
            'to_user_id' => 'required|exists:users,id',
            'domain_id' => 'nullable|exists:domains,id',
            'body' => 'required|string|max:1000',
        ]);

        // Prevent sending messages to yourself
        if ($request->to_user_id == Auth::id()) {
            return response()->json(['error' => 'Cannot send message to yourself'], 400);
        }

        // Check if domain exists and user has access to it
        if ($request->domain_id) {
            $domain = Domain::find($request->domain_id);
            if (!$domain) {
                return response()->json(['error' => 'Domain not found'], 404);
            }

            // Check if user is involved with this domain (owner, bidder, offer maker, watcher)
            $hasAccess = $domain->user_id === Auth::id() || 
                        $domain->bids()->where('user_id', Auth::id())->exists() ||
                        $domain->offers()->where('buyer_id', Auth::id())->exists() ||
                        $domain->watchlist()->where('user_id', Auth::id())->exists();

            if (!$hasAccess) {
                return response()->json(['error' => 'No access to this domain'], 403);
            }
        }

        // Create the message
        $message = Message::create([
            'from_user_id' => Auth::id(),
            'to_user_id' => $request->to_user_id,
            'domain_id' => $request->domain_id,
            'body' => strip_tags($request->body), // Sanitize HTML
            'metadata' => [
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ],
        ]);

        // Load relationships for broadcasting
        $message->load(['fromUser', 'toUser', 'domain']);

        // Broadcast the message
        broadcast(new MessageSent($message));

        // Hit rate limiter
        RateLimiter::hit($key, 1);

        return response()->json([
            'success' => true,
            'message' => $message->load(['fromUser', 'toUser', 'domain']),
        ], 201);
    }

    /**
     * Get conversation between current user and another user.
     */
    public function conversation(Request $request, int $userId): JsonResponse
    {
        $request->validate([
            'domain_id' => 'nullable|exists:domains,id',
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        $perPage = $request->get('per_page', 20);
        $domainId = $request->get('domain_id');

        // Get conversation messages
        $query = Message::betweenUsers(Auth::id(), $userId)
            ->with(['fromUser', 'toUser', 'domain'])
            ->orderBy('created_at', 'desc');

        if ($domainId) {
            $query->where('domain_id', $domainId);
        }

        $messages = $query->paginate($perPage);

        return response()->json([
            'messages' => $messages->items(),
            'pagination' => [
                'current_page' => $messages->currentPage(),
                'last_page' => $messages->lastPage(),
                'per_page' => $messages->perPage(),
                'total' => $messages->total(),
            ],
        ]);
    }

    /**
     * Mark a message as read.
     */
    public function markAsRead(Message $message): JsonResponse
    {
        // Only the recipient can mark as read
        if ($message->to_user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if (!$message->isRead()) {
            $message->markAsRead();
            broadcast(new MessageRead($message));
        }

        return response()->json([
            'success' => true,
            'message' => 'Message marked as read',
        ]);
    }

    /**
     * Mark all messages from a user as read.
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        $request->validate([
            'from_user_id' => 'required|exists:users,id',
            'domain_id' => 'nullable|exists:domains,id',
        ]);

        $query = Message::where('from_user_id', $request->from_user_id)
            ->where('to_user_id', Auth::id())
            ->whereNull('read_at');

        if ($request->domain_id) {
            $query->where('domain_id', $request->domain_id);
        }

        $messages = $query->get();

        foreach ($messages as $message) {
            $message->markAsRead();
            broadcast(new MessageRead($message));
        }

        return response()->json([
            'success' => true,
            'message' => "Marked {$messages->count()} messages as read",
        ]);
    }

    /**
     * Get unread message count for current user.
     */
    public function unreadCount(): JsonResponse
    {
        $count = Message::where('to_user_id', Auth::id())
            ->whereNull('read_at')
            ->count();

        return response()->json([
            'unread_count' => $count,
        ]);
    }

    /**
     * Get recent conversations for current user.
     */
    public function conversations(): JsonResponse
    {
        $conversations = Message::selectRaw('
                CASE 
                    WHEN from_user_id = ? THEN to_user_id 
                    ELSE from_user_id 
                END as other_user_id,
                MAX(created_at) as last_message_at,
                COUNT(CASE WHEN to_user_id = ? AND read_at IS NULL THEN 1 END) as unread_count
            ', [Auth::id(), Auth::id()])
            ->where(function ($query) {
                $query->where('from_user_id', Auth::id())
                      ->orWhere('to_user_id', Auth::id());
            })
            ->groupBy('other_user_id')
            ->orderBy('last_message_at', 'desc')
            ->limit(20)
            ->get();

        // Load user details for each conversation
        $conversations = $conversations->map(function ($conv) {
            $user = User::find($conv->other_user_id);
            return [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'avatar' => $user->avatar,
                ],
                'last_message_at' => $conv->last_message_at,
                'unread_count' => $conv->unread_count,
            ];
        });

        return response()->json([
            'conversations' => $conversations,
        ]);
    }

    /**
     * Delete a message (only sender can delete).
     */
    public function destroy(Message $message): JsonResponse
    {
        if ($message->from_user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $message->delete();

        return response()->json([
            'success' => true,
            'message' => 'Message deleted',
        ]);
    }
}