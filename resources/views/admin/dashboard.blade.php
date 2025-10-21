@extends('layouts.app')

@section('content')
<div class="space-y-6">
        <!-- Page Header -->
        <div class="md:flex md:items-center md:justify-between">
            <div class="flex-1 min-w-0">
                <div class="flex items-center space-x-4">
                    <div class="p-3 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-3xl font-bold leading-7 text-gray-900 dark:text-white sm:text-4xl sm:truncate">
                            Admin Dashboard
                        </h2>
                        <p class="mt-2 text-lg text-gray-600 dark:text-gray-300">
                            Welcome back, <span class="text-blue-600 dark:text-blue-400 font-semibold">{{ auth()->user()->name }}</span>! Here's what's happening with your marketplace.
                        </p>
                    </div>
                </div>
            </div>
            <div class="mt-4 flex md:mt-0 md:ml-4">
                <button type="button" class="inline-flex items-center px-6 py-3 text-base font-medium text-white bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Export Report
                </button>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Total Users -->
        <x-dashboard.card 
            title="Total Users" 
            value="{{ $stats['total_users'] ?? 0 }}"
            icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path></svg>'
            color="blue"
            trend="up"
            trendValue="+12%"
        />

        <!-- Total Domains -->
        <x-dashboard.card 
            title="Total Domains" 
            value="{{ $stats['total_domains'] ?? 0 }}"
            icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9v-9m0-9v9"></path></svg>'
            color="green"
            trend="up"
            trendValue="+8%"
        />

        <!-- Revenue -->
        <x-dashboard.card 
            title="Total Revenue" 
            value="${{ number_format($stats['total_revenue'] ?? 0, 2) }}"
            icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path></svg>'
            color="purple"
            trend="up"
            trendValue="+23%"
        />

        <!-- Active Auctions -->
        <x-dashboard.card 
            title="Active Auctions" 
            value="{{ $stats['active_auctions'] ?? 0 }}"
            icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
            color="yellow"
        />
    </div>

        <!-- Secondary Stats -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Verified Users -->
        <x-dashboard.card 
            title="Verified Users" 
            value="{{ $stats['verified_users'] ?? 0 }}"
            icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
            color="green"
        />

        <!-- Pending Verifications -->
        <x-dashboard.card 
            title="Pending Verifications" 
            value="{{ $stats['pending_verifications'] ?? 0 }}"
            icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
            color="yellow"
        />

        <!-- Commission Earned -->
        <x-dashboard.card 
            title="Commission Earned" 
            value="${{ number_format($stats['commission_earned'] ?? 0, 2) }}"
            icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>'
            color="indigo"
        />

        <!-- Suspended Users -->
        <x-dashboard.card 
            title="Suspended Users" 
            value="{{ $stats['suspended_users'] ?? 0 }}"
            icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"></path></svg>'
            color="red"
        />
    </div>

        <!-- Charts and Analytics -->
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
        <!-- Revenue Chart -->
        <x-dashboard.chart-card 
            title="Revenue Over Time"
            chartId="revenueChart"
            chartType="line"
            :data="[
                'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                'datasets' => [
                    [
                        'label' => 'Revenue',
                        'data' => [12000, 19000, 15000, 25000, 22000, 30000],
                        'borderColor' => 'rgb(59, 130, 246)',
                        'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                        'tension' => 0.4
                    ]
                ]
            ]"
        />

        <!-- User Growth Chart -->
        <x-dashboard.chart-card 
            title="User Growth"
            chartId="userGrowthChart"
            chartType="bar"
            :data="[
                'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                'datasets' => [
                    [
                        'label' => 'New Users',
                        'data' => [65, 78, 90, 81, 56, 95],
                        'backgroundColor' => 'rgba(34, 197, 94, 0.8)',
                        'borderColor' => 'rgb(34, 197, 94)',
                        'borderWidth' => 1
                    ]
                ]
            ]"
        />
    </div>

        <!-- Recent Activity and Quick Actions -->
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
            <!-- Recent Transactions -->
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-gray-800 shadow-xl rounded-2xl border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-gradient-to-r from-green-500 to-emerald-600 rounded-lg">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Recent Transactions</h3>
                        </div>
                    </div>
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($recentTransactions ?? [] as $transaction)
                            <div class="px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-4">
                                        <div class="flex-shrink-0">
                                            <div class="w-10 h-10 bg-gradient-to-r from-green-500 to-emerald-600 rounded-full flex items-center justify-center shadow-lg">
                                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <p class="text-sm font-semibold text-gray-900 dark:text-white">Domain Sale</p>
                                            <p class="text-sm text-gray-600 dark:text-gray-300">{{ $transaction->domain_name ?? 'example.com' }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-lg font-bold text-green-600 dark:text-green-400">${{ number_format($transaction->amount ?? 1500, 2) }}</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $transaction->created_at ? $transaction->created_at->diffForHumans() : '2 hours ago' }}</p>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="px-6 py-12 text-center">
                                <div class="mx-auto w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">No transactions</h3>
                                <p class="mt-2 text-gray-600 dark:text-gray-300">Get started by approving some domain listings.</p>
                            </div>
                        @endforelse
                    </div>
            </div>
        </div>

            <!-- Quick Actions -->
            <div class="bg-white dark:bg-gray-800 shadow-xl rounded-2xl border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-gradient-to-r from-purple-500 to-pink-600 rounded-lg">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Quick Actions</h3>
                    </div>
                </div>
                <div class="p-6 space-y-4">
                    <x-dashboard.quick-action 
                        title="Approve Users"
                        description="Review pending user registrations"
                        href="{{ route('admin.users.index') }}"
                        icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
                        color="green"
                    />

                    <x-dashboard.quick-action 
                        title="Review Domains"
                        description="Approve or reject domain listings"
                        href="{{ route('admin.domains.index') }}"
                        icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9v-9m0-9v9"></path></svg>'
                        color="blue"
                    />

                    <x-dashboard.quick-action 
                        title="Verify Documents"
                        description="Review government ID submissions"
                        href="{{ route('admin.verifications.index') }}"
                        icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>'
                        color="purple"
                    />

                    <x-dashboard.quick-action 
                        title="Site Settings"
                        description="Configure platform settings"
                        href="{{ route('admin.settings.index') }}"
                        icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>'
                        color="indigo"
                    />
                </div>
            </div>

                <!-- Pending Verifications -->
                <div class="bg-white dark:bg-gray-800 shadow-xl rounded-2xl border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-gradient-to-r from-yellow-500 to-orange-600 rounded-lg">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Pending Verifications</h3>
                        </div>
                    </div>
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($pendingVerifications ?? [] as $verification)
                            <div class="px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $verification->user->name ?? 'John Doe' }}</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ ucfirst(str_replace('_', ' ', $verification->type ?? 'government_id')) }}</p>
                                    </div>
                                    <div class="flex space-x-2">
                                        <button class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300 p-1 rounded-md hover:bg-green-50 dark:hover:bg-green-900/20 transition-colors duration-200">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </button>
                                        <button class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 p-1 rounded-md hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors duration-200">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="px-6 py-8 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">All caught up!</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">No pending verifications.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection