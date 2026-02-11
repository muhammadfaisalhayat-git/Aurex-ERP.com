<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;

use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::useBootstrapFive();
        Schema::defaultStringLength(191);

        // RTL directive for Arabic language
        Blade::directive('rtl', function () {
            return "<?php echo app()->getLocale() === 'ar' ? 'rtl' : 'ltr'; ?>";
        });

        // Language switcher route
        Route::macro('localized', function ($uri, $action) {
            return Route::get($uri, $action)
                ->middleware(['web', 'auth']);
        });
    }
}
