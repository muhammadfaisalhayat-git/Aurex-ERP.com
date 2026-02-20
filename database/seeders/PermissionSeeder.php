<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // User Management
            ['name' => 'view users', 'display_name_en' => 'View Users', 'display_name_ar' => 'عرض المستخدمين', 'module' => 'user_management'],
            ['name' => 'create users', 'display_name_en' => 'Create Users', 'display_name_ar' => 'إنشاء المستخدمين', 'module' => 'user_management'],
            ['name' => 'edit users', 'display_name_en' => 'Edit Users', 'display_name_ar' => 'تعديل المستخدمين', 'module' => 'user_management'],
            ['name' => 'delete users', 'display_name_en' => 'Delete Users', 'display_name_ar' => 'حذف المستخدمين', 'module' => 'user_management'],
            ['name' => 'manage roles', 'display_name_en' => 'Manage Roles', 'display_name_ar' => 'إدارة الأدوار', 'module' => 'user_management'],

            // Sales
            ['name' => 'view customers', 'display_name_en' => 'View Customers', 'display_name_ar' => 'عرض العملاء', 'module' => 'sales'],
            ['name' => 'create customers', 'display_name_en' => 'Create Customers', 'display_name_ar' => 'إنشاء العملاء', 'module' => 'sales'],
            ['name' => 'edit customers', 'display_name_en' => 'Edit Customers', 'display_name_ar' => 'تعديل العملاء', 'module' => 'sales'],
            ['name' => 'delete customers', 'display_name_en' => 'Delete Customers', 'display_name_ar' => 'حذف العملاء', 'module' => 'sales'],

            ['name' => 'view quotations', 'display_name_en' => 'View Quotations', 'display_name_ar' => 'عرض عروض الأسعار', 'module' => 'sales'],
            ['name' => 'create quotations', 'display_name_en' => 'Create Quotations', 'display_name_ar' => 'إنشاء عروض الأسعار', 'module' => 'sales'],
            ['name' => 'edit quotations', 'display_name_en' => 'Edit Quotations', 'display_name_ar' => 'تعديل عروض الأسعار', 'module' => 'sales'],
            ['name' => 'delete quotations', 'display_name_en' => 'Delete Quotations', 'display_name_ar' => 'حذف عروض الأسعار', 'module' => 'sales'],

            ['name' => 'view invoices', 'display_name_en' => 'View Invoices', 'display_name_ar' => 'عرض الفواتير', 'module' => 'sales'],
            ['name' => 'create invoices', 'display_name_en' => 'Create Invoices', 'display_name_ar' => 'إنشاء الفواتير', 'module' => 'sales'],
            ['name' => 'edit invoices', 'display_name_en' => 'Edit Invoices', 'display_name_ar' => 'تعديل الفواتير', 'module' => 'sales'],
            ['name' => 'delete invoices', 'display_name_en' => 'Delete Invoices', 'display_name_ar' => 'حذف الفواتير', 'module' => 'sales'],
            ['name' => 'post invoices', 'display_name_en' => 'Post/Unpost Invoices', 'display_name_ar' => 'ترحيل/إلغاء ترحيل الفواتير', 'module' => 'sales'],

            ['name' => 'view returns', 'display_name_en' => 'View Returns', 'display_name_ar' => 'عرض المرتجعات', 'module' => 'sales'],
            ['name' => 'create returns', 'display_name_en' => 'Create Returns', 'display_name_ar' => 'إنشاء المرتجعات', 'module' => 'sales'],
            ['name' => 'edit returns', 'display_name_en' => 'Edit Returns', 'display_name_ar' => 'تعديل المرتجعات', 'module' => 'sales'],
            ['name' => 'post returns', 'display_name_en' => 'Post Returns', 'display_name_ar' => 'ترحيل المرتجعات', 'module' => 'sales'],

            ['name' => 'view commissions', 'display_name_en' => 'View Commissions', 'display_name_ar' => 'عرض العمولات', 'module' => 'sales'],
            ['name' => 'manage commissions', 'display_name_en' => 'Manage Commissions', 'display_name_ar' => 'إدارة العمولات', 'module' => 'sales'],
            ['name' => 'approve commissions', 'display_name_en' => 'Approve Commissions', 'display_name_ar' => 'اعتماد العمولات', 'module' => 'sales'],

            // Purchases
            ['name' => 'view vendors', 'display_name_en' => 'View Vendors', 'display_name_ar' => 'عرض الموردين', 'module' => 'purchases'],
            ['name' => 'create vendors', 'display_name_en' => 'Create Vendors', 'display_name_ar' => 'إنشاء الموردين', 'module' => 'purchases'],
            ['name' => 'edit vendors', 'display_name_en' => 'Edit Vendors', 'display_name_ar' => 'تعديل الموردين', 'module' => 'purchases'],
            ['name' => 'delete vendors', 'display_name_en' => 'Delete Vendors', 'display_name_ar' => 'حذف الموردين', 'module' => 'purchases'],

            ['name' => 'view purchases', 'display_name_en' => 'View Purchases', 'display_name_ar' => 'عرض المشتريات', 'module' => 'purchases'],
            ['name' => 'create purchases', 'display_name_en' => 'Create Purchases', 'display_name_ar' => 'إنشاء المشتريات', 'module' => 'purchases'],
            ['name' => 'edit purchases', 'display_name_en' => 'Edit Purchases', 'display_name_ar' => 'تعديل المشتريات', 'module' => 'purchases'],
            ['name' => 'delete purchases', 'display_name_en' => 'Delete Purchases', 'display_name_ar' => 'حذف المشتريات', 'module' => 'purchases'],
            ['name' => 'post purchases', 'display_name_en' => 'Post Purchases', 'display_name_ar' => 'ترحيل المشتريات', 'module' => 'purchases'],

            // Local Purchases
            ['name' => 'view local_purchase', 'display_name_en' => 'View Local Purchases', 'display_name_ar' => 'عرض المشتريات المحلية', 'module' => 'purchases'],
            ['name' => 'create local_purchase', 'display_name_en' => 'Create Local Purchases', 'display_name_ar' => 'إنشاء المشتريات المحلية', 'module' => 'purchases'],
            ['name' => 'edit local_purchase', 'display_name_en' => 'Edit Local Purchases', 'display_name_ar' => 'تعديل المشتريات المحلية', 'module' => 'purchases'],
            ['name' => 'delete local_purchase', 'display_name_en' => 'Delete Local Purchases', 'display_name_ar' => 'حذف المشتريات المحلية', 'module' => 'purchases'],
            ['name' => 'post local_purchase', 'display_name_en' => 'Post Local Purchases', 'display_name_ar' => 'ترحيل المشتريات المحلية', 'module' => 'purchases'],

            // Supplier Registrations
            ['name' => 'view supplier_registration', 'display_name_en' => 'View Supplier Registrations', 'display_name_ar' => 'عرض تسجيلات الموردين', 'module' => 'purchases'],
            ['name' => 'create supplier_registration', 'display_name_en' => 'Create Supplier Registrations', 'display_name_ar' => 'إنشاء تسجيلات الموردين', 'module' => 'purchases'],
            ['name' => 'edit supplier_registration', 'display_name_en' => 'Edit Supplier Registrations', 'display_name_ar' => 'تعديل تسجيلات الموردين', 'module' => 'purchases'],
            ['name' => 'delete supplier_registration', 'display_name_en' => 'Delete Supplier Registrations', 'display_name_ar' => 'حذف تسجيلات الموردين', 'module' => 'purchases'],
            ['name' => 'approve supplier_registration', 'display_name_en' => 'Approve Supplier Registrations', 'display_name_ar' => 'اعتماد تسجيلات الموردين', 'module' => 'purchases'],

            // Customer Registrations
            ['name' => 'view customer_registration', 'display_name_en' => 'View Customer Registrations', 'display_name_ar' => 'عرض تسجيلات العملاء', 'module' => 'sales'],
            ['name' => 'create customer_registration', 'display_name_en' => 'Create Customer Registrations', 'display_name_ar' => 'إنشاء تسجيلات العملاء', 'module' => 'sales'],
            ['name' => 'edit customer_registration', 'display_name_en' => 'Edit Customer Registrations', 'display_name_ar' => 'تعديل تسجيلات العملاء', 'module' => 'sales'],
            ['name' => 'delete customer_registration', 'display_name_en' => 'Delete Customer Registrations', 'display_name_ar' => 'حذف تسجيلات العملاء', 'module' => 'sales'],
            ['name' => 'approve customer_registration', 'display_name_en' => 'Approve Customer Registrations', 'display_name_ar' => 'اعتماد تسجيلات العملاء', 'module' => 'sales'],

            // Inventory
            ['name' => 'view products', 'display_name_en' => 'View Products', 'display_name_ar' => 'عرض المنتجات', 'module' => 'inventory'],
            ['name' => 'create products', 'display_name_en' => 'Create Products', 'display_name_ar' => 'إنشاء المنتجات', 'module' => 'inventory'],
            ['name' => 'edit products', 'display_name_en' => 'Edit Products', 'display_name_ar' => 'تعديل المنتجات', 'module' => 'inventory'],
            ['name' => 'delete products', 'display_name_en' => 'Delete Products', 'display_name_ar' => 'حذف المنتجات', 'module' => 'inventory'],

            ['name' => 'view inventory', 'display_name_en' => 'View Inventory', 'display_name_ar' => 'عرض المخزون', 'module' => 'inventory'],
            ['name' => 'manage stock', 'display_name_en' => 'Manage Stock', 'display_name_ar' => 'إدارة المخزون', 'module' => 'inventory'],
            ['name' => 'post inventory', 'display_name_en' => 'Post Inventory', 'display_name_ar' => 'ترحيل المخزون', 'module' => 'inventory'],
            ['name' => 'approve transfers', 'display_name_en' => 'Approve Transfers', 'display_name_ar' => 'اعتماد التحويلات', 'module' => 'inventory'],

            // Transport
            ['name' => 'view transport', 'display_name_en' => 'View Transport', 'display_name_ar' => 'عرض النقل', 'module' => 'transport'],
            ['name' => 'manage transport', 'display_name_en' => 'Manage Transport', 'display_name_ar' => 'إدارة النقل', 'module' => 'transport'],
            ['name' => 'close transport', 'display_name_en' => 'Close Transport Orders', 'display_name_ar' => 'إغلاق أوامر النقل', 'module' => 'transport'],
            ['name' => 'manage claims', 'display_name_en' => 'Manage Claims', 'display_name_ar' => 'إدارة المطالبات', 'module' => 'transport'],

            // Production
            ['name' => 'view production', 'display_name_en' => 'View Production', 'display_name_ar' => 'عرض الإنتاج', 'module' => 'production'],
            ['name' => 'manage production', 'display_name_en' => 'Manage Production', 'display_name_ar' => 'إدارة الإنتاج', 'module' => 'production'],
            ['name' => 'create production_orders', 'display_name_en' => 'Create Production Orders', 'display_name_ar' => 'إنشاء أوامر الإنتاج', 'module' => 'production'],
            ['name' => 'post production_orders', 'display_name_en' => 'Post Production Orders', 'display_name_ar' => 'ترحيل أوامر الإنتاج', 'module' => 'production'],

            // Logistics (Enhanced)
            ['name' => 'view logistics', 'display_name_en' => 'View Logistics', 'display_name_ar' => 'عرض اللوجستيات', 'module' => 'logistics'],
            ['name' => 'manage fleet', 'display_name_en' => 'Manage Fleet', 'display_name_ar' => 'إدارة الأسطول', 'module' => 'logistics'],
            ['name' => 'record fuel_logs', 'display_name_en' => 'Record Fuel Logs', 'display_name_ar' => 'تسجيل سجلات الوقود', 'module' => 'logistics'],

            // Maintenance
            ['name' => 'view maintenance', 'display_name_en' => 'View Maintenance', 'display_name_ar' => 'عرض الصيانة', 'module' => 'maintenance'],
            ['name' => 'manage maintenance', 'display_name_en' => 'Manage Maintenance', 'display_name_ar' => 'إدارة الصيانة', 'module' => 'maintenance'],

            // Reports
            ['name' => 'view reports', 'display_name_en' => 'View Reports', 'display_name_ar' => 'عرض التقارير', 'module' => 'reports'],
            ['name' => 'export data', 'display_name_en' => 'Export Data', 'display_name_ar' => 'تصدير البيانات', 'module' => 'reports'],

            // HR Management
            ['name' => 'view employees', 'display_name_en' => 'View Employees', 'display_name_ar' => 'عرض الموظفين', 'module' => 'hr'],
            ['name' => 'create employees', 'display_name_en' => 'Create Employees', 'display_name_ar' => 'إنشاء الموظفين', 'module' => 'hr'],
            ['name' => 'edit employees', 'display_name_en' => 'Edit Employees', 'display_name_ar' => 'تعديل الموظفين', 'module' => 'hr'],
            ['name' => 'delete employees', 'display_name_en' => 'Delete Employees', 'display_name_ar' => 'حذف الموظفين', 'module' => 'hr'],
            ['name' => 'view departments', 'display_name_en' => 'View Departments', 'display_name_ar' => 'عرض الأقسام', 'module' => 'hr'],
            ['name' => 'manage departments', 'display_name_en' => 'Manage Departments', 'display_name_ar' => 'إدارة الأقسام', 'module' => 'hr'],
            ['name' => 'view designations', 'display_name_en' => 'View Designations', 'display_name_ar' => 'عرض المسميات الوظيفية', 'module' => 'hr'],
            ['name' => 'manage designations', 'display_name_en' => 'Manage Designations', 'display_name_ar' => 'إدارة المسميات الوظيفية', 'module' => 'hr'],

            // Settings
            ['name' => 'manage settings', 'display_name_en' => 'Manage Settings', 'display_name_ar' => 'إدارة الإعدادات', 'module' => 'settings'],
            // Accounting
            ['name' => 'view accounting', 'display_name_en' => 'View Accounting', 'display_name_ar' => 'عرض المحاسبة', 'module' => 'accounting'],
            ['name' => 'view journal_vouchers', 'display_name_en' => 'View Journal Vouchers', 'display_name_ar' => 'عرض قيود اليومية', 'module' => 'accounting'],
            ['name' => 'create journal_vouchers', 'display_name_en' => 'Create Journal Vouchers', 'display_name_ar' => 'إنشاء قيود اليومية', 'module' => 'accounting'],
            ['name' => 'edit journal_vouchers', 'display_name_en' => 'Edit Journal Vouchers', 'display_name_ar' => 'تعديل قيود اليومية', 'module' => 'accounting'],
            ['name' => 'delete journal_vouchers', 'display_name_en' => 'Delete Journal Vouchers', 'display_name_ar' => 'حذف قيود اليومية', 'module' => 'accounting'],
            ['name' => 'view chart_of_accounts', 'display_name_en' => 'View Chart of Accounts', 'display_name_ar' => 'عرض شجرة الحسابات', 'module' => 'accounting'],
            ['name' => 'create chart_of_accounts', 'display_name_en' => 'Create Chart of Accounts', 'display_name_ar' => 'إنشاء شجرة الحسابات', 'module' => 'accounting'],
            ['name' => 'edit chart_of_accounts', 'display_name_en' => 'Edit Chart of Accounts', 'display_name_ar' => 'تعديل شجرة الحسابات', 'module' => 'accounting'],
            ['name' => 'delete chart_of_accounts', 'display_name_en' => 'Delete Chart of Accounts', 'display_name_ar' => 'حذف شجرة الحسابات', 'module' => 'accounting'],
            ['name' => 'view ledger', 'display_name_en' => 'View Ledger', 'display_name_ar' => 'عرض دفتر الأستاذ', 'module' => 'accounting'],
            ['name' => 'edit posted journal vouchers', 'display_name_en' => 'Edit Posted Journal Vouchers', 'display_name_ar' => 'تعديل قيود اليومية المرحلة', 'module' => 'accounting'],

            // Accounting Setup
            ['name' => 'view cost_centers', 'display_name_en' => 'View Cost Centers', 'display_name_ar' => 'عرض مراكز التكلفة', 'module' => 'accounting'],
            ['name' => 'create cost_centers', 'display_name_en' => 'Create Cost Centers', 'display_name_ar' => 'إنشاء مراكز التكلفة', 'module' => 'accounting'],
            ['name' => 'edit cost_centers', 'display_name_en' => 'Edit Cost Centers', 'display_name_ar' => 'تعديل مراكز التكلفة', 'module' => 'accounting'],
            ['name' => 'delete cost_centers', 'display_name_en' => 'Delete Cost Centers', 'display_name_ar' => 'حذف مراكز التكلفة', 'module' => 'accounting'],

            ['name' => 'view activities', 'display_name_en' => 'View Activities', 'display_name_ar' => 'عرض الأنشطة', 'module' => 'accounting'],
            ['name' => 'create activities', 'display_name_en' => 'Create Activities', 'display_name_ar' => 'إنشاء الأنشطة', 'module' => 'accounting'],
            ['name' => 'edit activities', 'display_name_en' => 'Edit Activities', 'display_name_ar' => 'تعديل الأنشطة', 'module' => 'accounting'],
            ['name' => 'delete activities', 'display_name_en' => 'Delete Activities', 'display_name_ar' => 'حذف الأنشطة', 'module' => 'accounting'],

            ['name' => 'view lcs', 'display_name_en' => 'View LCs', 'display_name_ar' => 'عرض الاعتمادات المستندية', 'module' => 'accounting'],
            ['name' => 'create lcs', 'display_name_en' => 'Create LCs', 'display_name_ar' => 'إنشاء الاعتمادات المستندية', 'module' => 'accounting'],
            ['name' => 'edit lcs', 'display_name_en' => 'Edit LCs', 'display_name_ar' => 'تعديل الاعتمادات المستندية', 'module' => 'accounting'],
            ['name' => 'delete lcs', 'display_name_en' => 'Delete LCs', 'display_name_ar' => 'حذف الاعتمادات المستندية', 'module' => 'accounting'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
            ['name' => $permission['name'], 'guard_name' => 'web'],
            [
                'display_name_en' => $permission['display_name_en'],
                'display_name_ar' => $permission['display_name_ar'],
                'module' => $permission['module'],
            ]
            );
        }
    }
}
