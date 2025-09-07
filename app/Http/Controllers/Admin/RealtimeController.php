<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Notification;
use App\Models\Domain;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class RealtimeController extends Controller
{
    /**
     * Display real-time system dashboard.
     */
    public function index()
    {
        $stats = $this->getSystemStats();
        $recentActivity = $this->getRecentActivity();
        $activeUsers = $this->getActiveUsers();
        $queueStats = $this->getQueueStats();

        return view('admin.realtime.index', compact(
            'stats',
            'recentActivity',
            'activeUsers',
            'queueStats'
        ));
    }

    /**
     * Get system statistics.
     */
    private function getSystemStats()
    {
        return [
            'total_messages' => Message::count(),
            'unread_messages' => Message::where('is_read', false)->count(),
            'total_notifications' => Notification::count(),
            'unread_notifications' => Notification::where('is_read', false)->count(),
            'active_auctions' => Domain::where('status', 'active')
                ->where('enable_bidding', true)
                ->where('auction_end', '>', now())
                ->count(),
            'online_users' => $this->getOnlineUsersCount(),
            'queue_jobs' => $this->getQueueJobsCount(),
        ];
    }

    /**
     * Get recent activity.
     */
    private function getRecentActivity()
    {
        $recentMessages = Message::with(['fromUser', 'toUser', 'domain'])
            ->latest()
            ->limit(10)
            ->get();

        $recentNotifications = Notification::with('notifiable')
            ->latest()
            ->limit(10)
            ->get();

        return [
            'messages' => $recentMessages,
            'notifications' => $recentNotifications,
        ];
    }

    /**
     * Get active users.
     */
    private function getActiveUsers()
    {
        // Get users who have been active in the last 5 minutes
        $activeUsers = User::where('last_activity_at', '>=', now()->subMinutes(5))
            ->orderBy('last_activity_at', 'desc')
            ->limit(20)
            ->get();

        return $activeUsers;
    }

    /**
     * Get queue statistics.
     */
    private function getQueueStats()
    {
        try {
            $redis = Redis::connection();
            
            return [
                'pending_jobs' => $redis->llen('queues:default'),
                'failed_jobs' => $redis->llen('queues:failed'),
                'processed_jobs' => Cache::get('queue:processed', 0),
            ];
        } catch (\Exception $e) {
            return [
                'pending_jobs' => 0,
                'failed_jobs' => 0,
                'processed_jobs' => 0,
            ];
        }
    }

    /**
     * Get online users count.
     */
    private function getOnlineUsersCount()
    {
        try {
            $redis = Redis::connection();
            $keys = $redis->keys('presence:*');
            $onlineCount = 0;

            foreach ($keys as $key) {
                $members = $redis->hgetall($key);
                $onlineCount += count($members);
            }

            return $onlineCount;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get queue jobs count.
     */
    private function getQueueJobsCount()
    {
        try {
            $redis = Redis::connection();
            return $redis->llen('queues:default');
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get broadcasting statistics.
     */
    public function broadcastingStats()
    {
        $stats = [
            'channels' => $this->getChannelStats(),
            'events' => $this->getEventStats(),
            'connections' => $this->getConnectionStats(),
        ];

        return response()->json($stats);
    }

    /**
     * Get channel statistics.
     */
    private function getChannelStats()
    {
        try {
            $redis = Redis::connection();
            
            $userChannels = $redis->keys('presence:user.*');
            $domainChannels = $redis->keys('presence:domain.*');
            $auctionChannels = $redis->keys('presence:auction.*');

            return [
                'user_channels' => count($userChannels),
                'domain_channels' => count($domainChannels),
                'auction_channels' => count($auctionChannels),
                'total_channels' => count($userChannels) + count($domainChannels) + count($auctionChannels),
            ];
        } catch (\Exception $e) {
            return [
                'user_channels' => 0,
                'domain_channels' => 0,
                'auction_channels' => 0,
                'total_channels' => 0,
            ];
        }
    }

    /**
     * Get event statistics.
     */
    private function getEventStats()
    {
        $today = now()->startOfDay();
        
        return [
            'messages_today' => Message::where('created_at', '>=', $today)->count(),
            'notifications_today' => Notification::where('created_at', '>=', $today)->count(),
            'bids_today' => DB::table('bids')->where('created_at', '>=', $today)->count(),
        ];
    }

    /**
     * Get connection statistics.
     */
    private function getConnectionStats()
    {
        try {
            $redis = Redis::connection();
            $connections = $redis->keys('presence:*');
            
            $totalConnections = 0;
            foreach ($connections as $connection) {
                $members = $redis->hgetall($connection);
                $totalConnections += count($members);
            }

            return [
                'total_connections' => $totalConnections,
                'active_channels' => count($connections),
            ];
        } catch (\Exception $e) {
            return [
                'total_connections' => 0,
                'active_channels' => 0,
            ];
        }
    }

    /**
     * Clear failed queue jobs.
     */
    public function clearFailedJobs()
    {
        try {
            \Artisan::call('queue:flush');
            
            return response()->json([
                'success' => true,
                'message' => 'Failed jobs cleared successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear jobs: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Restart queue workers.
     */
    public function restartQueueWorkers()
    {
        try {
            \Artisan::call('queue:restart');
            
            return response()->json([
                'success' => true,
                'message' => 'Queue workers restarted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to restart workers: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get real-time data for dashboard.
     */
    public function realtimeData()
    {
        $data = [
            'timestamp' => now()->toISOString(),
            'stats' => $this->getSystemStats(),
            'queue_stats' => $this->getQueueStats(),
            'online_users' => $this->getOnlineUsersCount(),
        ];

        return response()->json($data);
    }

    /**
     * Export system logs.
     */
    public function exportLogs(Request $request)
    {
        $request->validate([
            'type' => 'required|in:messages,notifications,queue',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        $type = $request->get('type');
        $dateFrom = $request->get('date_from', now()->subDays(7));
        $dateTo = $request->get('date_to', now());

        switch ($type) {
            case 'messages':
                $data = Message::with(['fromUser', 'toUser', 'domain'])
                    ->whereBetween('created_at', [$dateFrom, $dateTo])
                    ->get();
                break;
            case 'notifications':
                $data = Notification::with('notifiable')
                    ->whereBetween('created_at', [$dateFrom, $dateTo])
                    ->get();
                break;
            case 'queue':
                $data = DB::table('jobs')
                    ->whereBetween('created_at', [$dateFrom, $dateTo])
                    ->get();
                break;
        }

        $filename = "{$type}_export_" . now()->format('Y-m-d_H-i-s') . '.json';
        
        return response()->json($data)
            ->header('Content-Disposition', "attachment; filename={$filename}");
    }
}