#!/bin/bash

# Navigate to the project directory
cd /home/u248666255/domains/flippdeal.com/public_html

# Backup the current routes file
cp routes/web.php routes/web.php.backup.$(date +%Y%m%d_%H%M%S)

# Fix the buyer dashboard route with correct stats keys
sed -i "s|'stats' => \[|'stats' => \[|g" routes/web.php
sed -i "s|'total_bids' => 0,|'total_bids' => 0,|g" routes/web.php
sed -i "s|'active_bids' => 0,|'active_bids' => 0,|g" routes/web.php
sed -i "s|'won_bids' => 0,|'winning_bids' => 0,|g" routes/web.php
sed -i "s|'total_spent' => 0,|'total_spent' => 0,|g" routes/web.php
sed -i "s|'watchlist_count' => 0,|'watchlist_count' => 0,|g" routes/web.php
sed -i "s|'offers_sent' => 0|'offers_sent' => 0,\n            'won_auctions' => 0,\n            'pending_payments' => 0,\n            'completed_purchases' => 0|g" routes/web.php

# Clear route cache
php artisan route:clear

# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear

echo "Buyer dashboard stats updated!"
echo "Backup created at: routes/web.php.backup.$(date +%Y%m%d_%H%M%S)"
