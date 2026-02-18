<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LanguageController;

// Admin Controllers
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\WarehouseController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\DashboardLayoutController;

// Sales Controllers
use App\Http\Controllers\Sales\CustomerController;
use App\Http\Controllers\Sales\CustomerRequestController;
use App\Http\Controllers\Sales\QuotationController;
use App\Http\Controllers\Sales\SalesContractController;
use App\Http\Controllers\Sales\SalesOrderController;
use App\Http\Controllers\Sales\SalesInvoiceController;
use App\Http\Controllers\Sales\SalesReturnController;
use App\Http\Controllers\Sales\CommissionController;
use App\Http\Controllers\Sales\CustomerRegistrationController;

// Purchase Controllers
use App\Http\Controllers\Purchases\VendorController;
use App\Http\Controllers\Purchases\SupplyOrderController;
use App\Http\Controllers\Purchases\PurchaseInvoiceController;
use App\Http\Controllers\Purchases\LocalPurchaseController;
use App\Http\Controllers\Purchases\SupplierRegistrationController;
use App\Http\Controllers\Purchases\PurchaseAIController;

// Inventory Controllers
use App\Http\Controllers\Inventory\ProductController;
use App\Http\Controllers\Inventory\ProductCategoryController;
use App\Http\Controllers\Inventory\StockSupplyController;
use App\Http\Controllers\Inventory\StockReceivingController;
use App\Http\Controllers\Inventory\StockTransferController;
use App\Http\Controllers\Inventory\StockTransferRequestController;
use App\Http\Controllers\Inventory\StockIssueOrderController;
use App\Http\Controllers\Inventory\CompositeAssemblyController;
use App\Http\Controllers\Inventory\StockLedgerController;
use App\Http\Controllers\Inventory\BarcodeController;
use App\Http\Controllers\Inventory\BarcodeSettingController;

// Transport Controllers
use App\Http\Controllers\Transport\TrailerController;
use App\Http\Controllers\Transport\TransportOrderController;
use App\Http\Controllers\Transport\TransportContractController;
use App\Http\Controllers\Transport\TransportClaimController;

// Maintenance Controllers
use App\Http\Controllers\Maintenance\MaintenanceWorkshopController;
use App\Http\Controllers\Maintenance\MaintenanceVoucherController;

// Report Controllers
use App\Http\Controllers\Reports\SalesReportController;
use App\Http\Controllers\Reports\InventoryReportController;
use App\Http\Controllers\Reports\TaxReportController;
use App\Http\Controllers\Reports\SupplierReportController;
use App\Http\Controllers\Accounting\ChartOfAccountController;
use App\Http\Controllers\Accounting\JournalVoucherController;
use App\Http\Controllers\Accounting\AccountingReportController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Tenant Switching
Route::middleware(['auth'])->group(function () {
    Route::post('/switch-company', [\App\Http\Controllers\Admin\TenantController::class, 'switchCompany'])->name('switch-company');
    Route::post('/switch-branch', [\App\Http\Controllers\Admin\TenantController::class, 'switchBranch'])->name('switch-branch');
});

/*
|--------------------------------------------------------------------------
| Language Switcher
|--------------------------------------------------------------------------
*/

