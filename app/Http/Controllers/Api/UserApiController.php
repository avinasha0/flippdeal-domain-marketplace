<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Domain;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserApiController extends Controller
{
    /**
     * Register a new user.
     */
    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => 3, // Regular user role
            'account_status' => 'pending_verification'
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'User registered successfully',
            'data' => [
                'user' => $user,
                'token' => $token
            ]
        ], 201);
    }

    /**
     * Login user.
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        // Update last login
        $user->updateLastLogin();

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => $user,
                'token' => $token
            ]
        ]);
    }

    /**
     * Logout user.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout successful'
        ]);
    }

    /**
     * Get authenticated user profile.
     */
    public function profile(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->load(['role', 'domains', 'verifications']);

        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    /**
     * Update user profile.
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'phone' => 'sometimes|nullable|string|max:20',
            'bio' => 'sometimes|nullable|string|max:1000',
            'location' => 'sometimes|nullable|string|max:255',
            'company_name' => 'sometimes|nullable|string|max:255',
            'website' => 'sometimes|nullable|url|max:255',
        ]);

        $user->update($request->only([
            'name', 'phone', 'bio', 'location', 'company_name', 'website'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => $user
        ]);
    }

    /**
     * Get user's domains.
     */
    public function domains(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $query = $user->domains()->with(['bids', 'offers', 'orders']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $domains = $query->paginate($request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => $domains->items(),
            'pagination' => [
                'current_page' => $domains->currentPage(),
                'last_page' => $domains->lastPage(),
                'per_page' => $domains->perPage(),
                'total' => $domains->total(),
                'has_more' => $domains->hasMorePages()
            ]
        ]);
    }

    /**
     * Get user's orders.
     */
    public function orders(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $query = $user->orders()->with(['domain', 'seller', 'buyer']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->paginate($request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => $orders->items(),
            'pagination' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
                'has_more' => $orders->hasMorePages()
            ]
        ]);
    }

    /**
     * Get user's favorites.
     */
    public function favorites(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $favorites = $user->favorites()
            ->with(['domain.user'])
            ->paginate($request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => $favorites->items(),
            'pagination' => [
                'current_page' => $favorites->currentPage(),
                'last_page' => $favorites->lastPage(),
                'per_page' => $favorites->perPage(),
                'total' => $favorites->total(),
                'has_more' => $favorites->hasMorePages()
            ]
        ]);
    }

    /**
     * Get user's verification status.
     */
    public function verificationStatus(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $status = [
            'paypal_verified' => $user->isPayPalVerified(),
            'government_id_verified' => $user->isGovernmentIdVerified(),
            'fully_verified' => $user->isFullyVerified(),
            'account_status' => $user->account_status,
            'pending_verifications' => $user->getPendingVerifications()
        ];

        return response()->json([
            'success' => true,
            'data' => $status
        ]);
    }

    /**
     * Get user statistics.
     */
    public function stats(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $stats = [
            'total_domains' => $user->domains()->count(),
            'active_domains' => $user->domains()->active()->count(),
            'sold_domains' => $user->domains()->sold()->count(),
            'total_sales' => $user->total_sales,
            'total_purchases' => $user->total_purchases,
            'total_orders' => $user->orders()->count(),
            'completed_orders' => $user->orders()->where('status', 'completed')->count(),
            'total_favorites' => $user->favorites()->count(),
            'unread_messages' => $user->unread_message_count
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Change password.
     */
    public function changePassword(Request $request): JsonResponse
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['The current password is incorrect.'],
            ]);
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully'
        ]);
    }
}