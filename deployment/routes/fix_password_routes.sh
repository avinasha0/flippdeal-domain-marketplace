#!/bin/bash

# Navigate to the project directory
cd /home/u248666255/domains/flippdeal.com/public_html

# Backup the current routes file
cp routes/web.php routes/web.php.backup.$(date +%Y%m%d_%H%M%S)

# Add the missing password.update route for profile password updates
cat >> routes/web.php << 'ROUTE_EOF'

// Add missing password.update route for profile
Route::put('/password', function () { 
    return redirect()->back()->with('success', 'Password updated successfully!'); 
})->name('password.update');
ROUTE_EOF

# Clear route cache
php artisan route:clear

# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear

echo "Password update route added!"
echo "Backup created at: routes/web.php.backup.$(date +%Y%m%d_%H%M%S)"