Route::get('/language/{locale}', [LanguageController::class, 'switch'])->name('language.switch');

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'set.locale'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/widgets/reorder', [DashboardController::class, 'reorderWidgets'])->name('dashboard.widgets.reorder');
    Route::post('/dashboard/widgets/toggle', [DashboardController::class, 'toggleWidget'])->name('dashboard.widgets.toggle');
    Route::post('/dashboard/widgets/reset', [DashboardController::class, 'resetWidgets'])->name('dashboard.widgets.reset');

    /*
    |--------------------------------------------------------------------------
    | Admin Routes (Super Admin Only)
    |--------------------------------------------------------------------------
    */

    Route::middleware(['can:view-user-management'])->prefix('admin')->name('admin.')->group(function () {
        // Users
        Route::resource('users', UserController::class);
        Route::post('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::post('users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
        Route::get('users/{user}/permissions', [UserController::class, 'editPermissions'])->name('users.permissions');
        Route::post('users/{user}/permissions', [UserController::class, 'updatePermissions'])->name('users.permissions.update');

        // Roles
        Route::resource('roles', RoleController::class);
        Route::get('roles/{role}/permissions', [RoleController::class, 'editPermissions'])->name('roles.permissions');
        Route::post('roles/{role}/permissions', [RoleController::class, 'updatePermissions'])->name('roles.permissions.update');

        // Companies
        Route::resource('companies', CompanyController::class);

        // Branches
        Route::resource('branches', BranchController::class);

        // Warehouses
        Route::resource('warehouses', WarehouseController::class);

        // Settings
        Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('settings', [SettingController::class, 'update'])->name('settings.update');

        // Dashboard Layouts
        Route::resource('dashboard-layouts', DashboardLayoutController::class);
    });

    /*
    |--------------------------------------------------------------------------
    | Sales Routes
    |--------------------------------------------------------------------------
    */

    Route::prefix('sales')->name('sales.')->group(function () {
        // Customers
        Route::resource('customers', CustomerController::class);
        Route::get('customers/{customer}/statement', [CustomerController::class, 'statement'])->name('customers.statement');

        // Customer Requests
        Route::resource('customer-requests', CustomerRequestController::class);
        Route::get('customer-requests/{customerRequest}/pdf', [CustomerRequestController::class, 'exportPdf'])->name('customer-requests.pdf');
        Route::get('customer-requests/{customerRequest}/whatsapp', [CustomerRequestController::class, 'sendWhatsApp'])->name('customer-requests.whatsapp');
        Route::post('customer-requests/{customerRequest}/convert', [CustomerRequestController::class, 'convertToQuotation'])->name('customer-requests.convert');

        // Quotations
        Route::resource('quotations', QuotationController::class);
        Route::post('quotations/{quotation}/send', [QuotationController::class, 'send'])->name('quotations.send');
        Route::post('quotations/{quotation}/convert', [QuotationController::class, 'convert'])->name('quotations.convert');
        Route::get('quotations/{quotation}/pdf', [QuotationController::class, 'downloadPdf'])->name('quotations.pdf');
        Route::get('quotations/{quotation}/print', [QuotationController::class, 'print'])->name('quotations.print');
        Route::get('quotations/{quotation}/whatsapp', [QuotationController::class, 'sendWhatsApp'])->name('quotations.whatsapp');
        Route::post('quotations/{quotation}/revise', [QuotationController::class, 'revise'])->name('quotations.revise');

        // Sales Contracts
        Route::resource('contracts', SalesContractController::class);
        Route::post('contracts/{contract}/activate', [SalesContractController::class, 'activate'])->name('contracts.activate');
        Route::post('contracts/{contract}/terminate', [SalesContractController::class, 'terminate'])->name('contracts.terminate');
        Route::post('contracts/{contract}/generate-invoice', [SalesContractController::class, 'generateInvoice'])->name('contracts.generate-invoice');

        // Sales Orders
        Route::resource('sales-orders', SalesOrderController::class);
        Route::post('sales-orders/{salesOrder}/confirm', [SalesOrderController::class, 'confirm'])->name('sales-orders.confirm');
        Route::post('sales-orders/create-from-quotation', [SalesOrderController::class, 'createFromQuotation'])->name('sales-orders.create-from-quotation');
        Route::post('sales-orders/{salesOrder}/convert-to-invoice', [SalesOrderController::class, 'convertToInvoice'])->name('sales-orders.convert-to-invoice');
        Route::get('sales-orders/{salesOrder}/pending-items', [SalesOrderController::class, 'getPendingItems'])->name('sales-orders.pending-items');
        Route::get('sales-orders/{salesOrder}/print', [SalesOrderController::class, 'print'])->name('sales-orders.print');

        // Sales Invoices
        Route::get('invoices/import-sources', [SalesInvoiceController::class, 'getSourceDocuments'])->name('invoices.import-sources');
        Route::get('invoices/source-data/{type}/{id}', [SalesInvoiceController::class, 'getSourceDocumentData'])->name('invoices.source-data');
        Route::resource('invoices', SalesInvoiceController::class);
        Route::post('invoices/{invoice}/post', [SalesInvoiceController::class, 'post'])->name('invoices.post');
        Route::post('invoices/{invoice}/unpost', [SalesInvoiceController::class, 'unpost'])->name('invoices.unpost');
        Route::get('invoices/{invoice}/pdf', [SalesInvoiceController::class, 'downloadPdf'])->name('invoices.pdf');
        Route::get('invoices/{invoice}/whatsapp', [SalesInvoiceController::class, 'sendWhatsApp'])->name('invoices.whatsapp');
        Route::get('invoices/{invoice}/print', [SalesInvoiceController::class, 'print'])->name('invoices.print');

        // Import Invoice from Quotation
        Route::get('invoices/create-from-quotation/{quotation}', [SalesInvoiceController::class, 'createFromQuotation'])->name('invoices.create-from-quotation');
        Route::post('invoices/store-from-quotation/{quotation}', [SalesInvoiceController::class, 'storeFromQuotation'])->name('invoices.store-from-quotation');

        // Sales Returns
        Route::resource('returns', SalesReturnController::class);
        Route::post('returns/{salesReturn}/post', [SalesReturnController::class, 'post'])->name('returns.post');

        // Commissions
        Route::get('commissions/rules', [CommissionController::class, 'rulesIndex'])->name('commissions.rules');
        Route::get('commissions/rules/create', [CommissionController::class, 'rulesCreate'])->name('commissions.rules.create');
        Route::post('commissions/rules', [CommissionController::class, 'rulesStore'])->name('commissions.rules.store');
        Route::get('commissions/rules/{rule}/edit', [CommissionController::class, 'rulesEdit'])->name('commissions.rules.edit');
        Route::put('commissions/rules/{rule}', [CommissionController::class, 'rulesUpdate'])->name('commissions.rules.update');
        Route::delete('commissions/rules/{rule}', [CommissionController::class, 'rulesDestroy'])->name('commissions.rules.destroy');

        Route::get('commissions/runs', [CommissionController::class, 'runsIndex'])->name('commissions.runs');
        Route::get('commissions/runs/create', [CommissionController::class, 'runsCreate'])->name('commissions.runs.create');
        Route::post('commissions/runs', [CommissionController::class, 'runsStore'])->name('commissions.runs.store');
        Route::get('commissions/runs/{run}', [CommissionController::class, 'runsShow'])->name('commissions.runs.show');
        Route::post('commissions/runs/{run}/calculate', [CommissionController::class, 'runsCalculate'])->name('commissions.runs.calculate');
        Route::post('commissions/runs/{run}/approve', [CommissionController::class, 'runsApprove'])->name('commissions.runs.approve');
        Route::get('commissions/runs/{run}/export', [CommissionController::class, 'runsExport'])->name('commissions.runs.export');

        // Customer Registrations
        Route::resource('customer-registrations', CustomerRegistrationController::class);
        Route::post('customer-registrations/{customerRegistration}/approve', [CustomerRegistrationController::class, 'approve'])->name('customer-registrations.approve');
        Route::post('customer-registrations/{customerRegistration}/reject', [CustomerRegistrationController::class, 'reject'])->name('customer-registrations.reject');
        Route::post('customer-registrations/{customerRegistration}/convert', [CustomerRegistrationController::class, 'convertToCustomer'])->name('customer-registrations.convert');
        Route::delete('customer-registrations/documents/{document}', [CustomerRegistrationController::class, 'deleteDocument'])->name('customer-registrations.documents.delete');
    });

    /*
    |--------------------------------------------------------------------------
    | Purchases Routes
    |--------------------------------------------------------------------------
    */

    Route::prefix('purchases')->name('purchases.')->group(function () {
        // Vendors
        Route::resource('vendors', VendorController::class);
        Route::get('vendors/{vendor}/statement', [VendorController::class, 'statement'])->name('vendors.statement');

        // Supply Orders
        Route::resource('supply-orders', SupplyOrderController::class);
        Route::post('supply-orders/{supplyOrder}/send', [SupplyOrderController::class, 'send'])->name('supply-orders.send');
        Route::post('supply-orders/{supplyOrder}/convert-to-invoice', [SupplyOrderController::class, 'convertToInvoice'])->name('supply-orders.convert-to-invoice');
        Route::get('supply-orders/{supplyOrder}/print', [SupplyOrderController::class, 'print'])->name('supply-orders.print');

        // Purchase Invoices
        Route::resource('invoices', PurchaseInvoiceController::class);
        Route::post('invoices/{invoice}/post', [PurchaseInvoiceController::class, 'post'])->name('invoices.post');
        Route::post('invoices/{invoice}/unpost', [PurchaseInvoiceController::class, 'unpost'])->name('invoices.unpost');
        Route::get('invoices/{invoice}/print', [PurchaseInvoiceController::class, 'print'])->name('invoices.print');
        Route::post('invoices/ocr/extract', [\App\Http\Controllers\Purchases\InvoiceOcrController::class, 'extractData'])->name('invoices.ocr.extract');

        // Local Purchases
        Route::resource('local-purchases', LocalPurchaseController::class);
        Route::patch('local-purchases/{localPurchase}/post', [LocalPurchaseController::class, 'post'])->name('local-purchases.post');
        Route::patch('local-purchases/{localPurchase}/unpost', [LocalPurchaseController::class, 'unpost'])->name('local-purchases.unpost');

        // Supplier Registrations
        Route::resource('supplier-registrations', SupplierRegistrationController::class);
        Route::post('supplier-registrations/{supplierRegistration}/approve', [SupplierRegistrationController::class, 'approve'])->name('supplier-registrations.approve');
        Route::post('supplier-registrations/{supplierRegistration}/reject', [SupplierRegistrationController::class, 'reject'])->name('supplier-registrations.reject');
        Route::post('supplier-registrations/{supplierRegistration}/convert', [SupplierRegistrationController::class, 'convertToVendor'])->name('supplier-registrations.convert');
        Route::delete('supplier-registrations/documents/{document}', [SupplierRegistrationController::class, 'deleteDocument'])->name('supplier-registrations.documents.delete');

        // AI Invoice Scanning
        Route::post('ai/scan', [App\Http\Controllers\Purchases\PurchaseAIController::class, 'scan'])->name('ai.scan');
        Route::post('ai/save-to-vendor', [App\Http\Controllers\Purchases\PurchaseAIController::class, 'saveToVendor'])->name('ai.save-to-vendor');
    });

    /*
    |--------------------------------------------------------------------------
    | Inventory Routes
    |--------------------------------------------------------------------------
    */

    // HR Routes
    Route::prefix('hr')->name('hr.')->group(function () {
        Route::get('employees/{employee}/salary-slip', [\App\Http\Controllers\HR\EmployeeController::class, 'salarySlip'])->name('employees.salary-slip');
        Route::resource('employees', \App\Http\Controllers\HR\EmployeeController::class);
        Route::resource('departments', \App\Http\Controllers\HR\DepartmentController::class);
        Route::resource('designations', \App\Http\Controllers\HR\DesignationController::class);
        Route::resource('salaries', \App\Http\Controllers\HR\SalaryController::class);
        Route::resource('experience', \App\Http\Controllers\HR\ExperienceController::class);
    });

    Route::prefix('inventory')->name('inventory.')->group(function () {
        // Products
        Route::resource('products', ProductController::class);
        Route::resource('categories', ProductCategoryController::class);
        Route::get('products/{product}/bom', [ProductController::class, 'bom'])->name('products.bom');
        Route::post('products/{product}/bom', [ProductController::class, 'updateBom'])->name('products.bom.update');

        // Barcodes
        Route::get('barcodes', [BarcodeController::class, 'index'])->name('barcodes.index');
        Route::get('barcodes/search', [BarcodeController::class, 'search'])->name('barcodes.search');
        Route::get('barcodes/print', [BarcodeController::class, 'print'])->name('barcodes.print');

        // Barcode Settings
        Route::get('barcodes/settings', [BarcodeSettingController::class, 'edit'])->name('barcodes.settings.edit');
        Route::put('barcodes/settings', [BarcodeSettingController::class, 'update'])->name('barcodes.settings.update');
        Route::post('barcodes/settings/reset', [BarcodeSettingController::class, 'reset'])->name('barcodes.settings.reset');

        // Stock Supply
        Route::resource('stock-supply', StockSupplyController::class);
        Route::post('stock-supply/{stockSupply}/confirm', [StockSupplyController::class, 'confirm'])->name('stock-supply.confirm');
        Route::post('stock-supply/{stockSupply}/post', [StockSupplyController::class, 'post'])->name('stock-supply.post');

        // Stock Receiving
        Route::resource('stock-receiving', StockReceivingController::class);
        Route::post('stock-receiving/{stockReceiving}/receive', [StockReceivingController::class, 'receive'])->name('stock-receiving.receive');

        // Stock Transfers
        Route::resource('stock-transfers', StockTransferController::class);
        Route::post('stock-transfers/{stockTransfer}/approve', [StockTransferController::class, 'approve'])->name('stock-transfers.approve');
        Route::post('stock-transfers/{stockTransfer}/receive', [StockTransferController::class, 'receive'])->name('stock-transfers.receive');

        // Stock Transfer Requests
        Route::resource('transfer-requests', StockTransferRequestController::class);
        Route::post('transfer-requests/{transferRequest}/approve', [StockTransferRequestController::class, 'approve'])->name('transfer-requests.approve');
        Route::post('transfer-requests/{transferRequest}/reject', [StockTransferRequestController::class, 'reject'])->name('transfer-requests.reject');
        Route::post('transfer-requests/{transferRequest}/execute', [StockTransferRequestController::class, 'execute'])->name('transfer-requests.execute');

        // Stock Issue Orders
        Route::resource('issue-orders', StockIssueOrderController::class);
        Route::post('issue-orders/{issueOrder}/post', [StockIssueOrderController::class, 'post'])->name('issue-orders.post');
        Route::post('issue-orders/{issueOrder}/unpost', [StockIssueOrderController::class, 'unpost'])->name('issue-orders.unpost');

        // Composite Assemblies
        Route::resource('assemblies', CompositeAssemblyController::class);
        Route::post('assemblies/{assembly}/post', [CompositeAssemblyController::class, 'post'])->name('assemblies.post');

        // Stock Ledger
        Route::get('stock-ledger', [StockLedgerController::class, 'index'])->name('stock-ledger.index');
        Route::get('stock-ledger/product/{product}', [StockLedgerController::class, 'product'])->name('stock-ledger.product');
    });

    /*
    |--------------------------------------------------------------------------
    | Transport Routes
    |--------------------------------------------------------------------------
    */

    Route::prefix('transport')->name('transport.')->group(function () {
        // Trailers
        Route::resource('trailers', TrailerController::class);

        // Transport Orders
        Route::resource('orders', TransportOrderController::class);
        Route::post('orders/{order}/close', [TransportOrderController::class, 'close'])->name('orders.close');
        Route::get('orders/{order}/loading-sheet', [TransportOrderController::class, 'loadingSheet'])->name('orders.loading-sheet');

        // Transport Contracts
        Route::resource('contracts', TransportContractController::class);
        Route::post('contracts/{contract}/close', [TransportContractController::class, 'close'])->name('contracts.close');

        // Transport Claims
        Route::resource('claims', TransportClaimController::class);
        Route::post('claims/{claim}/review', [TransportClaimController::class, 'review'])->name('claims.review');
        Route::post('claims/{claim}/approve', [TransportClaimController::class, 'approve'])->name('claims.approve');
        Route::post('claims/{claim}/reject', [TransportClaimController::class, 'reject'])->name('claims.reject');
        Route::post('claims/{claim}/settle', [TransportClaimController::class, 'settle'])->name('claims.settle');
    });

    /*
    |--------------------------------------------------------------------------
    | Maintenance Routes
    |--------------------------------------------------------------------------
    */

    Route::prefix('maintenance')->name('maintenance.')->group(function () {
        // Workshops
        Route::resource('workshops', MaintenanceWorkshopController::class);

        // Vouchers
        Route::resource('vouchers', MaintenanceVoucherController::class);
        Route::post('vouchers/{voucher}/start', [MaintenanceVoucherController::class, 'start'])->name('vouchers.start');
        Route::post('vouchers/{voucher}/complete', [MaintenanceVoucherController::class, 'complete'])->name('vouchers.complete');
        Route::post('vouchers/{voucher}/add-parts', [MaintenanceVoucherController::class, 'addParts'])->name('vouchers.add-parts');
    });

    /*
    |--------------------------------------------------------------------------
    | Reports Routes
    |--------------------------------------------------------------------------
    */

    Route::prefix('reports')->name('reports.')->group(function () {
        // Sales Reports
        Route::get('sales', [SalesReportController::class, 'index'])->name('sales.index');
        Route::get('sales/by-customer', [SalesReportController::class, 'byCustomer'])->name('sales.by-customer');
        Route::get('sales/by-item', [SalesReportController::class, 'byItem'])->name('sales.by-item');
        Route::get('sales/date-wise', [SalesReportController::class, 'dateWise'])->name('sales.date-wise');
        Route::get('sales/export-by-customer', [SalesReportController::class, 'exportByCustomer'])->name('sales.export-by-customer');
        Route::get('sales/export-by-item', [SalesReportController::class, 'exportByItem'])->name('sales.export-by-item');

        // Supplier Reports
        Route::get('suppliers', [SupplierReportController::class, 'index'])->name('suppliers.index');
        Route::get('suppliers/by-code-name', [SupplierReportController::class, 'byCodeOrName'])->name('suppliers.by-code-name');
        Route::get('suppliers/local-purchases', [SupplierReportController::class, 'localPurchases'])->name('suppliers.local-purchases');
        Route::get('suppliers/purchase-summary', [SupplierReportController::class, 'purchaseSummary'])->name('suppliers.purchase-summary');
        Route::get('suppliers/export-by-code-name', [SupplierReportController::class, 'exportByCodeName'])->name('suppliers.export-by-code-name');

        // Tax Reports
        Route::get('tax-summary', [TaxReportController::class, 'summary'])->name('tax.summary');
        Route::get('tax-by-invoice', [TaxReportController::class, 'byInvoice'])->name('tax.by-invoice');

        // Inventory Reports
        Route::get('inventory/valuation', [InventoryReportController::class, 'valuation'])->name('inventory.valuation');
        Route::get('inventory/movements', [InventoryReportController::class, 'movements'])->name('inventory.movements');
        Route::get('inventory/low-stock', [InventoryReportController::class, 'lowStock'])->name('inventory.low-stock');
    });

    /*
    |--------------------------------------------------------------------------
    | Accounting Routes
    |--------------------------------------------------------------------------
    */

    Route::prefix('accounting')->name('accounting.')->group(function () {
        Route::prefix('gl')->name('gl.')->group(function () {
            // Chart of Accounts
            Route::resource('coa', ChartOfAccountController::class);
            Route::post('account-types', [App\Http\Controllers\Accounting\AccountTypeController::class, 'store'])->name('account-types.store');
            Route::get('account-types/{accountType}', [App\Http\Controllers\Accounting\AccountTypeController::class, 'show'])->name('account-types.show');
            Route::put('account-types/{accountType}', [App\Http\Controllers\Accounting\AccountTypeController::class, 'update'])->name('account-types.update');

            // Transactions
            Route::prefix('transactions')->name('transactions.')->group(function () {
                Route::resource('jv', JournalVoucherController::class);
                Route::get('jv/{jv}/print', [JournalVoucherController::class, 'print'])->name('jv.print');
                Route::post('jv/{jv}/post', [JournalVoucherController::class, 'post'])->name('jv.post');
            });

            // Reports
            Route::prefix('reports')->name('reports.')->group(function () {
                Route::get('account-statement', [AccountingReportController::class, 'accountStatement'])->name('account-statement');
                Route::post('account-statement', [AccountingReportController::class, 'generateAccountStatement']);
            });
        });
    });

    /*
    |--------------------------------------------------------------------------
    | AJAX Routes
    |--------------------------------------------------------------------------
    */

    Route::prefix('ajax')->name('ajax.')->group(function () {
        Route::get('products/search', [ProductController::class, 'ajaxSearch'])->name('products.search');
        Route::get('customers/search', [CustomerController::class, 'ajaxSearch'])->name('customers.search');
        Route::get('vendors/search', [VendorController::class, 'ajaxSearch'])->name('vendors.search');
        Route::get('warehouses/by-branch', [WarehouseController::class, 'ajaxByBranch'])->name('warehouses.by-branch');
        Route::get('products/stock', [ProductController::class, 'ajaxStock'])->name('products.stock');
        Route::post('purchases/scan', [PurchaseAIController::class, 'scan'])->name('purchases.scan');
    });
});
