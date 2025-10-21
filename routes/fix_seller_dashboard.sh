#!/bin/bash

# Navigate to the project directory
cd /home/u248666255/domains/flippdeal.com/public_html

# Backup the current routes file
cp routes/web.php routes/web.php.backup.$(date +%Y%m%d_%H%M%S)

# Fix the seller dashboard route
sed -i "s|Route::get('/seller-dashboard', function () { return view('dashboard.seller'); })->name('seller.dashboard');|Route::get('/seller-dashboard', function () { \n    return view('dashboard.seller', [\n        'stats' => [\n            'total_listings' => 0,\n            'active_listings' => 0,\n            'sold_listings' => 0,\n            'total_revenue' => 0,\n            'pending_offers' => 0,\n            'draft_listings' => 0\n        ],\n        'recentListings' => collect([]),\n        'recentOffers' => collect([]),\n        'recentSales' => collect([])\n    ]); \n})->name('seller.dashboard');|g" routes/web.php

# Also fix buyer dashboard
sed -i "s|Route::get('/buyer-dashboard', function () { return view('dashboard.buyer'); })->name('buyer.dashboard');|Route::get('/buyer-dashboard', function () { \n    return view('dashboard.buyer', [\n        'stats' => [\n            'total_bids' => 0,\n            'active_bids' => 0,\n            'won_bids' => 0,\n            'total_spent' => 0,\n            'watchlist_count' => 0,\n            'offers_sent' => 0\n        ],\n        'recentBids' => collect([]),\n        'recentWins' => collect([]),\n        'watchlist' => collect([])\n    ]); \n})->name('buyer.dashboard');|g" routes/web.php

# Clear route cache
php artisan route:clear

# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear

echo "Seller and buyer dashboard routes updated with stats!"
echo "Backup created at: routes/web.php.backup.$(date +%Y%m%d_%H%M%S)"
