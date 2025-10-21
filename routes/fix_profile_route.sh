#!/bin/bash

# Navigate to the project directory
cd /home/u248666255/domains/flippdeal.com/public_html

# Backup the current routes file
cp routes/web.php routes/web.php.backup.$(date +%Y%m%d_%H%M%S)

# Fix the profile route to pass the authenticated user
awk '
/Route::get.*profile.*function.*{/ {
    print "Route::get(\"/profile\", function () { "
    print "    return view(\"profile.edit\", ["
    print "        \"user\" => auth()->user()"
    print "    ]); "
    print "})->name(\"profile.edit\");"
    next
}
/})->name.*profile\.edit/ {
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

echo "Profile route updated with user variable!"
echo "Backup created at: routes/web.php.backup.$(date +%Y%m%d_%H%M%S)"
