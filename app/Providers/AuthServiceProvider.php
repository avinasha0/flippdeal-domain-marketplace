<?php

namespace App\Providers;

use App\Models\Domain;
use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Define gates for Domain model
        Gate::define('create', function (User $user, $model) {
            if ($model === Domain::class) {
                // Allow domain creation if user is verified or has completed basic verification
                return $user->isVerified() || $user->hasCompletedBasicVerification();
            }
            return false;
        });

        Gate::define('view', function (User $user, Domain $domain) {
            // Users can view their own domains or published domains
            return $user->id === $domain->user_id || $domain->status === 'active';
        });

        Gate::define('update', function (User $user, Domain $domain) {
            // Users can only update their own domains
            return $user->id === $domain->user_id;
        });

        Gate::define('delete', function (User $user, Domain $domain) {
            // Users can only delete their own domains
            return $user->id === $domain->user_id;
        });

        // Admin gates
        Gate::define('admin', function (User $user) {
            return $user->isAdmin();
        });
    }
}
