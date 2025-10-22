<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DomainApiController;
use App\Http\Controllers\Api\UserApiController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\VerificationController;
use App\Http\Controllers\Api\ChecklistController;
use App\Http\Controllers\RecentActivityController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public API routes
Route::prefix('v1')->group(function () {
    // Domain routes (public)
    Route::get('/domains', [DomainApiController::class, 'index']);
    Route::get('/domains/{domain}', [DomainApiController::class, 'show']);
    Route::get('/domains/categories', [DomainApiController::class, 'categories']);
    Route::get('/domains/extensions', [DomainApiController::class, 'extensions']);
    Route::get('/domains/featured', [DomainApiController::class, 'featured']);
    Route::get('/domains/auctions', [DomainApiController::class, 'auctions']);
    Route::get('/domains/search', [DomainApiController::class, 'search']);
    Route::get('/domains/stats', [DomainApiController::class, 'stats']);

    // User authentication routes
    Route::post('/auth/register', [UserApiController::class, 'register']);
    Route::post('/auth/login', [UserApiController::class, 'login']);
});

// Protected API routes
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    // User routes
    Route::post('/auth/logout', [UserApiController::class, 'logout']);
    Route::get('/user/profile', [UserApiController::class, 'profile']);
    Route::put('/user/profile', [UserApiController::class, 'updateProfile']);
    Route::get('/user/domains', [UserApiController::class, 'domains']);
    Route::get('/user/orders', [UserApiController::class, 'orders']);
    Route::get('/user/favorites', [UserApiController::class, 'favorites']);
    Route::get('/user/verification-status', [UserApiController::class, 'verificationStatus']);
    Route::get('/user/stats', [UserApiController::class, 'stats']);
    Route::post('/user/change-password', [UserApiController::class, 'changePassword']);

    // Domain management routes (authenticated users)
    Route::post('/domains', [DomainApiController::class, 'store']);
    Route::put('/domains/{domain}', [DomainApiController::class, 'update']);
    Route::delete('/domains/{domain}', [DomainApiController::class, 'destroy']);
    
    // Verification routes
    Route::get('/domains/{domain}/verification-status', [VerificationController::class, 'getStatus']);
    Route::post('/domains/{domain}/verification/retry', [VerificationController::class, 'retry']);
    Route::post('/domains/{domain}/verification', [VerificationController::class, 'create']);
    Route::post('/domains/{domain}/publish', [DomainApiController::class, 'publish']);
    
    // Checklist routes
    Route::get('/transactions/{transaction}/checklist', [ChecklistController::class, 'getChecklist']);
    Route::post('/transactions/{transaction}/checklist/mark', [ChecklistController::class, 'markItem']);
    Route::post('/transactions/{transaction}/evidence', [ChecklistController::class, 'uploadEvidence']);
    Route::get('/transactions/{transaction}/evidence/{item}', [ChecklistController::class, 'getEvidence']);
});

// Admin API routes
Route::prefix('v1/admin')->middleware(['auth:sanctum', 'admin'])->group(function () {
    // Admin-specific API endpoints can be added here
    Route::get('/stats', function () {
        return response()->json([
            'success' => true,
            'data' => [
                'total_users' => \App\Models\User::count(),
                'total_domains' => \App\Models\Domain::count(),
                'total_orders' => \App\Models\Order::count(),
            ]
        ]);
    });
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Messaging API routes
Route::middleware('auth:sanctum')->prefix('messages')->group(function () {
    Route::post('/', [MessageController::class, 'store']);
    Route::get('/conversations/{userId}', [MessageController::class, 'conversation']);
    Route::patch('/{message}/read', [MessageController::class, 'markAsRead']);
    Route::patch('/mark-all-read', [MessageController::class, 'markAllAsRead']);
    Route::get('/unread-count', [MessageController::class, 'unreadCount']);
    Route::get('/conversations', [MessageController::class, 'conversations']);
    Route::delete('/{message}', [MessageController::class, 'destroy']);
});

// Notification API routes
Route::middleware('auth:sanctum')->prefix('notifications')->group(function () {
    Route::get('/', [NotificationController::class, 'index']);
    Route::get('/recent', [NotificationController::class, 'recent']);
    Route::get('/unread-count', [NotificationController::class, 'unreadCount']);
    Route::patch('/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::patch('/mark-all-read', [NotificationController::class, 'markAllAsRead']);
    Route::delete('/{id}', [NotificationController::class, 'destroy']);
    Route::delete('/', [NotificationController::class, 'destroyAll']);
    Route::get('/statistics', [NotificationController::class, 'statistics']);
});

// Recent Activity API routes
Route::middleware('auth:web')->prefix('activity')->group(function () {
    Route::get('/', [RecentActivityController::class, 'index']);
    Route::patch('/mark-read', [RecentActivityController::class, 'markAsRead']);
    Route::patch('/mark-all-read', [RecentActivityController::class, 'markAllAsRead']);
});
