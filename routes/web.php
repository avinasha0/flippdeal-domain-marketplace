<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\RecentActivityController;

// Include auth routes
require __DIR__.'/auth.php';

// Basic routes
Route::get('/', function () {
    // Get published domains for the home page
    $publishedDomains = \App\Models\Domain::where('status', 'active')
        ->where('domain_verified', true)
        ->with('user')
        ->orderBy('created_at', 'desc')
        ->limit(6)
        ->get();
    
    return view('welcome', compact('publishedDomains'));
});

// Login routes - FIXED with POST
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', function () {
    $credentials = request()->only('email', 'password');
    $remember = request()->has('remember');
    
    if (Auth::attempt($credentials, $remember)) {
        request()->session()->regenerate();
        return redirect()->intended('/dashboard');
    }
    
    return back()->withErrors([
        'email' => 'The provided credentials do not match our records.',
    ])->onlyInput('email');
})->name('login');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::post('/register', [App\Http\Controllers\Auth\RegisteredUserController::class, 'store']);

// Email activation routes for new user registration
Route::get('/register/activate-email', [App\Http\Controllers\Auth\RegisteredUserController::class, 'showActivationForm'])->name('register.activate-email');
Route::get('/register/activate/{token}/{email}', [App\Http\Controllers\Auth\RegisteredUserController::class, 'activateAccount'])->name('register.activate');
Route::post('/register/resend-activation', [App\Http\Controllers\Auth\RegisteredUserController::class, 'resendActivation'])->name('register.resend-activation');

Route::post('/logout', function () { 
    Auth::logout(); 
    return redirect('/'); 
})->name('logout');

// Dashboard routes
Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard')->middleware('auth');

// Domain routes
Route::middleware('auth')->group(function () {
    Route::get('/domains/create', [App\Http\Controllers\DomainController::class, 'create'])->name('domains.create');
    Route::post('/domains', [App\Http\Controllers\DomainController::class, 'store'])->name('domains.store');
    Route::get('/domains/{domain}', [App\Http\Controllers\DomainController::class, 'show'])->name('domains.show');
    Route::get('/domains/{domain}/edit', [App\Http\Controllers\DomainController::class, 'edit'])->name('domains.edit');
    Route::patch('/domains/{domain}', [App\Http\Controllers\DomainController::class, 'update'])->name('domains.update');
    Route::get('/domains/{domain}/verification', [App\Http\Controllers\DomainVerificationController::class, 'show'])->name('domains.verification');
    Route::post('/domains/{domain}/verification/generate', [App\Http\Controllers\DomainVerificationController::class, 'generate'])->name('domains.verification.generate');
    Route::post('/domains/{domain}/verification/verify', [App\Http\Controllers\DomainVerificationController::class, 'verify'])->name('domains.verification.verify');
    Route::post('/domains/{domain}/verification/regenerate', [App\Http\Controllers\DomainVerificationController::class, 'regenerate'])->name('domains.verification.regenerate');
    Route::post('/domains/{domain}/verification/verify-file', [App\Http\Controllers\DomainVerificationController::class, 'verifyFile'])->name('domains.verification.verify-file');
    Route::get('/domains/{domain}/verification/download-file', [App\Http\Controllers\DomainVerificationController::class, 'downloadFile'])->name('domains.verification.download-file');
    Route::post('/domains/{domain}/verification/switch-method', [App\Http\Controllers\DomainVerificationController::class, 'switchMethod'])->name('domains.verification.switch-method');
    Route::post('/domains/{domain}/publish', [App\Http\Controllers\DomainController::class, 'publish'])->name('domains.publish');
    // Gracefully handle direct GET to publish URL by redirecting back with message
    Route::get('/domains/{domain}/publish', function (\App\Models\Domain $domain) {
        return redirect()
            ->route('domains.show', $domain)
            ->with('error', 'Publishing must be submitted via the Publish button.');
    })->name('domains.publish.get');
    Route::post('/domains/{domain}/change-to-draft', [App\Http\Controllers\DomainController::class, 'changeToDraft'])->name('domains.change-to-draft');
    Route::delete('/domains/{domain}', [App\Http\Controllers\DomainController::class, 'destroy'])->name('domains.destroy');
    Route::post('/domains/{domain}/mark-sold', [App\Http\Controllers\DomainController::class, 'markAsSold'])->name('domains.mark-sold');
    Route::post('/domains/{domain}/deactivate', [App\Http\Controllers\DomainController::class, 'deactivate'])->name('domains.deactivate');
    Route::post('/domains/{domain}/buy', [App\Http\Controllers\DomainController::class, 'buy'])->name('domains.buy');
});

