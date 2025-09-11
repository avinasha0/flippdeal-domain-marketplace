#!/bin/bash

# Navigate to the project directory
cd /home/u248666255/domains/flippdeal.com/public_html

# Backup the current routes file
cp routes/web.php routes/web.php.backup.$(date +%Y%m%d_%H%M%S)

# Fix the seller dashboard route to include the $data variable
sed -i "s|'tab' => request('tab', 'listings')\n    \]); \n})->name('seller.dashboard');|'tab' => request('tab', 'listings'),\n        'data' => collect([])\n    ]); \n})->name('seller.dashboard');|g" routes/web.php

# Also fix buyer dashboard
sed -i "s|'tab' => request('tab', 'bids')\n    \]); \n})->name('buyer.dashboard');|'tab' => request('tab', 'bids'),\n        'data' => collect([])\n    ]); \n})->name('buyer.dashboard');|g" routes/web.php

# Clear route cache
php artisan route:clear

# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear

echo "Seller and buyer dashboard routes updated with data variable!"
echo "Backup created at: routes/web.php.backup.$(date +%Y%m%d_%H%M%S)"
