@extends('layouts.admin')

@section('title', 'Real-Time System Dashboard')

@section('content')
<div class="container-fluid" x-data="realtimeDashboard()" x-init="init()">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Real-Time System Dashboard</h1>
        <div class="d-flex gap-2">
            <button @click="refreshData()" 
                    class="btn btn-primary btn-sm"
                    :disabled="isLoading">
                <i class="fas fa-sync-alt" :class="{ 'fa-spin': isLoading }"></i>
                Refresh
            </button>
            <button @click="restartQueueWorkers()" 
                    class="btn btn-warning btn-sm"
                    :disabled="isLoading">
                <i class="fas fa-redo"></i>
                Restart Queue
            </button>
            <button @click="clearFailedJobs()" 
                    class="btn btn-danger btn-sm"
                    :disabled="isLoading">
                <i class="fas fa-trash"></i>
                Clear Failed Jobs
            </button>
        </div>
    </div>

    <!-- System Status -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                System Status
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <span :class="systemStatus === 'online' ? 'text-success' : 'text-danger'">
                                    <i class="fas fa-circle"></i>
                                    <span x-text="systemStatus.toUpperCase()"></span>
                                </span>
                                <small class="text-muted ml-2" x-text="lastUpdate"></small>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-server fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Messages
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" x-text="stats.total_messages"></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-comments fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Unread Messages
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" x-text="stats.unread_messages"></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-envelope fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Online Users
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" x-text="stats.online_users"></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Queue Jobs
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" x-text="stats.queue_jobs"></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tasks fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Activity -->
    <div class="row">
        <!-- Recent Activity -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Activity</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>User</th>
                                    <th>Message</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="activity in recentActivity.messages" :key="activity.id">
                                    <tr>
                                        <td>
                                            <span class="badge badge-info">Message</span>
                                        </td>
                                        <td x-text="activity.from_user?.name || 'Unknown'"></td>
                                        <td x-text="activity.body?.substring(0, 50) + '...'"></td>
                                        <td x-text="formatTime(activity.created_at)"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Queue Statistics -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Queue Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="text-center">
                                <div class="h4 text-primary" x-text="queueStats.pending_jobs"></div>
                                <div class="text-muted">Pending Jobs</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <div class="h4 text-danger" x-text="queueStats.failed_jobs"></div>
                                <div class="text-muted">Failed Jobs</div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-12">
                            <div class="text-center">
                                <div class="h4 text-success" x-text="queueStats.processed_jobs"></div>
                                <div class="text-muted">Processed Jobs</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Users -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Active Users</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Last Activity</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="user in activeUsers" :key="user.id">
                                    <tr>
                                        <td x-text="user.name"></td>
                                        <td x-text="user.email"></td>
                                        <td x-text="formatTime(user.last_activity_at)"></td>
                                        <td>
                                            <span class="badge badge-success">Online</span>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function realtimeDashboard() {
    return {
        isLoading: false,
        systemStatus: 'offline',
        lastUpdate: '',
        stats: {
            total_messages: 0,
            unread_messages: 0,
            online_users: 0,
            queue_jobs: 0,
        },
        recentActivity: {
            messages: [],
            notifications: [],
        },
        activeUsers: [],
        queueStats: {
            pending_jobs: 0,
            failed_jobs: 0,
            processed_jobs: 0,
        },
        refreshInterval: null,

        init() {
            this.loadData();
            this.startAutoRefresh();
        },

        async loadData() {
            this.isLoading = true;
            
            try {
                const response = await fetch('/admin/realtime/data');
                const data = await response.json();
                
                this.stats = data.stats;
                this.queueStats = data.queue_stats;
                this.systemStatus = 'online';
                this.lastUpdate = new Date().toLocaleTimeString();
                
            } catch (error) {
                console.error('Failed to load data:', error);
                this.systemStatus = 'offline';
            } finally {
                this.isLoading = false;
            }
        },

        async refreshData() {
            await this.loadData();
        },

        startAutoRefresh() {
            this.refreshInterval = setInterval(() => {
                this.loadData();
            }, 30000); // Refresh every 30 seconds
        },

        async restartQueueWorkers() {
            this.isLoading = true;
            
            try {
                const response = await fetch('/admin/realtime/restart-queue', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                });
                
                const data = await response.json();
                
                if (data.success) {
                    alert('Queue workers restarted successfully');
                } else {
                    alert('Failed to restart queue workers: ' + data.message);
                }
                
            } catch (error) {
                console.error('Failed to restart queue workers:', error);
                alert('Failed to restart queue workers');
            } finally {
                this.isLoading = false;
            }
        },

        async clearFailedJobs() {
            if (!confirm('Are you sure you want to clear all failed jobs?')) {
                return;
            }
            
            this.isLoading = true;
            
            try {
                const response = await fetch('/admin/realtime/clear-failed-jobs', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                });
                
                const data = await response.json();
                
                if (data.success) {
                    alert('Failed jobs cleared successfully');
                    this.loadData();
                } else {
                    alert('Failed to clear jobs: ' + data.message);
                }
                
            } catch (error) {
                console.error('Failed to clear failed jobs:', error);
                alert('Failed to clear failed jobs');
            } finally {
                this.isLoading = false;
            }
        },

        formatTime(timestamp) {
            return new Date(timestamp).toLocaleString();
        }
    }
}
</script>
@endsection