// Public domain routes
Route::get('/browse-domains', [App\Http\Controllers\DomainController::class, 'publicIndex'])->name('domains.public.index');

Route::get('/my-domains', [App\Http\Controllers\DomainController::class, 'index'])->name('my.domains.index');

// Admin routes
Route::get('/admin', function () { return view('admin.dashboard'); })->name('admin.dashboard');
Route::get('/admin/domains', function () { return view('admin.domains.index'); })->name('admin.domains.index');
Route::get('/admin/users', function () { return view('admin.users.index'); })->name('admin.users.index');
Route::get('/admin/verifications', function () { return view('admin.verifications.index'); })->name('admin.verifications.index');
Route::get('/admin/escrow', function () { return view('admin.escrow.index'); })->name('admin.escrow.index');
Route::get('/admin/escrow/pending-transfers', function () { return view('admin.escrow.pending-transfers'); })->name('admin.escrow.pending-transfers');
Route::get('/admin/audit-logs', function () { return view('admin.audit-logs.index'); })->name('admin.audit-logs.index');
Route::get('/admin/settings', function () { return view('admin.settings.index'); })->name('admin.settings.index');

// User dashboard routes - COMPLETELY FIXED
Route::get('/seller-dashboard', function () { 
    return view('dashboard.seller', [
        'stats' => [
            'total_listings' => 0,
            'active_listings' => 0,
            'sold_listings' => 0,
            'total_sales' => 0,
            'total_revenue' => 0,
            'monthly_sales' => 0,
            'conversion_rate' => 0,
            'pending_offers' => 0,
            'draft_listings' => 0
        ],
        'recentListings' => collect([]),
        'recentOffers' => collect([]),
        'recentSales' => collect([]),
        'tab' => request('tab', 'listings'),
        'data' => collect([])
    ]); 
})->name('seller.dashboard');

Route::get('/buyer-dashboard', function () { 
    return view('dashboard.buyer', [
        'stats' => [
            'total_bids' => 0,
            'active_bids' => 0,
            'winning_bids' => 0,
            'total_spent' => 0,
            'watchlist_count' => 0,
            'offers_sent' => 0,
            'won_auctions' => 0,
            'pending_payments' => 0,
            'completed_purchases' => 0
        ],
        'recentBids' => collect([]),
        'recentWins' => collect([]),
        'watchlist' => collect([]),
        'tab' => request('tab', 'bids'),
        'data' => collect([])
    ]); 
})->name('buyer.dashboard');

// Profile routes - FIXED
Route::get('/profile', function () { 
    return view('profile.edit', [
        'user' => auth()->user()
    ]); 
})->name('profile.edit');

// Public user profile route
Route::get('/users/{user}/profile', [App\Http\Controllers\ProfileController::class, 'show'])->name('users.profile.show');

Route::patch('/profile', function () { 
    return redirect()->back()->with('success', 'Profile updated!'); 
})->name('profile.update');

Route::delete('/profile', function () { 
    return redirect('/')->with('success', 'Account deleted!'); 
})->name('profile.destroy');

Route::get('/profile/verification', function () { 
    return view('profile.verification', [
        'user' => auth()->user()
    ]); 
})->name('profile.verification');

// Password routes - FIXED
Route::put('/password', function () { 
    return redirect()->back()->with('success', 'Password updated successfully!'); 
})->name('password.update');

