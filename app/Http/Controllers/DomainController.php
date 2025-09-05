<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDomainRequest;
use App\Models\Domain;
use App\Models\Category;
use App\Models\AuditLog;
use App\Services\DomainVerificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;

class DomainController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $domains = Auth::user()->domains()->latest()->paginate(10);
        return view('domains.index', compact('domains'));
    }

    /**
     * Display a public listing of published domains.
     */
    public function publicIndex()
    {
        $domains = Domain::where('status', 'active')
            ->whereHas('user') // Only get domains that have a valid user
            ->with('user')
            ->latest()
            ->paginate(12);
        return view('domains.public-index', compact('domains'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Check if user can create domains
        if (!Gate::allows('create', Domain::class)) {
            return redirect()->route('verification.index')->with('warning', 
                'Please complete your profile verification to list domains.');
        }

        // Get categories - handle case where no categories exist
        try {
            $categories = Category::active()->orderBy('name')->get();
        } catch (\Exception $e) {
            // If categories table doesn't exist or has issues, use empty collection
            $categories = collect();
        }
        
        return view('domains.create', compact('categories'));
    }

    /**
     * Get WHOIS data for a domain.
     */
    public function getWhoisData($domain)
    {
        try {
            // Clean the domain name
            $domain = strtolower(trim($domain));
            
            // Validate domain format
            if (!preg_match('/^[a-zA-Z0-9][a-zA-Z0-9-]{1,61}[a-zA-Z0-9]\.[a-zA-Z]{2,}$/', $domain)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid domain format. Please enter a valid domain name.'
                ], 400);
            }
            
            // Try multiple WHOIS APIs
            $whoisData = $this->fetchWhoisData($domain);
            
            if ($whoisData) {
                return response()->json([
                    'success' => true,
                    'data' => $whoisData
                ]);
            } else {
                // Provide helpful message instead of generic error
                return response()->json([
                    'success' => false,
                    'message' => 'WHOIS data not available for this domain. This could be due to domain privacy protection or API limitations. Please enter the dates manually.'
                ], 404);
            }
            
        } catch (\Exception $e) {
            \Log::error('WHOIS API Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Unable to fetch domain information at this time. Please enter the dates manually.'
            ], 500);
        }
    }
    
    /**
     * Test WHOIS data for debugging.
     */
    public function testWhoisData($domain)
    {
        try {
            $domain = strtolower(trim($domain));
            
            // Test each API individually
            $apis = [
                'https://api.whoisjson.com/v1/' . $domain,
                'https://api.whois.vu/?q=' . $domain,
                'https://whoisjson.com/api/v1/whois?domain=' . $domain
            ];
            
            $results = [];
            
            foreach ($apis as $index => $apiUrl) {
                $results["api_" . ($index + 1)] = [
                    'url' => $apiUrl,
                    'response' => $this->makeHttpRequest($apiUrl)
                ];
            }
            
            return response()->json([
                'domain' => $domain,
                'test_results' => $results,
                'domain_resolves' => gethostbyname($domain) !== $domain
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Fetch WHOIS data from various APIs.
     */
    private function fetchWhoisData($domain)
    {
        // Try different approaches to get WHOIS data
        
        // Method 1: Try free WHOIS APIs
        $apis = [
            'https://api.whoisjson.com/v1/' . $domain,
            'https://api.whois.vu/?q=' . $domain,
            'https://whoisjson.com/api/v1/whois?domain=' . $domain,
            'https://whoisjson.com/api/v1/whois?domain=' . $domain . '&format=json'
        ];
        
        foreach ($apis as $apiUrl) {
            try {
                $response = $this->makeHttpRequest($apiUrl);
                
                if ($response) {
                    $parsedData = $this->parseWhoisResponse($response);
                    if ($parsedData) {
                        return $parsedData;
                    }
                }
            } catch (\Exception $e) {
                // Continue to next API
                continue;
            }
        }
        
        // Method 2: Try to get basic domain info using alternative approach
        return $this->getBasicDomainInfo($domain);
    }
    
    /**
     * Make HTTP request to WHOIS API.
     */
    private function makeHttpRequest($url)
    {
        // Use cURL for better error handling
        if (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Accept: application/json',
                'Content-Type: application/json'
            ]);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);
            
            if ($response === false || $httpCode !== 200) {
                return null;
            }
            
            return json_decode($response, true);
        }
        
        // Fallback to file_get_contents
        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => [
                    'Accept: application/json',
                    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
                ],
                'timeout' => 10
            ]
        ]);
        
        $response = @file_get_contents($url, false, $context);
        
        if ($response === false) {
            return null;
        }
        
        return json_decode($response, true);
    }
    
    /**
     * Get basic domain information as fallback.
     */
    private function getBasicDomainInfo($domain)
    {
        // For demonstration purposes, we'll return some sample data
        // In a real implementation, you might use a different approach
        
        // Check if domain is valid by trying to resolve it
        if (gethostbyname($domain) === $domain) {
            // Domain doesn't resolve, might not exist
            return null;
        }
        
        // For existing domains, we can't get exact dates without WHOIS
        // But we can provide some helpful information
        $currentYear = date('Y');
        
        // Return null to let user enter manually
        // In a production environment, you might want to:
        // 1. Use a paid WHOIS service
        // 2. Cache WHOIS data
        // 3. Use alternative data sources
        
        return null;
    }
    
    /**
     * Parse WHOIS API response.
     */
    private function parseWhoisResponse($data)
    {
        if (!$data || !is_array($data)) {
            return null;
        }
        
        $registrationDate = null;
        $expiryDate = null;
        
        // Common field names for registration date
        $regFields = ['creation_date', 'created_date', 'registered_date', 'creationDate', 'createdDate', 'registeredDate'];
        // Common field names for expiry date
        $expFields = ['expiry_date', 'expiration_date', 'expires_date', 'expiryDate', 'expirationDate', 'expiresDate'];
        
        // Look for registration date
        foreach ($regFields as $field) {
            if (isset($data[$field]) && $data[$field]) {
                $registrationDate = $this->formatDateForInput($data[$field]);
                break;
            }
        }
        
        // Look for expiry date
        foreach ($expFields as $field) {
            if (isset($data[$field]) && $data[$field]) {
                $expiryDate = $this->formatDateForInput($data[$field]);
                break;
            }
        }
        
        // Return data if we found at least one date
        if ($registrationDate || $expiryDate) {
            return [
                'registration_date' => $registrationDate,
                'expiry_date' => $expiryDate
            ];
        }
        
        return null;
    }
    
    /**
     * Format date for HTML input.
     */
    private function formatDateForInput($dateString)
    {
        try {
            if (is_string($dateString)) {
                // If it's already in YYYY-MM-DD format, return as is
                if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateString)) {
                    return $dateString;
                }
                
                $date = new \DateTime($dateString);
            } elseif ($dateString instanceof \DateTime) {
                $date = $dateString;
            } else {
                return null;
            }
            
            return $date->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDomainRequest $request)
    {
        // Check if user can create domains
        if (!Gate::allows('create', Domain::class)) {
            return redirect()->route('verification.index')->with('warning', 
                'Please complete your profile verification to list domains.');
        }

        $action = $request->input('action', 'draft');
        $data = $request->validated();
        $data['user_id'] = Auth::id();
        
        // Handle different actions
        if ($action === 'publish') {
            // Check if user is fully verified to publish
            if (!Auth::user()->isFullyVerified()) {
                return back()->withErrors(['error' => 'You must complete your account verification before listing domains for sale.']);
            }
            
            // Set status to draft first - domain must be verified before becoming active
            $data['status'] = 'draft';
            $data['domain_verified'] = false; // Domain verification required
            
            $successMessage = 'Domain created successfully! Please verify domain ownership to publish your listing.';
            $auditEvent = 'domain_created_draft';
        } else {
            // Save as draft
            $data['status'] = 'draft';
            $data['domain_verified'] = false;
            
            $successMessage = 'Domain saved as draft successfully! Complete your account verification to list it for sale.';
            $auditEvent = 'domain_created_draft';
        }

        // Process sale options
        $data['enable_buy_now'] = $request->has('enable_buy_now');
        $data['enable_bidding'] = $request->has('enable_bidding');
        $data['enable_offers'] = $request->has('enable_offers');
        $data['auto_accept_offers'] = $request->has('auto_accept_offers');

        // Set auction status if bidding is enabled
        if ($data['enable_bidding']) {
            // All domains start as draft - auction will be scheduled after verification
            $data['auction_status'] = 'draft';
        }

        DB::beginTransaction();
        
        try {
            $domain = Domain::create($data);

            // Log domain creation
            try {
                AuditLog::create([
                    'user_id' => Auth::id(),
                    'event' => $auditEvent,
                    'auditable_type' => Domain::class,
                    'auditable_id' => $domain->id,
                    'new_values' => [
                        'domain_name' => $domain->domain_name,
                        'domain_extension' => $domain->domain_extension,
                        'status' => $data['status'],
                        'enable_buy_now' => $data['enable_buy_now'],
                        'enable_bidding' => $data['enable_bidding'],
                        'enable_offers' => $data['enable_offers']
                    ],
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
                \Log::info('AuditLog created successfully');
            } catch (\Exception $e) {
                \Log::error('AuditLog creation failed: ' . $e->getMessage());
                // Don't fail the entire transaction for audit log issues
            }

            DB::commit();

            if ($action === 'publish') {
                // Send notification about domain verification requirement
                Auth::user()->notify(new \App\Notifications\DomainVerificationRequired($domain));
                
                return redirect()->route('domains.verification', $domain)
                    ->with('success', $successMessage);
            } else {
                return redirect()->route('domains.show', $domain)
                    ->with('success', $successMessage);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to create domain listing. Please try again.']);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Domain $domain)
    {
        // For published domains, require authentication to view details
        if ($domain->status === 'active') {
            if (!auth()->check()) {
                return redirect()->route('login')->with('message', 'Please login to view domain details.');
            }
            return view('domains.show', compact('domain'));
        }
        
        // For non-published domains, only allow access to the owner
        if (!auth()->check() || $domain->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }
        
        return view('domains.show', compact('domain'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Domain $domain)
    {
        if ($domain->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }
        return view('domains.edit', compact('domain'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreDomainRequest $request, Domain $domain)
    {
        if ($domain->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $data = $request->validated();
        
        // Handle BIN (Buy It Now) logic
        if (!isset($data['enable_bin']) || !$data['enable_bin']) {
            $data['bin_price'] = null;
        }
        unset($data['enable_bin']); // Remove the checkbox value
        
        // Handle offer acceptance logic
        if (!isset($data['accepts_offers']) || !$data['accepts_offers']) {
            $data['minimum_offer'] = null;
            $data['accepts_offers'] = false;
        } else {
            $data['accepts_offers'] = true;
            // If accepts_offers is true but minimum_offer is empty string, set it to null
            // This allows users to accept offers without setting a minimum
            if (isset($data['minimum_offer']) && $data['minimum_offer'] === '') {
                $data['minimum_offer'] = null;
            }
        }

        // Handle bidding logic
        if (!isset($data['enable_bidding']) || !$data['enable_bidding']) {
            $data['enable_bidding'] = false;
            $data['starting_bid'] = null;
            $data['current_bid'] = null;
            $data['auction_start'] = null;
            $data['auction_end'] = null;
            $data['auction_status'] = 'draft';
            $data['reserve_price'] = null;
            $data['reserve_met'] = false;
            $data['minimum_bid_increment'] = 10;
            $data['auto_extend'] = false;
            $data['auto_extend_minutes'] = 5;
        } else {
            $data['enable_bidding'] = true;
            
            // Set auction status based on dates
            if (isset($data['auction_start']) && isset($data['auction_end'])) {
                $now = now();
                $start = Carbon::parse($data['auction_start']);
                $end = Carbon::parse($data['auction_end']);
                
                if ($start > $now) {
                    $data['auction_status'] = 'scheduled';
                } elseif ($end > $now) {
                    $data['auction_status'] = 'active';
                } else {
                    $data['auction_status'] = 'ended';
                }
            } else {
                $data['auction_status'] = 'draft';
            }
            
            // Set default values for bidding fields
            if (!isset($data['minimum_bid_increment']) || empty($data['minimum_bid_increment'])) {
                $data['minimum_bid_increment'] = 10;
            }
            if (!isset($data['auto_extend_minutes']) || empty($data['auto_extend_minutes'])) {
                $data['auto_extend_minutes'] = 5;
            }
        }
        
        $domain->update($data);

        return redirect()->route('domains.show', $domain)
            ->with('success', 'Domain updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Domain $domain)
    {
        // 1. Check ownership
        if ($domain->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }
        
        // 2. Check if domain can be deleted (only draft and inactive domains)
        if (!in_array($domain->status, ['draft', 'inactive'])) {
            abort(403, 'Only draft and inactive domains can be deleted. Active or sold domains cannot be deleted.');
        }
        
        // 3. Check if domain has any bids
        if ($domain->bids()->count() > 0) {
            abort(403, 'Cannot delete domain with existing bids. Please contact support if you need assistance.');
        }
        
        // 4. Check if domain has any offers
        if ($domain->offers()->count() > 0) {
            abort(403, 'Cannot delete domain with existing offers. Please contact support if you need assistance.');
        }
        
        // 5. Check if domain has any conversations
        if ($domain->conversations()->count() > 0) {
            abort(403, 'Cannot delete domain with existing conversations. Please contact support if you need assistance.');
        }
        
        // 6. Check if domain is on any watchlists
        if ($domain->watchlist()->count() > 0) {
            abort(403, 'Cannot delete domain that is being watched by users. Please contact support if you need assistance.');
        }
        
        // 7. Log the deletion for audit purposes
        \Log::info('Domain deleted', [
            'domain_id' => $domain->id,
            'domain_name' => $domain->full_domain,
            'user_id' => auth()->id(),
            'deleted_at' => now()
        ]);
        
        // 8. Delete the domain
        $domain->delete();

        return redirect()->route('my.domains.index')
            ->with('success', 'Domain removed successfully!');
    }

    /**
     * Publish a draft domain to active status.
     */
    public function publish(Domain $domain)
    {
        // Debug: Log the publish attempt
        \Log::info('Publish attempt - NEW CODE:', [
            'domain_id' => $domain->id,
            'domain_name' => $domain->domain_name,
            'domain_status' => $domain->status,
            'domain_user_id' => $domain->user_id,
            'current_user_id' => Auth::id()
        ]);
        
        // Manual authorization check - bypass gate completely
        if (Auth::id() !== $domain->user_id) {
            \Log::error('Publish failed: User does not own domain', [
                'user_id' => Auth::id(),
                'domain_user_id' => $domain->user_id
            ]);
            abort(403, 'You can only publish your own domains.');
        }
        
        if ($domain->status !== 'draft') {
            \Log::error('Publish failed: Domain is not draft', ['status' => $domain->status]);
            return back()->with('error', 'Only draft domains can be published.');
        }

        \Log::info('Domain verification check:', [
            'domain_verified' => $domain->domain_verified,
            'domain_id' => $domain->id
        ]);

        // Check if domain is verified before publishing
        if (!$domain->domain_verified) {
            \Log::error('Publish failed: Domain not verified', [
                'domain_verified' => $domain->domain_verified,
                'domain_id' => $domain->id
            ]);
            return back()->with('error', 'Domain ownership must be verified before publishing. Please verify your domain first.');
        }

        \Log::info('Starting database transaction for domain publish');
        DB::beginTransaction();
        
        try {
            \Log::info('Updating domain status to active');
            $updateData = ['status' => 'active'];
            
            // If bidding is enabled, set auction status to scheduled
            if ($domain->enable_bidding) {
                $updateData['auction_status'] = 'scheduled';
            }
            
            $domain->update($updateData);
            \Log::info('Domain status updated successfully');

            // Log domain publication
            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'domain_published',
                'event' => 'domain_published',
                'auditable_type' => Domain::class,
                'auditable_id' => $domain->id,
                'new_values' => ['status' => 'active'],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            \Log::info('Committing database transaction');
            DB::commit();
            \Log::info('Database transaction committed successfully');

            return back()->with('success', 'Domain published successfully! It is now visible to buyers.');

        } catch (\Exception $e) {
            \Log::error('Publish failed with exception', [
                'error' => $e->getMessage(),
                'domain_id' => $domain->id
            ]);
            DB::rollBack();
            return back()->with('error', 'Failed to publish domain. Please try again.');
        }
    }

    /**
     * Mark a domain as sold.
     */
    public function markAsSold(Domain $domain)
    {
        if ($domain->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $domain->update(['status' => 'sold']);

        return back()->with('success', 'Domain marked as sold!');
    }

    /**
     * Deactivate a domain.
     */
    public function deactivate(Domain $domain)
    {
        if ($domain->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $domain->update(['status' => 'inactive']);

        return back()->with('success', 'Domain deactivated successfully!');
    }

    /**
     * Change domain status to draft (if no pending actions).
     */
    public function changeToDraft(Domain $domain)
    {
        // Debug logging
        \Log::info('Change to Draft Request', [
            'domain_id' => $domain->id,
            'domain_name' => $domain->full_domain,
            'current_status' => $domain->status,
            'user_id' => auth()->id(),
            'domain_user_id' => $domain->user_id
        ]);

        if ($domain->user_id !== auth()->id()) {
            \Log::error('Unauthorized change to draft attempt', [
                'domain_user_id' => $domain->user_id,
                'auth_user_id' => auth()->id()
            ]);
            abort(403, 'Unauthorized action.');
        }

        // Check if domain can be changed to draft
        if (!in_array($domain->status, ['active', 'inactive'])) {
            \Log::warning('Invalid status for draft change', ['status' => $domain->status]);
            return back()->with('error', 'Only active or inactive domains can be changed to draft.');
        }

        // Check for pending actions
        if ($domain->hasPendingActions()) {
            $pendingActions = $domain->getPendingActionsSummary();
            $actionsText = implode(', ', $pendingActions);
            \Log::warning('Pending actions prevent draft change', ['actions' => $pendingActions]);
            return back()->with('error', "Cannot change to draft. Domain has pending actions: {$actionsText}");
        }

        // Update status to draft
        $result = $domain->update(['status' => 'draft']);
        
        \Log::info('Status update result', [
            'result' => $result,
            'new_status' => $domain->fresh()->status
        ]);

        if ($result) {
            return back()->with('success', 'Domain status changed to draft successfully!');
        } else {
            return back()->with('error', 'Failed to change domain status to draft.');
        }
    }

    /**
     * Check if domain can be changed to draft.
     */
    public function canChangeToDraft(Domain $domain)
    {
        if ($domain->user_id !== auth()->id()) {
            return false;
        }

        return in_array($domain->status, ['active', 'inactive']) && !$domain->hasPendingActions();
    }

    /**
     * Buy a domain (create order).
     */
    public function buy(Request $request, Domain $domain)
    {
        $user = auth()->user();
        
        // Check if user is not buying their own domain
        if ($domain->user_id === $user->id) {
            return back()->with('error', 'You cannot buy your own domain.');
        }
        
        // Check if domain is available for purchase
        if ($domain->status !== 'active') {
            return back()->with('error', 'This domain is not available for purchase.');
        }

        $request->validate([
            'payment_method' => 'required|in:stripe,paypal,razorpay',
            'purchase_type' => 'nullable|in:bin,asking_price',
        ]);

        // Determine the purchase price based on purchase type
        $purchaseType = $request->get('purchase_type', 'asking_price');
        $domainPrice = $purchaseType === 'bin' && $domain->hasBin() 
            ? $domain->bin_price 
            : $domain->asking_price;

        // Calculate commission and amounts
        $commissionRate = $domain->commission_rate ?? 5.00;
        $commissionAmount = round(($domainPrice * $commissionRate) / 100, 2);
        $totalAmount = $domainPrice + $commissionAmount;
        $sellerAmount = $domainPrice - $commissionAmount;

        try {
            DB::beginTransaction();

            // Create the order
            $order = \App\Models\Order::create([
                'domain_id' => $domain->id,
                'buyer_id' => $user->id,
                'seller_id' => $domain->user_id,
                'domain_price' => $domainPrice,
                'commission_amount' => $commissionAmount,
                'total_amount' => $totalAmount,
                'seller_amount' => $sellerAmount,
                'status' => 'pending',
                'payment_method' => $request->payment_method,
                'purchase_type' => $purchaseType,
            ]);

            // Update domain status
            $domain->update(['status' => 'pending_sale']);

            DB::commit();

            // Redirect to payment processing
            return redirect()->route('orders.payment', $order)
                ->with('success', 'Order created successfully. Please complete payment.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create order. Please try again.');
        }
    }

    /**
     * Search domains by keyword and filters.
     */
    public function search(Request $request)
    {
        $query = Domain::query()->active()->with('user');

        // Keyword search
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->search($keyword);
        }

        // Price range filter
        if ($request->filled('min_price') || $request->filled('max_price')) {
            $minPrice = $request->min_price ?? 0;
            $maxPrice = $request->max_price ?? 999999;
            $query->inPriceRange($minPrice, $maxPrice);
        }

        // Category filter
        if ($request->filled('category')) {
            $query->inCategory($request->category);
        }

        // Extension filter
        if ($request->filled('extension')) {
            $query->withExtension($request->extension);
        }

        // Tags filter
        if ($request->filled('tags')) {
            $tags = explode(',', $request->tags);
            $query->withTags($tags);
        }

        // Featured domains
        if ($request->boolean('featured')) {
            $query->featured();
        }

        // Verified domains
        if ($request->boolean('verified')) {
            $query->verified();
        }

        // Domains with BIN
        if ($request->boolean('bin_only')) {
            $query->withBin();
        }

        // Domains that accept offers
        if ($request->boolean('offers_only')) {
            $query->acceptsOffers();
        }

        // Sort results
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        switch ($sortBy) {
            case 'price':
                $query->orderBy('asking_price', $sortOrder);
                break;
            case 'name':
                $query->orderBy('domain_name', $sortOrder);
                break;
            case 'views':
                $query->orderBy('view_count', $sortOrder);
                break;
            case 'favorites':
                $query->orderBy('favorite_count', $sortOrder);
                break;
            case 'offers':
                $query->orderBy('offer_count', $sortOrder);
                break;
            default:
                $query->orderBy('created_at', $sortOrder);
        }

        $domains = $query->paginate(20)->withQueryString();

        // Get available categories and extensions for filters
        $categories = Domain::active()->distinct()->pluck('category')->filter();
        $extensions = Domain::active()->distinct()->pluck('domain_extension')->filter();

        return view('domains.search', compact('domains', 'categories', 'extensions'));
    }

    /**
     * Show domains by category.
     */
    public function byCategory(string $category)
    {
        $domains = Domain::active()
            ->inCategory($category)
            ->with('user')
            ->latest()
            ->paginate(20);

        return view('domains.by-category', compact('domains', 'category'));
    }

    /**
     * Show domains by extension.
     */
    public function byExtension(string $extension)
    {
        $domains = Domain::active()
            ->withExtension($extension)
            ->with('user')
            ->latest()
            ->paginate(20);

        return view('domains.by-extension', compact('domains', 'extension'));
    }


}
