<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Domain;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConversationController extends Controller
{
    /**
     * Display a listing of conversations for the authenticated user.
     */
    public function index()
    {
        $user = Auth::user();
        
        $conversations = Conversation::with(['domain', 'buyer', 'seller', 'latestMessage'])
            ->where(function ($query) use ($user) {
                $query->where('buyer_id', $user->id)
                      ->orWhere('seller_id', $user->id);
            })
            ->orderBy('last_message_at', 'desc')
            ->paginate(20);

        return view('conversations.index', compact('conversations'));
    }

    /**
     * Show a specific conversation.
     */
    public function show(Request $request, $conversation = null)
    {
        $user = Auth::user();
        
        // If conversation parameter is actually a domain ID (from domain page)
        if (is_numeric($conversation) && $request->has('domain_id')) {
            $domainId = $request->get('domain_id');
            $domain = \App\Models\Domain::findOrFail($domainId);
            
            // Check if user is trying to chat with themselves
            if ($domain->user_id === $user->id) {
                return redirect()->back()->with('error', 'You cannot start a conversation with yourself.');
            }
            
            // Find or create conversation between current user and domain owner
            $conversation = Conversation::firstOrCreate(
                [
                    'seller_id' => $domain->user_id,
                    'buyer_id' => $user->id,
                    'domain_id' => $domain->id
                ],
                [
                    'subject' => 'Discussion about ' . $domain->full_domain,
                    'last_message_at' => now()
                ]
            );
        }
        
        // If conversation is still null or not found, treat as regular conversation
        if (!$conversation || !($conversation instanceof Conversation)) {
            $conversation = Conversation::findOrFail($conversation);
        }
        
        // Check if user is part of this conversation
        if ($conversation->buyer_id !== $user->id && $conversation->seller_id !== $user->id) {
            abort(403, 'Unauthorized access to conversation.');
        }

        // Mark conversation as read for this user
        $conversation->markAsReadForUser($user->id);
        
        // Reset unread count for this user
        if ($conversation->buyer_id === $user->id) {
            $conversation->update(['buyer_unread_count' => 0]);
        } else {
            $conversation->update(['seller_unread_count' => 0]);
        }
        
        // Also mark all messages in this conversation as read for this user
        Message::where('conversation_id', $conversation->id)
            ->where('to_user_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        $conversation->load(['domain', 'buyer', 'seller', 'messages.sender']);
        
        $otherUser = $conversation->buyer_id === $user->id 
            ? $conversation->seller 
            : $conversation->buyer;

        return view('conversations.show', compact('conversation', 'otherUser'));
    }

    /**
     * Start a new conversation about a domain.
     */
    public function store(Request $request)
    {
        $request->validate([
            'domain_id' => 'required|exists:domains,id',
            'message' => 'required|string|max:1000',
        ]);

        $user = Auth::user();
        $domain = Domain::findOrFail($request->domain_id);

        // Check if user is not the domain owner
        if ($domain->user_id === $user->id) {
            return back()->with('error', 'You cannot start a conversation about your own domain.');
        }

        // Check if conversation already exists
        $conversation = Conversation::where('domain_id', $domain->id)
            ->where('buyer_id', $user->id)
            ->where('seller_id', $domain->user_id)
            ->first();

        if (!$conversation) {
            $conversation = Conversation::create([
                'domain_id' => $domain->id,
                'buyer_id' => $user->id,
                'seller_id' => $domain->user_id,
                'subject' => "Inquiry about {$domain->full_domain}",
                'last_message_at' => now(),
                'seller_unread_count' => 1,
            ]);
        } else {
            // Update last message time and increment unread count
            $conversation->update([
                'last_message_at' => now(),
                'seller_unread_count' => $conversation->seller_unread_count + 1,
            ]);
        }

        // Create the message
        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'message' => $request->message,
        ]);

        // Create notification for the seller
        \App\Models\Notification::createNotification(
            $domain->user_id,
            'message_received',
            'New Message Received',
            "You have received a new message about {$domain->full_domain}",
            ['conversation_id' => $conversation->id, 'domain_id' => $domain->id]
        );

        return redirect()->route('conversations.show', $conversation)
            ->with('success', 'Message sent successfully.');
    }

    /**
     * Get unread conversation count for the authenticated user.
     */
    public function unreadCount()
    {
        $user = Auth::user();
        $count = $user->unread_conversation_count;
        
        return response()->json(['count' => $count]);
    }
}