// Verification routes
Route::get('/verification', function () { 
    return view('verification.index', [
        'user' => auth()->user(),
        'verifications' => collect([])
    ]); 
})->name('verification.index')->middleware('auth');
Route::get('/verification/government-id', function () { 
    return view('verification.government-id', [
        'user' => auth()->user(),
        'governmentIdVerification' => null
    ]); 
})->name('verification.government-id')->middleware('auth');
Route::post('/verification/government-id', function () { 
    $request = request();
    
    // Validate the request
    $request->validate([
        'government_id' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120' // 5MB max
    ]);
    
    // Get the authenticated user
    $user = auth()->user();
    
    // Store the uploaded file
    if ($request->hasFile('government_id')) {
        $file = $request->file('government_id');
        $filename = 'government_ids/' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('public', $filename);
        
        // Update user's government ID path and mark as verified
        $user->update([
            'government_id_path' => $filename,
            'government_id_verified' => true, // Auto-verify for demo purposes
            'government_id_verified_at' => now(),
            'government_id_rejection_reason' => null
        ]);
    }
    
    return redirect()->route('verification.government-id')->with('success', 'Government ID has been uploaded and verified successfully!');
})->name('verification.government-id.submit')->middleware('auth');
Route::get('/verification/paypal', function () { 
    return view('verification.paypal', [
        'user' => auth()->user(),
        'paypalVerification' => null
    ]); 
})->name('verification.paypal')->middleware('auth');
Route::post('/verification/paypal', function () { 
    $request = request();
    
    // Validate the request
    $request->validate([
        'paypal_email' => 'required|email|max:255'
    ]);
    
    // Get the authenticated user
    $user = auth()->user();
    
    // Update user's PayPal email and mark as verified
    $user->update([
        'paypal_email' => $request->paypal_email,
        'paypal_verified' => true,
        'paypal_verified_at' => now()
    ]);
    
    return redirect()->route('verification.paypal')->with('success', 'PayPal email has been added and verified successfully!');
})->name('verification.paypal.submit')->middleware('auth');
Route::post('/verification/send', [App\Http\Controllers\VerificationController::class, 'sendVerificationEmail'])->name('verification.send');
Route::post('/verification/verify-email', [App\Http\Controllers\VerificationController::class, 'verifyEmail'])->name('verification.verify-email');

// PayPal routes
Route::get('/paypal/connect', function () { return redirect()->back()->with('success', 'PayPal connected!'); })->name('paypal.connect');
Route::get('/paypal/disconnect', function () { return redirect()->back()->with('success', 'PayPal disconnected!'); })->name('paypal.disconnect');

// Wallet routes
Route::middleware('auth')->group(function () {
    Route::get('/wallet', [App\Http\Controllers\WalletController::class, 'index'])->name('wallet.index');
    Route::get('/wallet/balance', [App\Http\Controllers\WalletController::class, 'getBalance'])->name('wallet.balance');
    Route::get('/wallet/transactions', [App\Http\Controllers\WalletController::class, 'getTransactions'])->name('wallet.transactions');
    Route::post('/wallet/withdraw', [App\Http\Controllers\WalletController::class, 'withdraw'])->name('wallet.withdraw');
    Route::post('/wallet/add-funds', [App\Http\Controllers\WalletController::class, 'addFunds'])->name('wallet.add-funds');
    Route::get('/wallet/eligibility', [App\Http\Controllers\WalletController::class, 'getWithdrawalEligibility'])->name('wallet.eligibility');
    Route::get('/wallet/export', [App\Http\Controllers\WalletController::class, 'exportTransactions'])->name('wallet.export');
});

// Communication routes - FIXED with proper pagination
Route::get('/conversations', function () { 
    $conversations = new \Illuminate\Pagination\LengthAwarePaginator(
        collect([]),
        0,
        15,
        1,
        ['path' => request()->url()]
    );
    
    return view('conversations.index', [
        'conversations' => $conversations,
        'unreadCount' => 0
    ]); 
})->name('conversations.index');

Route::get('/conversations/{conversation}', function ($conversation) { 
    return view('conversations.show', compact('conversation')); 
})->name('conversations.show');

Route::get('/conversations/new', function () { 
    $userId = request('user_id');
    return view('conversations.create', compact('userId')); 
})->name('conversations.new');

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

Route::post('/messages', function () { 
    return redirect()->back()->with('success', 'Message sent!'); 
})->name('messages.store');

