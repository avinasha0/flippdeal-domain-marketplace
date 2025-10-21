<?php

namespace App\Providers;

use App\Contracts\DnsResolverInterface;
use App\Contracts\VirusScannerInterface;
use App\Services\DnsResolverService;
use App\Services\ClamAvVirusScanner;
use App\Services\StubVirusScanner;
use Illuminate\Support\ServiceProvider;

class SecurityServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register DNS resolver
        $this->app->bind(DnsResolverInterface::class, DnsResolverService::class);

        // Register virus scanner based on configuration
        $this->app->bind(VirusScannerInterface::class, function ($app) {
            $provider = config('upload.virus_scanning.provider', 'clamav');
            
            return match ($provider) {
                'clamav' => new ClamAvVirusScanner(),
                'stub' => new StubVirusScanner(),
                default => new StubVirusScanner(),
            };
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
