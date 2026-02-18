<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        $settings = [
            // Sections
            ['key' => 'module_administration', 'display_en' => 'Administration Section', 'display_ar' => 'قسم الإدارة'],
            ['key' => 'module_sales', 'display_en' => 'Sales Section', 'display_ar' => 'قسم المبيعات'],
            ['key' => 'module_purchases', 'display_en' => 'Purchases Section', 'display_ar' => 'قسم المشتريات'],
            ['key' => 'module_inventory', 'display_en' => 'Inventory Section', 'display_ar' => 'قسم المخزون'],
            ['key' => 'module_hr', 'display_en' => 'Human Resources Section', 'display_ar' => 'قسم الموارد البشرية'],
            ['key' => 'module_transport', 'display_en' => 'Transport Section', 'display_ar' => 'قسم النقل'],
            ['key' => 'module_maintenance', 'display_en' => 'Maintenance Section', 'display_ar' => 'قسم الصيانة'],
            ['key' => 'module_reports', 'display_en' => 'Reports Section', 'display_ar' => 'قسم التقارير'],

            // Administration Options
            ['key' => 'sidebar_user_management', 'display_en' => 'User Management', 'display_ar' => 'إدارة المستخدمين'],
            ['key' => 'sidebar_organization', 'display_en' => 'Organization', 'display_ar' => 'المؤسسة'],
            ['key' => 'sidebar_settings', 'display_en' => 'Settings', 'display_ar' => 'الإعدادات'],

            // Sales Options
            ['key' => 'sidebar_customers', 'display_en' => 'Customers', 'display_ar' => 'العملاء'],
            ['key' => 'sidebar_sales_documents', 'display_en' => 'Sales Documents', 'display_ar' => 'وثائق المبيعات'],
            ['key' => 'sidebar_sales_invoices', 'display_en' => 'Sales Invoices', 'display_ar' => 'فواتير المبيعات'],
            ['key' => 'sidebar_sales_returns', 'display_en' => 'Sales Returns', 'display_ar' => 'مرتجعات المبيعات'],
            ['key' => 'sidebar_commissions', 'display_en' => 'Commissions', 'display_ar' => 'العمولات'],
            ['key' => 'sidebar_customer_registrations', 'display_en' => 'Customer Registrations', 'display_ar' => 'تسجيلات العملاء'],

            // Purchases Options
            ['key' => 'sidebar_vendors', 'display_en' => 'Vendors', 'display_ar' => 'الموردين'],
            ['key' => 'sidebar_supply_orders', 'display_en' => 'Supply Orders', 'display_ar' => 'أوامر التوريد'],
            ['key' => 'sidebar_purchase_invoices', 'display_en' => 'Purchase Invoices', 'display_ar' => 'فواتير المشتريات'],
            ['key' => 'sidebar_local_purchases', 'display_en' => 'Local Purchases', 'display_ar' => 'المشتريات المحلية'],
            ['key' => 'sidebar_supplier_registrations', 'display_en' => 'Supplier Registrations', 'display_ar' => 'تسجيلات الموردين'],

            // Inventory Options
            ['key' => 'sidebar_products', 'display_en' => 'Products Management', 'display_ar' => 'إدارة المنتجات'],
            ['key' => 'sidebar_stock_management', 'display_en' => 'Stock Management', 'display_ar' => 'إدارة المخزون'],
            ['key' => 'sidebar_stock_ledger', 'display_en' => 'Stock Ledger', 'display_ar' => 'دفتر المخزون'],

            // HR Options
            ['key' => 'sidebar_employees', 'display_en' => 'Employees', 'display_ar' => 'الموظفين'],
            ['key' => 'sidebar_departments', 'display_en' => 'Departments', 'display_ar' => 'الأقسام'],
            ['key' => 'sidebar_designations', 'display_en' => 'Designations', 'display_ar' => 'المسميات الوظيفية'],
            ['key' => 'sidebar_salaries', 'display_en' => 'Salaries', 'display_ar' => 'الرواتب'],
            ['key' => 'sidebar_experience_letters', 'display_en' => 'Experience Letters', 'display_ar' => 'شهادات الخبرة'],

            // Transport Options
            ['key' => 'sidebar_trailers', 'display_en' => 'Trailers', 'display_ar' => 'المقطورات'],
            ['key' => 'sidebar_transport_orders', 'display_en' => 'Transport Orders', 'display_ar' => 'أوامر النقل'],
            ['key' => 'sidebar_transport_contracts', 'display_en' => 'Transport Contracts', 'display_ar' => 'عقود النقل'],
            ['key' => 'sidebar_transport_claims', 'display_en' => 'Transport Claims', 'display_ar' => 'مطالبات النقل'],

            // Maintenance Options
            ['key' => 'sidebar_workshops', 'display_en' => 'Workshops', 'display_ar' => 'الورش'],
            ['key' => 'sidebar_maintenance_vouchers', 'display_en' => 'Maintenance Vouchers', 'display_ar' => 'سندات الصيانة'],

            // Reports Options
            ['key' => 'sidebar_sales_reports', 'display_en' => 'Sales Reports', 'display_ar' => 'تقارير المبيعات'],
            ['key' => 'sidebar_supplier_reports', 'display_en' => 'Supplier Reports', 'display_ar' => 'تقارير الموردين'],
            ['key' => 'sidebar_tax_reports', 'display_en' => 'Tax Reports', 'display_ar' => 'تقارير الضرائب'],
            ['key' => 'sidebar_inventory_reports', 'display_en' => 'Inventory Reports', 'display_ar' => 'تقارير المخزون'],
        ];

        foreach ($settings as $setting) {
            DB::table('system_settings')->insertOrIgnore([
                'key' => $setting['key'],
                'value' => '1', // Default to visible
                'type' => 'boolean',
                'group' => 'module_visibility',
                'display_name_en' => $setting['display_en'],
                'display_name_ar' => $setting['display_ar'],
                'is_editable' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('system_settings')->where('group', 'module_visibility')->delete();
    }
};
