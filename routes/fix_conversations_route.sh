#!/bin/bash

# Navigate to the project directory
cd /home/u248666255/domains/flippdeal.com/public_html

# Backup the current routes file
cp routes/web.php routes/web.php.backup.$(date +%Y%m%d_%H%M%S)

# Update the conversations route
sed -i "s|Route::get('/conversations', function () { return view('conversations.index'); })->name('conversations.index');|Route::get('/conversations', function () { \n    return view('conversations.index', [\n        'conversations' => collect([]), // Empty collection for now\n        'unreadCount' => 0\n    ]); \n})->name('conversations.index');|g" routes/web.php

# Also fix other routes that might have similar issues
sed -i "s|Route::get('/messages', function () { return view('messages.index'); })->name('messages.index');|Route::get('/messages', function () { \n    return view('messages.index', [\n        'messages' => collect([]),\n        'unreadCount' => 0\n    ]); \n})->name('messages.index');|g" routes/web.php

sed -i "s|Route::get('/offers', function () { return view('offers.index'); })->name('offers.index');|Route::get('/offers', function () { \n    return view('offers.index', [\n        'offers' => collect([])\n    ]); \n})->name('offers.index');|g" routes/web.php

sed -i "s|Route::get('/orders', function () { return view('orders.index'); })->name('orders.index');|Route::get('/orders', function () { \n    return view('orders.index', [\n        'orders' => collect([])\n    ]); \n})->name('orders.index');|g" routes/web.php

sed -i "s|Route::get('/watchlist', function () { return view('watchlist.index'); })->name('watchlist.index');|Route::get('/watchlist', function () { \n    return view('watchlist.index', [\n        'watchlist' => collect([])\n    ]); \n})->name('watchlist.index');|g" routes/web.php

# Clear route cache
php artisan route:clear

# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear

echo "Conversations route and other routes updated successfully!"
echo "Backup created at: routes/web.php.backup.$(date +%Y%m%d_%H%M%S)"
