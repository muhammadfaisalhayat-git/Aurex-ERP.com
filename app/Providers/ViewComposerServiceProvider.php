<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Branch;
use App\Models\Warehouse;
use App\Models\TaxSetting;
use App\Models\SystemSetting;

class ViewComposerServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        View::composer('*', function ($view) {
            $view->with('currentLocale', app()->getLocale());
            $view->with('isRtl', app()->getLocale() === 'ar');
            $view->with('appName', config('app.name'));
        });

        View::composer(['layouts.app', 'layouts.sidebar', 'dashboard'], function ($view) {
            if (auth()->check()) {
                $user = auth()->user();
                $view->with('userBranches', $user->branch_id ? [$user->branch] : Branch::active()->get());
                $view->with('userWarehouses', $user->warehouses()->active()->get());
                $view->with('isSuperAdmin', $user->isSuperAdmin());
            }
        });

        View::composer(['sales.invoices.*', 'purchases.invoices.*'], function ($view) {
            $taxSetting = TaxSetting::first();
            $view->with('taxSetting', $taxSetting);
        });
    }
}
