<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    /**
     * Get recent notifications for the notification bell.
     */
    public function recent(): JsonResponse
    {
        $user = Auth::user();
        
        // Get recent message notifications
        $messageNotifications = $user->notifications()
            ->where('type', 'App\Notifications\NewMessageReceived')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($notification) {
                $data = $notification->data;
                return [
                    'id' => $notification->id,
                    'title' => 'New message from ' . ($data['from_user'] ?? 'Unknown'),
                    'message' => $data['message'] ?? 'You have a new message',
                    'sender' => $data['from_user'] ?? 'Unknown',
                    'time' => $notification->created_at->diffForHumans(),
                    'read' => $notification->read_at !== null,
                    'url' => route('conversations.show', $data['conversation_id'] ?? 1)
                ];
            });

        return response()->json([
            'notifications' => $messageNotifications,
            'unread_count' => $user->unread_conversation_count
        ]);
    }

    /**
     * Get user notifications.
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'limit' => 'nullable|integer|min:1|max:100',
            'page' => 'nullable|integer|min:1',
            'unread_only' => 'nullable|boolean',
        ]);

        $limit = $request->get('limit', 20);
        $unreadOnly = $request->get('unread_only', false);

        $query = Auth::user()->notifications()
            ->orderBy('created_at', 'desc');

        if ($unreadOnly) {
            $query->whereNull('read_at');
        }

        $notifications = $query->paginate($limit);

        return response()->json([
            'notifications' => $notifications->items(),
            'pagination' => [
                'current_page' => $notifications->currentPage(),
                'last_page' => $notifications->lastPage(),
                'per_page' => $notifications->perPage(),
                'total' => $notifications->total(),
            ],
        ]);
    }

    /**
     * Get unread notification count.
     */
    public function unreadCount(): JsonResponse
    {
        $count = Auth::user()->unreadNotifications()->count();

        return response()->json([
            'unread_count' => $count,
        ]);
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead(Request $request, string $id): JsonResponse
    {
        $notification = Auth::user()->notifications()->findOrFail($id);

        if (!$notification->read_at) {
            $notification->markAsRead();
        }

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read',
        ]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(): JsonResponse
    {
        $count = Auth::user()->unreadNotifications()->update(['read_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => "Marked {$count} notifications as read",
        ]);
    }

    /**
     * Delete a notification.
     */
    public function destroy(string $id): JsonResponse
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notification deleted',
        ]);
    }

    /**
     * Delete all notifications.
     */
    public function destroyAll(): JsonResponse
    {
        $count = Auth::user()->notifications()->delete();

        return response()->json([
            'success' => true,
            'message' => "Deleted {$count} notifications",
        ]);
    }

    /**
     * Get notification statistics.
     */
    public function statistics(): JsonResponse
    {
        $stats = [
            'total' => Auth::user()->notifications()->count(),
            'unread' => Auth::user()->unreadNotifications()->count(),
            'read' => Auth::user()->readNotifications()->count(),
            'by_type' => Auth::user()->notifications()
                ->select('type', DB::raw('count(*) as count'))
                ->groupBy('type')
                ->pluck('count', 'type')
                ->toArray(),
        ];

        return response()->json($stats);
    }
}