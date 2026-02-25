<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Database\Eloquent\Relations\Relation;

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
    }
}
