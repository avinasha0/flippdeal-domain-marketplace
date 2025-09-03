<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DomainController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\BidController;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    try {
        $publishedDomains = \App\Models\Domain::where('status', 'active')
            ->whereHas('user') // Only get domains that have a valid user
            ->with('user')
            ->latest()
            ->take(6)
            ->get();
    } catch (\Exception $e) {
        $publishedDomains = collect([]);
    }
    return view('welcome', compact('publishedDomains'));
})->name('welcome');

Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

// Public domain routes
Route::get('/domains', [DomainController::class, 'publicIndex'])->name('domains.public.index');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Verification routes
    Route::get('/verification', [\App\Http\Controllers\VerificationController::class, 'index'])->name('verification.index');
    Route::get('/verification/paypal', [\App\Http\Controllers\VerificationController::class, 'showPayPalForm'])->name('verification.paypal');
    Route::post('/verification/paypal', [\App\Http\Controllers\VerificationController::class, 'submitPayPalVerification'])->name('verification.paypal.submit');
    Route::get('/verification/government-id', [\App\Http\Controllers\VerificationController::class, 'showGovernmentIdForm'])->name('verification.government-id');
    Route::post('/verification/government-id', [\App\Http\Controllers\VerificationController::class, 'submitGovernmentIdVerification'])->name('verification.government-id.submit');
    Route::get('/verification/status', [\App\Http\Controllers\VerificationController::class, 'getStatus'])->name('verification.status');
    
    // Protected domain routes (user's own domains)
    Route::get('/my-domains', [DomainController::class, 'index'])->name('my.domains.index');
    Route::get('/domains/create', [DomainController::class, 'create'])->name('domains.create');
    Route::post('/domains', [DomainController::class, 'store'])->name('domains.store');
    Route::get('/api/whois/{domain}', [DomainController::class, 'getWhoisData'])->name('api.whois');
    Route::get('/api/test-whois/{domain}', [DomainController::class, 'testWhoisData'])->name('api.test-whois');
    Route::get('/domains/{domain}/edit', [DomainController::class, 'edit'])->name('domains.edit');
    Route::patch('/domains/{domain}', [DomainController::class, 'update'])->name('domains.update');
    Route::delete('/domains/{domain}', [DomainController::class, 'destroy'])->name('domains.destroy');
    Route::patch('/domains/{domain}/publish', [DomainController::class, 'publish'])->name('domains.publish');
    Route::patch('/domains/{domain}/deactivate', [DomainController::class, 'deactivate'])->name('domains.deactivate');
    Route::patch('/domains/{domain}/mark-sold', [DomainController::class, 'markAsSold'])->name('domains.mark-sold');
    
    // Order routes
    Route::resource('orders', OrderController::class);
    Route::get('/orders/{order}/payment', [OrderController::class, 'payment'])->name('orders.payment');
    Route::post('/orders/{order}/payment', [OrderController::class, 'processPayment'])->name('orders.process-payment');
    Route::patch('/orders/{order}/complete', [OrderController::class, 'complete'])->name('orders.complete');
    Route::patch('/orders/{order}/dispute', [OrderController::class, 'dispute'])->name('orders.dispute');
    
    // Offer routes
    Route::resource('offers', OfferController::class);
    Route::patch('/offers/{offer}/accept', [OfferController::class, 'accept'])->name('offers.accept');
    Route::patch('/offers/{offer}/reject', [OfferController::class, 'reject'])->name('offers.reject');
    Route::patch('/offers/{offer}/convert', [OfferController::class, 'convertToOrder'])->name('offers.convert');
    
    // Message routes
    Route::resource('messages', MessageController::class);
    Route::get('/messages/conversation/{user}', [MessageController::class, 'conversation'])->name('messages.conversation');
    Route::patch('/messages/{message}/read', [MessageController::class, 'markAsRead'])->name('messages.read');
    
    // Favorite routes
    Route::resource('favorites', FavoriteController::class)->only(['index', 'store', 'destroy']);
    Route::patch('/favorites/{favorite}/toggle-notifications', [FavoriteController::class, 'toggleNotifications'])->name('favorites.toggle-notifications');
    
    // Buy domain route
    Route::post('/domains/{domain}/buy', [DomainController::class, 'buy'])->name('domains.buy');
    
    // Bidding routes
    Route::resource('bids', BidController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);
    Route::get('/domains/{domain}/bids', [BidController::class, 'index'])->name('domains.bids.index');
    Route::get('/domains/{domain}/bids/create', [BidController::class, 'create'])->name('domains.bids.create');
    Route::post('/domains/{domain}/bids', [BidController::class, 'store'])->name('domains.bids.store');
    Route::get('/domains/{domain}/bids/history', [BidController::class, 'history'])->name('domains.bids.history');
    Route::post('/domains/{domain}/auto-bid', [BidController::class, 'autoBid'])->name('domains.auto-bid');
    
    // Search and filter routes
    Route::get('/search', [DomainController::class, 'search'])->name('domains.search');
    Route::get('/categories/{category}', [DomainController::class, 'byCategory'])->name('domains.by-category');
    Route::get('/extensions/{extension}', [DomainController::class, 'byExtension'])->name('domains.by-extension');
    
    // Domain verification routes
    Route::get('/domains/{domain}/verification', [\App\Http\Controllers\DomainVerificationController::class, 'show'])->name('domains.verification');
    Route::post('/domains/{domain}/verification/generate', [\App\Http\Controllers\DomainVerificationController::class, 'generate'])->name('domains.verification.generate');
    Route::post('/domains/{domain}/verification/verify', [\App\Http\Controllers\DomainVerificationController::class, 'verify'])->name('domains.verification.verify');
    Route::post('/domains/{domain}/verification/regenerate', [\App\Http\Controllers\DomainVerificationController::class, 'regenerate'])->name('domains.verification.regenerate');
    Route::get('/domains/{domain}/verification/status', [\App\Http\Controllers\DomainVerificationController::class, 'status'])->name('domains.verification.status');

});

