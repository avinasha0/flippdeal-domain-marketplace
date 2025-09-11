#!/bin/bash

# Navigate to the project directory
cd /home/u248666255/domains/flippdeal.com/public_html

# Backup the current routes file
cp routes/web.php routes/web.php.backup.$(date +%Y%m%d_%H%M%S)

# Add the missing login POST route after the login GET route
sed -i "/Route::get('\/login', function () { return view('auth.login'); })->name('login');/a\\
\\
// Login POST route\\
Route::post('/login', function () { \\
    \$credentials = request()->only('email', 'password'); \\
    \$remember = request()->has('remember'); \\
    \\
    if (Auth::attempt(\$credentials, \$remember)) { \\
        request()->session()->regenerate(); \\
        return redirect()->intended('/dashboard'); \\
    } \\
    \\
    return back()->withErrors([ \\
        'email' => 'The provided credentials do not match our records.', \\
    ])->onlyInput('email'); \\
})->name('login');" routes/web.php

# Clear route cache
php artisan route:clear

# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear

echo "Login POST route added!"
echo "Backup created at: routes/web.php.backup.$(date +%Y%m%d_%H%M%S)"
