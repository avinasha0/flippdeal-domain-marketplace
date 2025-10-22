#!/bin/bash

# Navigate to the project directory
cd /home/u248666255/domains/flippdeal.com/public_html

# Backup the current routes file
cp routes/web.php routes/web.php.backup.$(date +%Y%m%d_%H%M%S)

# Check if password.update route exists
echo "Checking for password.update route..."
grep -n "password.update" routes/web.php || echo "Route not found"

# Add the missing password.update route before the closing PHP tag
sed -i '/^?>$/i\
\
// Add missing password.update route for profile\
Route::put("/password", function () { \
    return redirect()->back()->with("success", "Password updated successfully!"); \
})->name("password.update");' routes/web.php

# Also add profile.update route if missing
grep -q "profile.update" routes/web.php || sed -i '/^?>$/i\
\
// Add missing profile.update route\
Route::patch("/profile", function () { \
    return redirect()->back()->with("success", "Profile updated successfully!"); \
})->name("profile.update");' routes/web.php

# Clear route cache
php artisan route:clear

# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear

echo "Routes updated!"
echo "Backup created at: routes/web.php.backup.$(date +%Y%m%d_%H%M%S)"

# Show the last few lines of the routes file to confirm
echo "Last 10 lines of routes file:"
tail -10 routes/web.php
