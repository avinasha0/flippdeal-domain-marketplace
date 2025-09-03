<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Trading Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Welcome Section -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-lg p-6 mb-6 text-white">
                <h3 class="text-2xl font-bold mb-2">Welcome back, {{ Auth::user()->name }}!</h3>
                <p class="text-blue-100">Your algorithmic trading dashboard is ready for today's market session.</p>
            </div>

            <!-- Market Overview -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-green-100 rounded-lg">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Portfolio Value</p>
                            <p class="text-2xl font-bold text-gray-900">₹2,45,000</p>
                            <p class="text-sm text-green-600">+₹12,500 (+5.4%)</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-100 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Active Strategies</p>
                            <p class="text-2xl font-bold text-gray-900">8</p>
                            <p class="text-sm text-blue-600">Running smoothly</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-purple-100 rounded-lg">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Today's P&L</p>
                            <p class="text-2xl font-bold text-gray-900">₹8,750</p>
                            <p class="text-sm text-green-600">+3.7%</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-orange-100 rounded-lg">
                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Success Rate</p>
                            <p class="text-2xl font-bold text-gray-900">94.2%</p>
                            <p class="text-sm text-green-600">This month</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Market Data -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <div class="bg-white rounded-lg shadow">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Live Market Data</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div class="flex justify-between items-center p-3 bg-gray-50 rounded">
                                <div>
                                    <p class="font-medium text-gray-900">NIFTY 50</p>
                                    <p class="text-sm text-gray-600">National Stock Exchange</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-gray-900">₹19,850.25</p>
                                    <p class="text-sm text-green-600">+1.25%</p>
                                </div>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-gray-50 rounded">
                                <div>
                                    <p class="font-medium text-gray-900">BANK NIFTY</p>
                                    <p class="text-sm text-gray-600">Banking Index</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-gray-900">₹44,125.50</p>
                                    <p class="text-sm text-red-600">-0.85%</p>
                                </div>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-gray-50 rounded">
                                <div>
                                    <p class="font-medium text-gray-900">SENSEX</p>
                                    <p class="text-sm text-gray-600">Bombay Stock Exchange</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-gray-900">₹66,250.75</p>
                                    <p class="text-sm text-green-600">+0.95%</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Active Strategies</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-3 bg-green-50 rounded border-l-4 border-green-500">
                                <div>
                                    <p class="font-medium text-gray-900">Momentum Trading</p>
                                    <p class="text-sm text-gray-600">Running • 85% success rate</p>
                                </div>
                                <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded">Active</span>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-blue-50 rounded border-l-4 border-blue-500">
                                <div>
                                    <p class="font-medium text-gray-900">Arbitrage Trading</p>
                                    <p class="text-sm text-gray-600">Running • 92% success rate</p>
                                </div>
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded">Active</span>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-purple-50 rounded border-l-4 border-purple-500">
                                <div>
                                    <p class="font-medium text-gray-900">Options Trading</p>
                                    <p class="text-sm text-gray-600">Running • 82% success rate</p>
                                </div>
                                <span class="px-2 py-1 bg-purple-100 text-purple-800 text-xs rounded">Active</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Trades -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Recent Trades</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Symbol</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">P&L</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">RELIANCE</div>
                                    <div class="text-sm text-gray-500">NSE</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded">BUY</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">100</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">₹2,450.00</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">+₹1,250</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">09:45 AM</td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">TCS</div>
                                    <div class="text-sm text-gray-500">NSE</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded">SELL</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">50</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">₹3,850.00</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">+₹750</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">10:15 AM</td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">INFY</div>
                                    <div class="text-sm text-gray-500">NSE</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded">BUY</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">75</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">₹1,650.00</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">-₹225</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">11:30 AM</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                <a href="#" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-100 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">Create Strategy</h3>
                            <p class="text-sm text-gray-600">Build new trading strategies</p>
                        </div>
                    </div>
                </a>

                <a href="#" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                    <div class="flex items-center">
                        <div class="p-2 bg-green-100 rounded-lg">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">View Reports</h3>
                            <p class="text-sm text-gray-600">Analyze performance data</p>
                        </div>
                    </div>
                </a>

                <a href="#" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                    <div class="flex items-center">
                        <div class="p-2 bg-purple-100 rounded-lg">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">Settings</h3>
                            <p class="text-sm text-gray-600">Configure trading parameters</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