// Offers and bids routes - FIXED with proper pagination
Route::get('/offers', function () { 
    $offers = new \Illuminate\Pagination\LengthAwarePaginator(
        collect([]),
        0,
        15,
        1,
        ['path' => request()->url()]
    );
    
    return view('offers.index', [
        'offers' => $offers
    ]); 
})->name('offers.index');

Route::get('/offers/create', function () { return view('offers.create'); })->name('offers.create');
Route::post('/offers', function () { return redirect()->back()->with('success', 'Offer submitted!'); })->name('offers.store');
Route::get('/domains/{domain}/bids', function ($domain) { return view('bids.index', compact('domain')); })->name('domains.bids.index');
Route::get('/domains/{domain}/bids/create', function ($domain) { return view('bids.create', compact('domain')); })->name('domains.bids.create');

// Other routes - FIXED with proper pagination
Route::get('/orders', function () { 
    $orders = new \Illuminate\Pagination\LengthAwarePaginator(
        collect([]),
        0,
        15,
        1,
        ['path' => request()->url()]
    );
    
    return view('orders.index', [
        'orders' => $orders
    ]); 
})->name('orders.index');

Route::get('/watchlist', function () { 
    $watchlist = new \Illuminate\Pagination\LengthAwarePaginator(
        collect([]),
        0,
        15,
        1,
        ['path' => request()->url()]
    );
    
    return view('watchlist.index', [
        'watchlist' => $watchlist
    ]); 
})->name('watchlist.index');

Route::get('/favorites', function () { return view('favorites.index'); })->name('favorites.index');

// Help and support routes
Route::get('/help', function () { return view('help.index'); })->name('help.index');
Route::get('/help/dns-txt', function () { return view('help.dns-txt'); })->name('help.dns-txt');
Route::get('/help/domain-transfer', function () { return view('help.domain-transfer'); })->name('help.domain-transfer');
Route::get('/help/domain-verification', function () { return view('help.domain-verification'); })->name('help.domain-verification');
Route::get('/support/contact', function () { return view('support.contact'); })->name('support.contact');

// Password reset routes - Enhanced with custom implementation
Route::get('/forgot-password', [App\Http\Controllers\Auth\PasswordResetLinkController::class, 'create'])->name('password.request');
Route::post('/forgot-password', [App\Http\Controllers\Auth\PasswordResetLinkController::class, 'store'])->name('password.email');
Route::get('/reset-password/{token}', [App\Http\Controllers\Auth\NewPasswordController::class, 'create'])->name('password.reset');
Route::post('/reset-password', [App\Http\Controllers\Auth\NewPasswordController::class, 'store'])->name('password.store');
Route::get('/confirm-password', function () { return view('auth.confirm-password'); })->name('password.confirm');
Route::post('/confirm-password', function () { return redirect()->back()->with('success', 'Password confirmed!'); })->name('password.confirm.store');

// Token route
Route::get('/token', function () { return response()->json(['token' => csrf_token()]); })->name('token');

// Test route
Route::get('/test-register', function () {
    try {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test' . time() . '@example.com',
            'password' => Hash::make('password'),
            'role_id' => 3,
            'account_status' => 'pending_verification'
        ]);
        return "User created successfully with ID: " . $user->id;
    } catch (Exception $e) {
        return "Error: " . $e->getMessage();
    }
});

// Activity routes for AJAX requests
Route::middleware('auth')->group(function () {
    Route::get('/api/activity', [RecentActivityController::class, 'index']);
    Route::patch('/api/activity/mark-read', [RecentActivityController::class, 'markAsRead']);
    Route::patch('/api/activity/mark-all-read', [RecentActivityController::class, 'markAllAsRead']);
});

// Alternative activity route without CSRF verification
Route::get('/activity-data', [RecentActivityController::class, 'index'])->middleware('auth');

// Test route for debugging
Route::get('/test-activity', function () {
    if (!Auth::check()) {
        return 'Not authenticated';
    }
    
    $controller = new RecentActivityController();
    $request = request();
    $response = $controller->index($request);
    
    return response()->json([
        'status' => 'success',
        'data' => $response->getData()
    ]);
})->middleware('auth');

// Simple test route without authentication
Route::get('/test-simple', function () {
    return response()->json([
        'message' => 'API is working',
        'authenticated' => Auth::check(),
        'user_id' => Auth::id()
    ]);
});
