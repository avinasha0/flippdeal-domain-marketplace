#!/bin/bash

# Navigate to the project directory
cd /home/u248666255/domains/flippdeal.com/public_html

# Backup the current routes file
cp routes/web.php routes/web.php.backup.$(date +%Y%m%d_%H%M%S)

# Fix the watchlist route to use proper pagination
sed -i "s|Route::get('/watchlist', function () { \n    return view('watchlist.index', [\n        'watchlist' => collect([])\n    ]); \n})->name('watchlist.index');|Route::get('/watchlist', function () { \n    \$watchlist = new \\Illuminate\\Pagination\\LengthAwarePaginator(\n        collect([]), // Empty data\n        0, // Total count\n        15, // Per page\n        1, // Current page\n        ['path' => request()->url()]\n    );\n    \n    return view('watchlist.index', [\n        'watchlist' => \$watchlist\n    ]); \n})->name('watchlist.index');|g" routes/web.php

# Also fix other routes that might have the same issue
sed -i "s|Route::get('/offers', function () { \n    return view('offers.index', [\n        'offers' => collect([])\n    ]); \n})->name('offers.index');|Route::get('/offers', function () { \n    \$offers = new \\Illuminate\\Pagination\\LengthAwarePaginator(\n        collect([]),\n        0,\n        15,\n        1,\n        ['path' => request()->url()]\n    );\n    \n    return view('offers.index', [\n        'offers' => \$offers\n    ]); \n})->name('offers.index');|g" routes/web.php

sed -i "s|Route::get('/orders', function () { \n    return view('orders.index', [\n        'orders' => collect([])\n    ]); \n})->name('orders.index');|Route::get('/orders', function () { \n    \$orders = new \\Illuminate\\Pagination\\LengthAwarePaginator(\n        collect([]),\n        0,\n        15,\n        1,\n        ['path' => request()->url()]\n    );\n    \n    return view('orders.index', [\n        'orders' => \$orders\n    ]); \n})->name('orders.index');|g" routes/web.php

# Clear route cache
php artisan route:clear

# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear

echo "Watchlist and other routes updated with proper pagination!"
echo "Backup created at: routes/web.php.backup.$(date +%Y%m%d_%H%M%S)"
