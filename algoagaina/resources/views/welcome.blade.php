<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'AlgoTrading') }} - Indian Stock Market Algorithmic Trading</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700" rel="stylesheet" />
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 50%, #3b82f6 100%);
        }
        .hero-gradient {
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 50%, #3b82f6 100%);
        }
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        .trading-chart {
            background: linear-gradient(45deg, #0f172a 25%, transparent 25%), 
                        linear-gradient(-45deg, #0f172a 25%, transparent 25%), 
                        linear-gradient(45deg, transparent 75%, #0f172a 75%), 
                        linear-gradient(-45deg, transparent 75%, #0f172a 75%);
            background-size: 20px 20px;
            background-position: 0 0, 0 10px, 10px -10px, -10px 0px;
        }
    </style>
</head>
<body class="antialiased">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <h1 class="text-2xl font-bold text-blue-900">AlgoTrading</h1>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="#features" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">Features</a>
                    <a href="#strategies" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">Strategies</a>
                    <a href="#about" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">About</a>
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">Dashboard</a>
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">Logout</button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">Log in</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">Register</a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="hero-gradient text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
            <div class="text-center">
                <h1 class="text-4xl md:text-6xl font-bold mb-6">
                    Indian Stock Market <span class="text-yellow-300">Algo Trading</span>
                </h1>
                <p class="text-xl md:text-2xl mb-8 text-blue-100">
                    Advanced algorithmic trading platform for NSE & BSE markets
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="#features" class="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition duration-300">
                        Explore Features
                    </a>
                    @auth
                        <a href="{{ url('/dashboard') }}" class="border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-blue-600 transition duration-300">
                            Go to Dashboard
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-blue-600 transition duration-300">
                            Get Started
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <!-- Market Stats Section -->
    <div class="bg-gray-50 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                <div class="bg-white rounded-lg p-6 shadow-md">
                    <div class="text-3xl font-bold text-green-600 mb-2">₹2.5L+</div>
                    <div class="text-gray-600 text-sm">Daily Trading Volume</div>
                </div>
                <div class="bg-white rounded-lg p-6 shadow-md">
                    <div class="text-3xl font-bold text-blue-600 mb-2">500+</div>
                    <div class="text-gray-600 text-sm">Active Strategies</div>
                </div>
                <div class="bg-white rounded-lg p-6 shadow-md">
                    <div class="text-3xl font-bold text-purple-600 mb-2">98.5%</div>
                    <div class="text-gray-600 text-sm">Success Rate</div>
                </div>
                <div class="bg-white rounded-lg p-6 shadow-md">
                    <div class="text-3xl font-bold text-orange-600 mb-2">24/7</div>
                    <div class="text-gray-600 text-sm">Market Monitoring</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div id="features" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                    Advanced Trading Features
                </h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Professional algorithmic trading platform designed specifically for Indian stock markets
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="bg-white rounded-lg p-8 shadow-lg card-hover border border-gray-200">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Real-time Data</h3>
                    <p class="text-gray-600">Live NSE & BSE market data with millisecond precision for accurate trading decisions.</p>
                </div>

                <!-- Feature 2 -->
                <div class="bg-white rounded-lg p-8 shadow-lg card-hover border border-gray-200">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Advanced Analytics</h3>
                    <p class="text-gray-600">Technical indicators, pattern recognition, and AI-powered market analysis.</p>
                </div>

                <!-- Feature 3 -->
                <div class="bg-white rounded-lg p-8 shadow-lg card-hover border border-gray-200">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Risk Management</h3>
                    <p class="text-gray-600">Advanced stop-loss, position sizing, and portfolio risk controls.</p>
                </div>

                <!-- Feature 4 -->
                <div class="bg-white rounded-lg p-8 shadow-lg card-hover border border-gray-200">
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">24/7 Execution</h3>
                    <p class="text-gray-600">Automated order execution with high-frequency trading capabilities.</p>
                </div>

                <!-- Feature 5 -->
                <div class="bg-white rounded-lg p-8 shadow-lg card-hover border border-gray-200">
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">SEBI Compliant</h3>
                    <p class="text-gray-600">Full compliance with SEBI regulations and exchange guidelines.</p>
                </div>

                <!-- Feature 6 -->
                <div class="bg-white rounded-lg p-8 shadow-lg card-hover border border-gray-200">
                    <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Backtesting</h3>
                    <p class="text-gray-600">Comprehensive strategy backtesting with historical data analysis.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Trading Strategies Section -->
    <div id="strategies" class="py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                    Popular Trading Strategies
                </h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Pre-built strategies optimized for Indian market conditions
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="bg-white rounded-lg p-6 shadow-lg">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-4">
                            <span class="text-green-600 font-bold">1</span>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">Momentum Trading</h3>
                    </div>
                    <p class="text-gray-600 mb-4">Capitalize on short-term price movements with high-frequency momentum strategies.</p>
                    <div class="text-sm text-gray-500">
                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded">Success Rate: 85%</span>
                    </div>
                </div>

                <div class="bg-white rounded-lg p-6 shadow-lg">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                            <span class="text-blue-600 font-bold">2</span>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">Arbitrage Trading</h3>
                    </div>
                    <p class="text-gray-600 mb-4">Exploit price differences between NSE and BSE for risk-free profits.</p>
                    <div class="text-sm text-gray-500">
                        <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded">Success Rate: 92%</span>
                    </div>
                </div>

                <div class="bg-white rounded-lg p-6 shadow-lg">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center mr-4">
                            <span class="text-purple-600 font-bold">3</span>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">Mean Reversion</h3>
                    </div>
                    <p class="text-gray-600 mb-4">Trade based on statistical patterns and price mean reversion.</p>
                    <div class="text-sm text-gray-500">
                        <span class="bg-purple-100 text-purple-800 px-2 py-1 rounded">Success Rate: 78%</span>
                    </div>
                </div>

                <div class="bg-white rounded-lg p-6 shadow-lg">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center mr-4">
                            <span class="text-yellow-600 font-bold">4</span>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">Options Trading</h3>
                    </div>
                    <p class="text-gray-600 mb-4">Advanced options strategies with automated Greeks management.</p>
                    <div class="text-sm text-gray-500">
                        <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded">Success Rate: 82%</span>
                    </div>
                </div>

                <div class="bg-white rounded-lg p-6 shadow-lg">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center mr-4">
                            <span class="text-red-600 font-bold">5</span>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">News Trading</h3>
                    </div>
                    <p class="text-gray-600 mb-4">AI-powered news sentiment analysis for rapid market reactions.</p>
                    <div class="text-sm text-gray-500">
                        <span class="bg-red-100 text-red-800 px-2 py-1 rounded">Success Rate: 75%</span>
                    </div>
                </div>

                <div class="bg-white rounded-lg p-6 shadow-lg">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center mr-4">
                            <span class="text-indigo-600 font-bold">6</span>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">Custom Strategies</h3>
                    </div>
                    <p class="text-gray-600 mb-4">Build your own strategies with our visual strategy builder.</p>
                    <div class="text-sm text-gray-500">
                        <span class="bg-indigo-100 text-indigo-800 px-2 py-1 rounded">Flexible</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Demo Section -->
    <div id="demo" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                    Platform Demo
                </h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    See our algorithmic trading platform in action
                </p>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-6">Real-time Trading Dashboard</h3>
                    <p class="text-gray-600 mb-6">
                        Experience our advanced trading interface with live market data, real-time charts, and automated order execution. Monitor your portfolio performance and track strategy performance in real-time.
                    </p>
                    <div class="space-y-4">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">Live NSE & BSE data feeds</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">Advanced charting with indicators</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">Automated order execution</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">Portfolio analytics & reporting</span>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-900 rounded-lg p-8 trading-chart">
                    <div class="text-center text-white">
                        <div class="text-2xl font-bold mb-4">Trading Dashboard</div>
                        <div class="text-gray-400 mb-6">Live Market Data</div>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div class="bg-gray-800 p-3 rounded">
                                <div class="text-green-400">NIFTY 50</div>
                                <div class="text-xl">₹19,850.25</div>
                                <div class="text-green-400 text-xs">+1.25%</div>
                            </div>
                            <div class="bg-gray-800 p-3 rounded">
                                <div class="text-blue-400">BANK NIFTY</div>
                                <div class="text-xl">₹44,125.50</div>
                                <div class="text-red-400 text-xs">-0.85%</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- About Section -->
    <div id="about" class="py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                    About AlgoTrading
                </h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Leading algorithmic trading platform for Indian stock markets
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-6">Why Choose AlgoTrading?</h3>
                    <p class="text-gray-600 mb-6">
                        We specialize in algorithmic trading solutions designed specifically for the Indian stock market. Our platform combines cutting-edge technology with deep market knowledge to deliver consistent returns.
                    </p>
                    <div class="space-y-4">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">SEBI registered trading platform</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">Direct exchange connectivity</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">24/7 technical support</span>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg p-8 shadow-lg">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Platform Statistics</h4>
                    <div class="space-y-4 text-sm text-gray-600">
                        <div class="flex justify-between">
                            <span>Total Users:</span>
                            <span class="font-semibold">10,000+</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Daily Trades:</span>
                            <span class="font-semibold">50,000+</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Success Rate:</span>
                            <span class="font-semibold text-green-600">98.5%</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Uptime:</span>
                            <span class="font-semibold text-blue-600">99.9%</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Supported Exchanges:</span>
                            <span class="font-semibold">NSE, BSE</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="bg-blue-900 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl md:text-4xl font-bold mb-4">
                Start Your Algo Trading Journey
            </h2>
            <p class="text-xl text-blue-100 mb-8 max-w-2xl mx-auto">
                Join thousands of successful traders using our algorithmic trading platform
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                @auth
                    <a href="{{ url('/dashboard') }}" class="bg-white text-blue-900 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition duration-300">
                        Go to Dashboard
                    </a>
                @else
                    <a href="{{ route('register') }}" class="bg-white text-blue-900 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition duration-300">
                        Get Started Today
                    </a>
                @endauth
                <a href="#demo" class="border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-blue-900 transition duration-300">
                    Schedule Demo
                </a>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer id="contact" class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-lg font-semibold mb-4">AlgoTrading</h3>
                    <p class="text-gray-400">
                        Advanced algorithmic trading platform for Indian stock markets.
                    </p>
                </div>
                <div>
                    <h4 class="text-sm font-semibold mb-4">Trading</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#features" class="hover:text-white">Features</a></li>
                        <li><a href="#strategies" class="hover:text-white">Strategies</a></li>
                        <li><a href="#demo" class="hover:text-white">Demo</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-sm font-semibold mb-4">Support</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white">Documentation</a></li>
                        <li><a href="#" class="hover:text-white">API Reference</a></li>
                        <li><a href="#" class="hover:text-white">Contact Support</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-sm font-semibold mb-4">Contact</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li>📧 info@algotrading.com</li>
                        <li>📞 +91 98765 43210</li>
                        <li>🏢 Mumbai, India</li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; {{ date('Y') }} AlgoTrading. All rights reserved. | SEBI Registered</p>
            </div>
        </div>
    </footer>
</body>
</html>
