<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display a listing of notifications for the authenticated user.
     */
    public function index()
    {
        $user = Auth::user();
        
        $notifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead(Notification $notification)
    {
        $user = Auth::user();
        
        // Check if user owns this notification
        if ($notification->user_id !== $user->id) {
            abort(403, 'Unauthorized access to notification.');
        }

        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read for the authenticated user.
     */
    public function markAllAsRead()
    {
        $user = Auth::user();
        
        $user->notifications()->unread()->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Get unread notification count for the authenticated user.
     */
    public function unreadCount()
    {
        $user = Auth::user();
        $count = $user->unread_notification_count;
        
        return response()->json(['count' => $count]);
    }

    /**
     * Get recent notifications for the authenticated user (for dropdown).
     */
    public function recent()
    {
        $user = Auth::user();
        
        $notifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json($notifications);
    }

    /**
     * Delete a notification.
     */
    public function destroy(Notification $notification)
    {
        $user = Auth::user();
        
        // Check if user owns this notification
        if ($notification->user_id !== $user->id) {
            abort(403, 'Unauthorized access to notification.');
        }

        $notification->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Delete all notifications for the authenticated user.
     */
    public function destroyAll()
    {
        $user = Auth::user();
        
        $user->notifications()->delete();

        return response()->json(['success' => true]);
    }
}