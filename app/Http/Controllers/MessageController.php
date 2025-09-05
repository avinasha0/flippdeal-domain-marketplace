<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Conversation;
use App\Models\User;
use App\Models\Domain;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get conversations (unique users the current user has messaged with)
        $conversations = Message::where('sender_id', $user->id)
            ->orWhere('receiver_id', $user->id)
            ->with(['sender', 'receiver'])
            ->get()
            ->groupBy(function ($message) use ($user) {
                return $message->sender_id === $user->id ? $message->receiver_id : $message->sender_id;
            })
            ->map(function ($messages) use ($user) {
                $lastMessage = $messages->sortByDesc('created_at')->first();
                $otherUser = $lastMessage->sender_id === $user->id ? $lastMessage->receiver : $lastMessage->sender;
                $unreadCount = $messages->where('receiver_id', $user->id)->where('is_read', false)->count();
                
                return [
                    'user' => $otherUser,
                    'last_message' => $lastMessage,
                    'unread_count' => $unreadCount,
                    'last_activity' => $lastMessage->created_at
                ];
            })
            ->sortByDesc('last_activity')
            ->values();

        return view('messages.index', compact('conversations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $recipientId = request('recipient_id');
        $domainId = request('domain_id');
        $orderId = request('order_id');
        
        $recipient = null;
        $domain = null;
        $order = null;
        
        if ($recipientId) {
            $recipient = User::findOrFail($recipientId);
        }
        
        if ($domainId) {
            $domain = Domain::findOrFail($domainId);
        }
        
        if ($orderId) {
            $order = Order::findOrFail($orderId);
        }

        return view('messages.create', compact('recipient', 'domain', 'order'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required|exists:conversations,id',
            'message' => 'required|string|max:1000',
        ]);

        $user = Auth::user();
        $conversation = Conversation::findOrFail($request->conversation_id);

        // Check if user is part of this conversation
        if ($conversation->buyer_id !== $user->id && $conversation->seller_id !== $user->id) {
            abort(403, 'Unauthorized access to conversation.');
        }

        // Create the message
        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'message' => $request->message,
        ]);

        // Update conversation
        $conversation->update([
            'last_message_at' => now(),
        ]);

        // Increment unread count for the other user
        $conversation->incrementUnreadForUser(
            $conversation->buyer_id === $user->id ? $conversation->seller_id : $conversation->buyer_id
        );

        // Create notification for the other user
        $otherUserId = $conversation->buyer_id === $user->id ? $conversation->seller_id : $conversation->buyer_id;
        \App\Models\Notification::createNotification(
            $otherUserId,
            'message_received',
            'New Message Received',
            "You have received a new message about {$conversation->domain->full_domain}",
            ['conversation_id' => $conversation->id, 'domain_id' => $conversation->domain_id]
        );

        return response()->json([
            'success' => true,
            'message' => $message->load('sender'),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Message $message)
    {
        $user = Auth::user();
        
        // Check if user has access to this message
        if ($message->sender_id !== $user->id && $message->receiver_id !== $user->id) {
            abort(403, 'Unauthorized access to this message.');
        }

        // Mark message as read if user is the receiver
        if ($message->receiver_id === $user->id && !$message->is_read) {
            $message->markAsRead();
        }

        return view('messages.show', compact('message'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Message $message)
    {
        // Messages cannot be edited after sending
        return redirect()->route('messages.show', $message)
            ->with('error', 'Messages cannot be edited after sending.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Message $message)
    {
        // Messages cannot be updated after sending
        return redirect()->route('messages.show', $message)
            ->with('error', 'Messages cannot be updated after sending.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Message $message)
    {
        $user = Auth::user();
        
        // Only sender can delete their own message
        if ($message->sender_id !== $user->id) {
            abort(403, 'You can only delete your own messages.');
        }

        $message->delete();

        return redirect()->route('messages.index')
            ->with('success', 'Message deleted successfully.');
    }

    /**
     * Show conversation between two users.
     */
    public function conversation(User $otherUser)
    {
        $user = Auth::user();
        
        // Check if user is not viewing conversation with themselves
        if ($otherUser->id === $user->id) {
            return redirect()->route('messages.index')
                ->with('error', 'You cannot view a conversation with yourself.');
        }

        // Get messages between the two users
        $messages = Message::betweenUsers($user->id, $otherUser->id)
            ->with(['sender', 'receiver', 'domain', 'order'])
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark unread messages as read
        $messages->where('receiver_id', $user->id)->where('is_read', false)
            ->each(function ($message) {
                $message->markAsRead();
            });

        return view('messages.conversation', compact('messages', 'otherUser'));
    }

    /**
     * Mark a message as read.
     */
    public function markAsRead(Message $message)
    {
        $user = Auth::user();
        
        // Only receiver can mark message as read
        if ($message->receiver_id !== $user->id) {
            abort(403, 'Only the receiver can mark a message as read.');
        }

        $message->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Send message to domain owner.
     */
    public function contactDomainOwner(Request $request, Domain $domain)
    {
        $user = Auth::user();
        
        // Check if user is not messaging themselves
        if ($domain->user_id === $user->id) {
            return back()->with('error', 'You cannot send a message to yourself.');
        }

        $request->validate([
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        Message::create([
            'sender_id' => $user->id,
            'receiver_id' => $domain->user_id,
            'domain_id' => $domain->id,
            'subject' => $request->subject ?: 'Inquiry about ' . $domain->full_domain,
            'message' => $request->message,
            'type' => 'offer',
        ]);

        return back()->with('success', 'Message sent to domain owner successfully.');
    }

    /**
     * Send message to order participant.
     */
    public function contactOrderParticipant(Request $request, Order $order)
    {
        $user = Auth::user();
        
        // Check if user is part of this order
        if ($order->buyer_id !== $user->id && $order->seller_id !== $user->id) {
            abort(403, 'You are not part of this order.');
        }

        $request->validate([
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        // Determine recipient (the other person in the order)
        $recipientId = $order->buyer_id === $user->id ? $order->seller_id : $order->buyer_id;

        Message::create([
            'sender_id' => $user->id,
            'receiver_id' => $recipientId,
            'order_id' => $order->id,
            'subject' => $request->subject ?: 'Message about Order #' . $order->order_number,
            'message' => $request->message,
            'type' => 'order',
        ]);

        return back()->with('success', 'Message sent successfully.');
    }
}