// Public domain show route (must come after /domains/create to avoid route conflict)
Route::get('/domains/{domain}', [DomainController::class, 'show'])->name('domains.show');

// Admin routes
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [\App\Http\Controllers\AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/stats', [\App\Http\Controllers\AdminController::class, 'getStats'])->name('admin.stats');
    
    // User management
    Route::get('/users', [\App\Http\Controllers\AdminController::class, 'users'])->name('admin.users.index');
    Route::get('/users/{user}', [\App\Http\Controllers\AdminController::class, 'showUser'])->name('admin.users.show');
    Route::post('/users/{user}/approve', [\App\Http\Controllers\AdminController::class, 'approveUser'])->name('admin.users.approve');
    Route::post('/users/{user}/suspend', [\App\Http\Controllers\AdminController::class, 'suspendUser'])->name('admin.users.suspend');
    Route::post('/users/{user}/activate', [\App\Http\Controllers\AdminController::class, 'activateUser'])->name('admin.users.activate');
    
    // Domain management
    Route::get('/domains', [\App\Http\Controllers\AdminController::class, 'domains'])->name('admin.domains.index');
    Route::get('/domains/{domain}', [\App\Http\Controllers\AdminController::class, 'showDomain'])->name('admin.domains.show');
    Route::post('/domains/{domain}/approve', [\App\Http\Controllers\AdminController::class, 'approveDomain'])->name('admin.domains.approve');
    Route::post('/domains/{domain}/reject', [\App\Http\Controllers\AdminController::class, 'rejectDomain'])->name('admin.domains.reject');
    
    // Verification management
    Route::get('/verifications', [\App\Http\Controllers\AdminController::class, 'verifications'])->name('admin.verifications.index');
    Route::post('/verifications/{verification}/approve', [\App\Http\Controllers\AdminController::class, 'approveVerification'])->name('admin.verifications.approve');
    Route::post('/verifications/{verification}/reject', [\App\Http\Controllers\AdminController::class, 'rejectVerification'])->name('admin.verifications.reject');
    
    // Settings
    Route::get('/settings', [\App\Http\Controllers\AdminController::class, 'settings'])->name('admin.settings.index');
    Route::post('/settings', [\App\Http\Controllers\AdminController::class, 'updateSettings'])->name('admin.settings.update');
    
    // Audit logs
    Route::get('/audit-logs', [\App\Http\Controllers\AdminController::class, 'auditLogs'])->name('admin.audit-logs.index');
    
    // Verification management
    Route::get('/verifications/{verification}/download', [\App\Http\Controllers\VerificationController::class, 'downloadGovernmentId'])->name('admin.verifications.download');
});

require __DIR__.'/auth.php';
