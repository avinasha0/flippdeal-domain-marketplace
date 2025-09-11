<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Domain;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DomainApiController extends Controller
{
    /**
     * Get all domains with pagination and filters.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Domain::with(['user', 'bids', 'offers'])
            ->active()
            ->verified();

        // Apply filters
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        if ($request->has('extension')) {
            $query->where('domain_extension', $request->extension);
        }

        if ($request->has('min_price')) {
            $query->where('asking_price', '>=', $request->min_price);
        }

        if ($request->has('max_price')) {
            $query->where('asking_price', '<=', $request->max_price);
        }

        if ($request->has('search')) {
            $query->search($request->search);
        }

        if ($request->has('featured')) {
            $query->featured();
        }

        if ($request->has('auction')) {
            $query->activeAuctions();
        }

        // Apply sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        $allowedSorts = ['created_at', 'asking_price', 'view_count', 'auction_end'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder);
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
     * Get a specific domain.
     */
    public function show(Domain $domain): JsonResponse
    {
        $domain->load(['user', 'bids.bidder', 'offers.buyer', 'favorites']);
        
        // Increment view count
        $domain->incrementViewCount();

        return response()->json([
            'success' => true,
            'data' => $domain
        ]);
    }

    /**
     * Get domain categories.
     */
    public function categories(): JsonResponse
    {
        $categories = Category::active()->ordered()->get();

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    /**
     * Get popular domain extensions.
     */
    public function extensions(): JsonResponse
    {
        $extensions = Domain::active()
            ->selectRaw('domain_extension, COUNT(*) as count')
            ->groupBy('domain_extension')
            ->orderBy('count', 'desc')
            ->limit(20)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $extensions
        ]);
    }

    /**
     * Get featured domains.
     */
    public function featured(): JsonResponse
    {
        $domains = Domain::with(['user'])
            ->featured()
            ->active()
            ->verified()
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $domains
        ]);
    }

    /**
     * Get active auctions.
     */
    public function auctions(): JsonResponse
    {
        $domains = Domain::with(['user', 'bids.bidder'])
            ->activeAuctions()
            ->verified()
            ->orderBy('auction_end', 'asc')
            ->limit(20)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $domains
        ]);
    }

    /**
     * Search domains.
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'q' => 'required|string|min:2|max:100'
        ]);

        $query = Domain::with(['user'])
            ->active()
            ->verified()
            ->search($request->q);

        // Apply additional filters
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        if ($request->has('extension')) {
            $query->where('domain_extension', $request->extension);
        }

        if ($request->has('min_price')) {
            $query->where('asking_price', '>=', $request->min_price);
        }

        if ($request->has('max_price')) {
            $query->where('asking_price', '<=', $request->max_price);
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
     * Get domain statistics.
     */
    public function stats(): JsonResponse
    {
        $stats = [
            'total_domains' => Domain::count(),
            'active_domains' => Domain::active()->count(),
            'verified_domains' => Domain::verified()->count(),
            'featured_domains' => Domain::featured()->count(),
            'active_auctions' => Domain::activeAuctions()->count(),
            'total_categories' => Category::active()->count(),
            'average_price' => Domain::active()->avg('asking_price'),
            'highest_price' => Domain::active()->max('asking_price'),
            'lowest_price' => Domain::active()->min('asking_price')
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}