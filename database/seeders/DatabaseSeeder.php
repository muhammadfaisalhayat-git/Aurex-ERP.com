<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            BranchSeeder::class,
            WarehouseSeeder::class,
            UserSeeder::class,
            TaxSettingSeeder::class,
            CurrencySeeder::class,
            ProductCategorySeeder::class,
            ProductSeeder::class,
            CustomerGroupSeeder::class,
            CustomerSeeder::class,
            VendorSeeder::class,
            TrailerSeeder::class,
            MaintenanceWorkshopSeeder::class,
            CustomerRequestSeeder::class,
            QuotationSeeder::class,
                // SalesContractSeeder::class,
            SalesOrderSeeder::class,
            SalesInvoiceSeeder::class,
            SalesReturnSeeder::class,
            SupplyOrderSeeder::class,
                // PurchaseInvoiceSeeder::class,
                // StockSupplySeeder::class,
                // StockReceivingSeeder::class,
                // StockTransferSeeder::class,
                // StockTransferRequestSeeder::class,
                // StockIssueOrderSeeder::class,
                // CompositeAssemblySeeder::class,
            TransportOrderSeeder::class,
                // TransportContractSeeder::class,
            TransportClaimSeeder::class,
                // MaintenanceVoucherSeeder::class,
            CommissionRuleSeeder::class,
            CommissionRunSeeder::class,
            LocalPurchaseSeeder::class,
            // SupplierRegistrationSeeder::class,
            // CustomerRegistrationSeeder::class,
        ]);
    }
}
