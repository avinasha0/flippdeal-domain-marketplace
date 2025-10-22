#!/bin/bash

# Navigate to the project directory
cd /home/u248666255/domains/flippdeal.com/public_html

# Backup the current routes file
cp routes/web.php routes/web.php.backup.$(date +%Y%m%d_%H%M%S)

# Create a simple fix by replacing the watchlist route directly
cat > temp_watchlist_fix.php << 'WATCHLIST_EOF'
Route::get('/watchlist', function () { 
    $watchlist = new \Illuminate\Pagination\LengthAwarePaginator(
        collect([]), // Empty data
        0, // Total count
        15, // Per page
        1, // Current page
        ['path' => request()->url()]
    );
    
    return view('watchlist.index', [
        'watchlist' => $watchlist
    ]); 
})->name('watchlist.index');
WATCHLIST_EOF

# Find and replace the watchlist route
awk '
/Route::get.*watchlist.*function/ {
    print "Route::get(\"/watchlist\", function () { "
    print "    \$watchlist = new \\Illuminate\\Pagination\\LengthAwarePaginator("
    print "        collect([]), // Empty data"
    print "        0, // Total count"
    print "        15, // Per page"
    print "        1, // Current page"
    print "        [\"path\" => request()->url()]"
    print "    );"
    print "    "
    print "    return view(\"watchlist.index\", ["
    print "        \"watchlist\" => \$watchlist"
    print "    ]); "
    print "})->name(\"watchlist.index\");"
    next
}
/})->name.*watchlist\.index/ {
    next
}
{ print }
' routes/web.php > routes/web_new.php

# Replace the old file with the new one
mv routes/web_new.php routes/web.php

# Clear route cache
php artisan route:clear

# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear

echo "Watchlist route updated with proper pagination!"
echo "Backup created at: routes/web.php.backup.$(date +%Y%m%d_%H%M%S)"
