<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Relations\Relation;

use Illuminate\Pagination\Paginator;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

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

        // Morph Map for Ledger References
        Relation::morphMap([
            'sales_invoice' => 'App\Models\SalesInvoice',
            'purchase_invoice' => 'App\Models\PurchaseInvoice',
            'journal_voucher' => 'App\Models\JournalVoucher',
            'payment_voucher' => 'App\Models\PaymentVoucher',
            'receipt_voucher' => 'App\Models\ReceiptVoucher',
            'stock_issue' => 'App\Models\StockIssueOrder',
            'stock_receiving' => 'App\Models\StockReceiving',
            'stock_supply' => 'App\Models\StockSupply',
            'sales_return' => 'App\Models\SalesReturn',
            'production_order' => 'App\Models\ProductionOrder',
            'fuel_log' => 'App\Models\FuelLog',
            'maintenance_voucher' => 'App\Models\MaintenanceVoucher',
        ]);

        // Configure Rate Limiters
        $this->configureRateLimiting();

        // Configure Google Drive Storage Driver
        try {
            Storage::extend('google', function ($app, $config) {
                $options = [];

                if (!empty($config['teamDriveId'] ?? null)) {
                    $options['teamDriveId'] = $config['teamDriveId'];
                }

                if (!empty($config['sharedFolderId'] ?? null)) {
                    $options['sharedFolderId'] = $config['sharedFolderId'];
                }

                $client = new \Google\Client();
                $httpClient = new \GuzzleHttp\Client(['verify' => false]);
                $client->setHttpClient($httpClient);
                $client->setClientId($config['clientId']);
                $client->setClientSecret($config['clientSecret']);
                $client->refreshToken($config['refreshToken']);

                $service = new \Google\Service\Drive($client);
                $adapter = new \Masbug\Flysystem\GoogleDriveAdapter($service, $config['folder'] ?? '/', $options);
                $driver = new \League\Flysystem\Filesystem($adapter);

                return new \Illuminate\Filesystem\FilesystemAdapter($driver, $adapter, $config);
            });
        } catch (\Exception $e) {
            // Log the exception if needed
            \Illuminate\Support\Facades\Log::error('Google Drive Storage Extension Error: ' . $e->getMessage());
        }
    }

    /**
     * Configure the rate limiters for the application.
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip());
        });

        RateLimiter::for('global', function (Request $request) {
            return Limit::perMinute(120)->by($request->ip());
        });
    }
}
