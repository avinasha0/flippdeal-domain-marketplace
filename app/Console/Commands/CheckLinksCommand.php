<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Exception;

class CheckLinksCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'check:links 
                            {--export= : Export results to file (csv, json)}
                            {--timeout=30 : HTTP request timeout in seconds}
                            {--concurrent=5 : Number of concurrent requests}
                            {--skip-external : Skip external links}
                            {--skip-internal : Skip internal links}
                            {--public-only : Check only public routes (skip auth/protected routes)}';

    /**
     * The console command description.
     */
    protected $description = 'Check all links in the website for broken URLs and HTTP errors';

    /**
     * Base URL for the application
     */
    protected string $baseUrl;

    /**
     * Collection of all found links
     */
    protected array $allLinks = [];

    /**
     * Results of link checking
     */
    protected array $results = [
        'working' => [],
        'broken' => [],
        'redirects' => [],
        'timeouts' => [],
        'errors' => []
    ];

    /**
     * Statistics
     */
    protected array $stats = [
        'total' => 0,
        'working' => 0,
        'broken' => 0,
        'redirects' => 0,
        'timeouts' => 0,
        'errors' => 0
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Starting comprehensive link check for FlippDeal...');
        $this->newLine();

        // Set base URL - check if Laravel server is running on port 8000
        $this->baseUrl = config('app.url');
        
        // If using localhost without port, check if Laravel server is running on 8000
        if (str_contains($this->baseUrl, 'localhost') && !str_contains($this->baseUrl, ':8000') && !str_contains($this->baseUrl, ':80')) {
            $this->baseUrl = 'http://localhost:8000';
            $this->info("🔄 Detected Laravel server on port 8000, using: {$this->baseUrl}");
        } else {
            $this->info("Base URL: {$this->baseUrl}");
        }

        // Collect all links
        $this->info('📋 Collecting links from routes and views...');
        $this->collectLinksFromRoutes();
        $this->collectLinksFromViews();
        $this->collectLinksFromControllers();

        // Remove duplicates
        $this->allLinks = array_unique($this->allLinks);
        $this->stats['total'] = count($this->allLinks);

        $this->info("Found {$this->stats['total']} unique links to check");
        $this->newLine();

        if (empty($this->allLinks)) {
            $this->warn('No links found to check!');
            return 0;
        }

        // Check links
        $this->info('🔗 Checking links...');
        $this->checkLinks();

        // Display results
        $this->displayResults();

        // Export results if requested
        if ($export = $this->option('export')) {
            $this->exportResults($export);
        }

        return $this->stats['broken'] > 0 ? 1 : 0;
    }

    /**
     * Collect links from Laravel routes
     */
    protected function collectLinksFromRoutes(): void
    {
        $this->line('  📍 Scanning routes...');
        
        $routes = Route::getRoutes();
        $routeCount = 0;

        foreach ($routes as $route) {
            $uri = $route->uri();
            $methods = $route->methods();
            
            // Skip non-GET routes and API routes
            if (!in_array('GET', $methods) || str_starts_with($uri, 'api/')) {
                continue;
            }

            // Skip problematic routes
            if ($this->shouldSkipRoute($uri)) {
                continue;
            }

            // Skip non-public routes if public-only option is enabled
            if ($this->option('public-only') && !$this->isPublicRoute($uri)) {
                continue;
            }

            // Convert route parameters to example values
            $url = $this->convertRouteToUrl($uri);
            if ($url) {
                $this->allLinks[] = $url;
                $routeCount++;
            }
        }

        $this->line("  ✅ Found {$routeCount} route links");
    }

    /**
     * Check if a route should be skipped
     */
    protected function shouldSkipRoute(string $uri): bool
    {
        $skipPatterns = [
            // Authentication routes that require specific tokens
            '/reset-password/{token}',
            '/verify-email/{id}/{hash}',
            '/email/verify/{id}/{hash}',
            
            // Routes with complex parameters
            '/storage/{path}',
            '/domains/{domain}/verification',
            '/domains/{domain}/bids',
            '/messages/conversation/{id}',
            
            // API and system routes
            '/sanctum/csrf-cookie',
            '/up', // Health check
            
            // Routes that require authentication and will always redirect
            '/logout', // POST route that redirects
            '/email/verification-notification', // POST route
            
            // Authentication routes (will redirect when not authenticated)
            '/login',
            '/register', 
            '/forgot-password',
            '/reset-password',
            '/verify-email',
            '/confirm-password',
            '/password',
            
            // Protected user routes (require authentication)
            '/profile',
            '/my-domains',
            '/verification',
            '/verification/paypal',
            '/verification/government-id',
            '/verification/status',
            '/domains/create',
            '/orders',
            '/orders/create',
            '/offers',
            '/offers/create',
            '/messages',
            '/messages/create',
            '/favorites',
            '/bids',
            '/bids/create',
            '/search',
            
            // Admin routes (require admin authentication)
            '/admin',
            '/admin/stats',
            '/admin/users',
            '/admin/domains',
            '/admin/verifications',
            '/admin/settings',
            '/admin/audit-logs',
            '/admin/verifications/*/download',
        ];

        foreach ($skipPatterns as $pattern) {
            if (fnmatch($pattern, $uri)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if a route is public (accessible without authentication)
     */
    protected function isPublicRoute(string $uri): bool
    {
        $publicRoutes = [
            '/',
            '/domains', // Public domain listing
            '/domains/{domain}', // Public domain details
            '/categories/{category}', // Public category pages
            '/extensions/{extension}', // Public extension pages
        ];

        foreach ($publicRoutes as $pattern) {
            if (fnmatch($pattern, $uri)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if a link should be skipped
     */
    protected function shouldSkipLink(string $url): bool
    {
        $skipPatterns = [
            // Authentication routes
            '/login',
            '/register',
            '/forgot-password',
            '/reset-password',
            '/verify-email',
            '/confirm-password',
            '/password',
            '/logout',
            '/email/verification-notification',
            
            // Protected user routes
            '/profile',
            '/my-domains',
            '/verification',
            '/verification/paypal',
            '/verification/government-id',
            '/verification/status',
            '/domains/create',
            '/orders',
            '/orders/create',
            '/offers',
            '/offers/create',
            '/messages',
            '/messages/create',
            '/favorites',
            '/bids',
            '/bids/create',
            '/search',
            
            // Admin routes
            '/admin',
            '/admin/stats',
            '/admin/users',
            '/admin/domains',
            '/admin/verifications',
            '/admin/settings',
            '/admin/audit-logs',
            
            // System routes
            '/sanctum/csrf-cookie',
            '/up',
        ];

        foreach ($skipPatterns as $pattern) {
            if (str_contains($url, $pattern)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if a link is public (accessible without authentication)
     */
    protected function isPublicLink(string $url): bool
    {
        $publicPatterns = [
            '/',
            '/domains',
            '/categories',
            '/extensions',
        ];

        foreach ($publicPatterns as $pattern) {
            if (str_contains($url, $pattern)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Collect links from Blade views
     */
    protected function collectLinksFromViews(): void
    {
        $this->line('  📄 Scanning Blade views...');
        
        $viewPath = resource_path('views');
        $viewCount = 0;

        if (!File::exists($viewPath)) {
            $this->warn('  ⚠️  Views directory not found');
            return;
        }

        $files = File::allFiles($viewPath);
        
        foreach ($files as $file) {
            if ($file->getExtension() === 'php') {
                $content = File::get($file->getPathname());
                $links = $this->extractLinksFromContent($content, $file->getPathname());
                
                // Filter links based on options
                $filteredLinks = [];
                foreach ($links as $link) {
                    if ($this->shouldSkipLink($link)) {
                        continue;
                    }
                    
                    if ($this->option('public-only') && !$this->isPublicLink($link)) {
                        continue;
                    }
                    
                    $filteredLinks[] = $link;
                }
                
                $this->allLinks = array_merge($this->allLinks, $filteredLinks);
                $viewCount += count($filteredLinks);
            }
        }

        $this->line("  ✅ Found {$viewCount} links in views");
    }

    /**
     * Collect links from controllers (route() calls)
     */
    protected function collectLinksFromControllers(): void
    {
        $this->line('  🎮 Scanning controllers...');
        
        $controllerPath = app_path('Http/Controllers');
        $controllerCount = 0;

        if (!File::exists($controllerPath)) {
            $this->warn('  ⚠️  Controllers directory not found');
            return;
        }

        $files = File::allFiles($controllerPath);
        
        foreach ($files as $file) {
            if ($file->getExtension() === 'php') {
                $content = File::get($file->getPathname());
                $links = $this->extractRouteLinksFromContent($content);
                $this->allLinks = array_merge($this->allLinks, $links);
                $controllerCount += count($links);
            }
        }

        $this->line("  ✅ Found {$controllerCount} route links in controllers");
    }

    /**
     * Convert Laravel route to URL
     */
    protected function convertRouteToUrl(string $uri): ?string
    {
        // Skip routes with required parameters that we can't resolve
        if (preg_match('/\{[^?}]+\}/', $uri)) {
            // Skip routes that require specific IDs or complex parameters
            if (preg_match('/\{(id|user|domain|order|message|bid|offer|category|extension)\}/', $uri)) {
                return null;
            }
            
            // Try to replace with example values for simple parameters
            $uri = preg_replace('/\{[^?}]+\}/', '1', $uri);
        }

        // Remove optional parameters
        $uri = preg_replace('/\{[^?}]+\?\}/', '', $uri);
        
        // Clean up double slashes
        $uri = preg_replace('/\/+/', '/', $uri);
        $uri = trim($uri, '/');

        // Skip empty URIs
        if (empty($uri)) {
            return $this->baseUrl . '/';
        }

        return $this->baseUrl . '/' . $uri;
    }

    /**
     * Extract links from HTML content
     */
    protected function extractLinksFromContent(string $content, string $filePath): array
    {
        $links = [];

        // Extract href attributes from <a> tags (excluding Blade syntax)
        preg_match_all('/href=["\']([^"\']+)["\']/', $content, $matches);
        foreach ($matches[1] as $href) {
            // Skip Blade template syntax
            if (str_contains($href, '{{') || str_contains($href, '}}') || str_contains($href, '$')) {
                continue;
            }
            
            $url = $this->normalizeUrl($href);
            if ($url) {
                $links[] = $url;
            }
        }

        // Extract route() calls from Blade templates
        preg_match_all('/\{\{\s*route\(["\']([^"\']+)["\']/', $content, $matches);
        foreach ($matches[1] as $routeName) {
            try {
                $url = route($routeName);
                if ($url) {
                    $links[] = $url;
                }
            } catch (Exception $e) {
                // Skip invalid routes
            }
        }

        // Extract url() calls from Blade templates
        preg_match_all('/\{\{\s*url\(["\']([^"\']+)["\']/', $content, $matches);
        foreach ($matches[1] as $path) {
            $url = $this->baseUrl . '/' . ltrim($path, '/');
            $links[] = $url;
        }

        return $links;
    }

    /**
     * Extract route links from PHP content
     */
    protected function extractRouteLinksFromContent(string $content): array
    {
        $links = [];

        // Extract route() calls
        preg_match_all('/route\(["\']([^"\']+)["\']/', $content, $matches);
        foreach ($matches[1] as $routeName) {
            try {
                $url = route($routeName);
                if ($url) {
                    $links[] = $url;
                }
            } catch (Exception $e) {
                // Skip invalid routes
            }
        }

        return $links;
    }

    /**
     * Normalize URL
     */
    protected function normalizeUrl(string $url): ?string
    {
        // Skip javascript, mailto, tel, etc.
        if (preg_match('/^(javascript:|mailto:|tel:|#)/', $url)) {
            return null;
        }

        // Handle relative URLs
        if (str_starts_with($url, '/')) {
            return $this->baseUrl . $url;
        }

        // Handle protocol-relative URLs
        if (str_starts_with($url, '//')) {
            return 'https:' . $url;
        }

        // Return absolute URLs as-is
        if (preg_match('/^https?:\/\//', $url)) {
            return $url;
        }

        // Handle relative paths
        return $this->baseUrl . '/' . ltrim($url, '/');
    }

    /**
     * Check all collected links
     */
    protected function checkLinks(): void
    {
        $progressBar = $this->output->createProgressBar($this->stats['total']);
        $progressBar->start();

        $concurrent = (int) $this->option('concurrent');
        $timeout = (int) $this->option('timeout');
        $skipExternal = $this->option('skip-external');
        $skipInternal = $this->option('skip-internal');

        $chunks = array_chunk($this->allLinks, $concurrent);

        foreach ($chunks as $chunk) {
            $this->checkLinkChunk($chunk, $timeout, $skipExternal, $skipInternal);
            $progressBar->advance(count($chunk));
        }

        $progressBar->finish();
        $this->newLine(2);
    }

    /**
     * Check a chunk of links concurrently
     */
    protected function checkLinkChunk(array $links, int $timeout, bool $skipExternal, bool $skipInternal): void
    {
        foreach ($links as $link) {
            $isExternal = !str_starts_with($link, $this->baseUrl);

            if (($skipExternal && $isExternal) || ($skipInternal && !$isExternal)) {
                continue;
            }

            try {
                $response = Http::timeout($timeout)
                    ->withOptions(['allow_redirects' => false])
                    ->get($link);

                $this->processResponse($link, $response);
            } catch (Exception $exception) {
                $this->processException($link, $exception);
            }
        }
    }

    /**
     * Process HTTP response
     */
    protected function processResponse(string $url, $response): void
    {
        $statusCode = $response->status();
        $isExternal = !str_starts_with($url, $this->baseUrl);

        $result = [
            'url' => $url,
            'status' => $statusCode,
            'external' => $isExternal,
            'response_time' => $response->transferStats?->getHandlerStat('total_time') ?? 0
        ];

        if ($statusCode >= 200 && $statusCode < 300) {
            $this->results['working'][] = $result;
            $this->stats['working']++;
        } elseif ($statusCode >= 300 && $statusCode < 400) {
            $this->results['redirects'][] = $result;
            $this->stats['redirects']++;
        } else {
            $this->results['broken'][] = $result;
            $this->stats['broken']++;
        }
    }

    /**
     * Process HTTP exception
     */
    protected function processException(string $url, $exception): void
    {
        $isExternal = !str_starts_with($url, $this->baseUrl);
        
        $result = [
            'url' => $url,
            'status' => 'ERROR',
            'external' => $isExternal,
            'error' => $exception->getMessage()
        ];

        if (str_contains($exception->getMessage(), 'timeout')) {
            $this->results['timeouts'][] = $result;
            $this->stats['timeouts']++;
        } else {
            $this->results['errors'][] = $result;
            $this->stats['errors']++;
        }
    }

    /**
     * Display results summary
     */
    protected function displayResults(): void
    {
        $this->info('📊 Link Check Results Summary');
        $this->line('================================');

        $this->table(
            ['Status', 'Count', 'Percentage'],
            [
                ['✅ Working', $this->stats['working'], $this->getPercentage($this->stats['working'])],
                ['❌ Broken', $this->stats['broken'], $this->getPercentage($this->stats['broken'])],
                ['🔄 Redirects', $this->stats['redirects'], $this->getPercentage($this->stats['redirects'])],
                ['⏱️  Timeouts', $this->stats['timeouts'], $this->getPercentage($this->stats['timeouts'])],
                ['⚠️  Errors', $this->stats['errors'], $this->getPercentage($this->stats['errors'])],
                ['📊 Total', $this->stats['total'], '100%']
            ]
        );

        // Show broken links details
        if (!empty($this->results['broken'])) {
            $this->newLine();
            $this->error('❌ Broken Links:');
            $this->line('================');
            
            foreach ($this->results['broken'] as $link) {
                $this->line("  {$link['url']} - HTTP {$link['status']}");
            }
        }

        // Show redirects
        if (!empty($this->results['redirects'])) {
            $this->newLine();
            $this->warn('🔄 Redirects:');
            $this->line('=============');
            
            foreach ($this->results['redirects'] as $link) {
                $this->line("  {$link['url']} - HTTP {$link['status']}");
            }
        }

        // Show timeouts
        if (!empty($this->results['timeouts'])) {
            $this->newLine();
            $this->warn('⏱️  Timeouts:');
            $this->line('============');
            
            foreach ($this->results['timeouts'] as $link) {
                $this->line("  {$link['url']} - {$link['error']}");
            }
        }

        // Show errors
        if (!empty($this->results['errors'])) {
            $this->newLine();
            $this->error('⚠️  Errors:');
            $this->line('===========');
            
            foreach ($this->results['errors'] as $link) {
                $this->line("  {$link['url']} - {$link['error']}");
            }
        }
    }

    /**
     * Get percentage of total
     */
    protected function getPercentage(int $count): string
    {
        if ($this->stats['total'] === 0) {
            return '0%';
        }
        
        return round(($count / $this->stats['total']) * 100, 1) . '%';
    }

    /**
     * Export results to file
     */
    protected function exportResults(string $format): void
    {
        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = "link_check_results_{$timestamp}.{$format}";
        $filepath = storage_path("app/{$filename}");

        $exportData = [
            'timestamp' => now()->toISOString(),
            'base_url' => $this->baseUrl,
            'stats' => $this->stats,
            'results' => $this->results
        ];

        try {
            if ($format === 'json') {
                File::put($filepath, json_encode($exportData, JSON_PRETTY_PRINT));
            } elseif ($format === 'csv') {
                $this->exportToCsv($filepath, $exportData);
            } else {
                $this->error("Unsupported export format: {$format}");
                return;
            }

            $this->info("📁 Results exported to: {$filepath}");
        } catch (Exception $e) {
            $this->error("Failed to export results: {$e->getMessage()}");
        }
    }

    /**
     * Export results to CSV
     */
    protected function exportToCsv(string $filepath, array $data): void
    {
        $csv = fopen($filepath, 'w');
        
        // Write header
        fputcsv($csv, ['URL', 'Status', 'Type', 'External', 'Response Time', 'Error']);

        // Write all results
        foreach (['working', 'broken', 'redirects', 'timeouts', 'errors'] as $type) {
            foreach ($data['results'][$type] as $result) {
                fputcsv($csv, [
                    $result['url'],
                    $result['status'],
                    $type,
                    $result['external'] ? 'Yes' : 'No',
                    $result['response_time'] ?? '',
                    $result['error'] ?? ''
                ]);
            }
        }

                fclose($csv);
    }
}
