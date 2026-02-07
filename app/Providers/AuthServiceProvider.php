<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        //
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        // Super Admin gate - has all permissions
        Gate::before(function ($user, $ability) {
            return $user->hasRole('Super Admin') ? true : null;
        });

        // User Management gate - only Super Admin
        Gate::define('manage-users', function ($user) {
            return $user->hasRole('Super Admin');
        });

        // View User Management menu
        Gate::define('view-user-management', function ($user) {
            return $user->hasRole('Super Admin');
        });

        // Manage roles and permissions
        Gate::define('manage-roles', function ($user) {
            return $user->hasRole('Super Admin');
        });

        // Post/Unpost documents
        Gate::define('post-documents', function ($user) {
            return $user->hasAnyPermission(['post invoices', 'post purchases', 'post inventory']);
        });

        // Approve documents
        Gate::define('approve-documents', function ($user) {
            return $user->hasAnyPermission(['approve sales', 'approve purchases', 'approve transfers']);
        });

        // Export data
        Gate::define('export-data', function ($user) {
            return $user->hasPermissionTo('export data');
        });

        // Cross-branch access
        Gate::define('cross-branch-access', function ($user) {
            return $user->hasRole('Super Admin');
        });
    }
}
