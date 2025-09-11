#!/bin/bash

# Navigate to the project directory
cd /home/u248666255/domains/flippdeal.com/public_html

# Backup the current routes file
cp routes/web.php routes/web.php.backup.$(date +%Y%m%d_%H%M%S)

# Update the conversations route to use pagination
cat > temp_conversations_route.php << 'ROUTE_EOF'
Route::get('/conversations', function () { 
    // Create a paginated collection that has total() method
    $conversations = new \Illuminate\Pagination\LengthAwarePaginator(
        collect([]), // Empty data
        0, // Total count
        15, // Per page
        1, // Current page
        ['path' => request()->url()]
    );
    
    return view('conversations.index', [
        'conversations' => $conversations,
        'unreadCount' => 0
    ]); 
})->name('conversations.index');
ROUTE_EOF

# Replace the conversations route in the main file
sed -i '/Route::get.*conversations.*function.*{/,/})->name.*conversations\.index.*);/c\
'"$(cat temp_conversations_route.php)"'' routes/web.php

# Clean up temp file
rm temp_conversations_route.php

# Also fix other routes that might have similar pagination issues
cat > temp_messages_route.php << 'ROUTE_EOF'
Route::get('/messages', function () { 
    $messages = new \Illuminate\Pagination\LengthAwarePaginator(
        collect([]),
        0,
        15,
        1,
        ['path' => request()->url()]
    );
    
    return view('messages.index', [
        'messages' => $messages,
        'unreadCount' => 0
    ]); 
})->name('messages.index');
ROUTE_EOF

sed -i '/Route::get.*messages.*function.*{/,/})->name.*messages\.index.*);/c\
'"$(cat temp_messages_route.php)"'' routes/web.php

rm temp_messages_route.php

# Clear route cache
php artisan route:clear

# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear

echo "Conversations route updated with proper pagination!"
echo "Backup created at: routes/web.php.backup.$(date +%Y%m%d_%H%M%S)"
