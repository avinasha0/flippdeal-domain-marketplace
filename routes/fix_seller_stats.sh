#!/bin/bash

# Navigate to the project directory
cd /home/u248666255/domains/flippdeal.com/public_html

# Backup the current routes file
cp routes/web.php routes/web.php.backup.$(date +%Y%m%d_%H%M%S)

# Fix the seller dashboard route with correct stats keys
sed -i "s|'stats' => \[|'stats' => \[|g" routes/web.php
sed -i "s|'total_listings' => 0,|'total_listings' => 0,|g" routes/web.php
sed -i "s|'active_listings' => 0,|'active_listings' => 0,|g" routes/web.php
sed -i "s|'sold_listings' => 0,|'sold_listings' => 0,|g" routes/web.php
sed -i "s|'total_revenue' => 0,|'total_sales' => 0,|g" routes/web.php
sed -i "s|'pending_offers' => 0,|'pending_offers' => 0,|g" routes/web.php
sed -i "s|'draft_listings' => 0|'draft_listings' => 0|g" routes/web.php

# Also add missing stats that the view might need
sed -i "s|'draft_listings' => 0|'draft_listings' => 0,\n            'total_revenue' => 0,\n            'monthly_sales' => 0,\n            'conversion_rate' => 0|g" routes/web.php

# Add tab variable
sed -i "s|'recentSales' => collect(\[\])\n    \]); \n})->name('seller.dashboard');|'recentSales' => collect([]),\n        'tab' => request('tab', 'listings')\n    ]); \n})->name('seller.dashboard');|g" routes/web.php

# Clear route cache
php artisan route:clear

# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear

echo "Seller dashboard stats updated!"
echo "Backup created at: routes/web.php.backup.$(date +%Y%m%d_%H%M%S)"
