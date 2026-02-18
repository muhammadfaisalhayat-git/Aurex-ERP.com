<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin - has all permissions
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web'], [
            'display_name_en' => 'Super Administrator',
            'display_name_ar' => 'مدير النظام',
            'is_system' => true,
        ]);

        $admin = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web'], [
            'display_name_en' => 'Administrator',
            'display_name_ar' => 'مدير',
            'is_system' => true,
        ]);

        $accountant = Role::firstOrCreate(['name' => 'Accountant', 'guard_name' => 'web'], [
            'display_name_en' => 'Accountant',
            'display_name_ar' => 'محاسب',
            'is_system' => false,
        ]);

        $salesManager = Role::firstOrCreate(['name' => 'Sales Manager', 'guard_name' => 'web'], [
            'display_name_en' => 'Sales Manager',
            'display_name_ar' => 'مدير المبيعات',
            'is_system' => false,
        ]);

        $salesman = Role::firstOrCreate(['name' => 'Salesman', 'guard_name' => 'web'], [
            'display_name_en' => 'Salesman',
            'display_name_ar' => 'مندوب مبيعات',
            'is_system' => false,
        ]);

        $dataAnalyst = Role::firstOrCreate(['name' => 'Data Analyst', 'guard_name' => 'web'], [
            'display_name_en' => 'Data Analyst',
            'display_name_ar' => 'محلل بيانات',
            'is_system' => false,
        ]);

        $dataEntry = Role::firstOrCreate(['name' => 'Data Entry', 'guard_name' => 'web'], [
            'display_name_en' => 'Data Entry',
            'display_name_ar' => 'مدخل بيانات',
            'is_system' => false,
        ]);

        $inventoryManager = Role::firstOrCreate(['name' => 'Inventory Manager', 'guard_name' => 'web'], [
            'display_name_en' => 'Inventory Manager',
            'display_name_ar' => 'مدير المخزون',
            'is_system' => false,
        ]);

        $warehouseUser = Role::firstOrCreate(['name' => 'Warehouse User', 'guard_name' => 'web'], [
            'display_name_en' => 'Warehouse User',
            'display_name_ar' => 'مستخدم المستودع',
            'is_system' => false,
        ]);

        $hrManager = Role::firstOrCreate(['name' => 'HR Manager', 'guard_name' => 'web'], [
            'display_name_en' => 'HR Manager',
            'display_name_ar' => 'مدير الموارد البشرية',
            'is_system' => false,
        ]);
        $superAdmin->syncPermissions(Permission::all());

        // Admin

        $admin->syncPermissions(Permission::whereNotIn('name', [
            'manage roles',
            'delete users'
        ])->get());

        // Accountant

        $accountant->syncPermissions(Permission::whereIn('name', [
            'view customers',
            'view vendors',
            'view invoices',
            'view purchases',
            'view returns',
            'view inventory',
            'view reports',
            'export data',
            'post invoices',
            'post purchases',
            'post returns',
            'post inventory',
            'create invoices',
            'edit invoices',
            'create purchases',
            'edit purchases',
            'view accounting',
            'view journal_vouchers',
            'create journal_vouchers',
            'edit journal_vouchers',
            'delete journal_vouchers',
            'view chart_of_accounts',
            'create chart_of_accounts',
            'edit chart_of_accounts',
            'delete chart_of_accounts',
            'view ledger',
        ])->get());

        // Sales Manager

        $salesManager->syncPermissions(Permission::whereIn('name', [
            'view customers',
            'create customers',
            'edit customers',
            'view quotations',
            'create quotations',
            'edit quotations',
            'delete quotations',
            'view invoices',
            'create invoices',
            'edit invoices',
            'view returns',
            'create returns',
            'edit returns',
            'view commissions',
            'manage commissions',
            'approve commissions',
            'view reports',
            'export data',
            'post invoices',
            'post returns',
        ])->get());

        // Salesman

        $salesman->syncPermissions(Permission::whereIn('name', [
            'view customers',
            'create customers',
            'edit customers',
            'view quotations',
            'create quotations',
            'edit quotations',
            'view invoices',
            'create invoices',
            'view returns',
        ])->get());

        // Data Analyst

        $dataAnalyst->syncPermissions(Permission::whereIn('name', [
            'view customers',
            'view vendors',
            'view products',
            'view invoices',
            'view purchases',
            'view returns',
            'view inventory',
            'view transport',
            'view maintenance',
            'view reports',
            'export data',
        ])->get());

        // Data Entry

        $dataEntry->syncPermissions(Permission::whereIn('name', [
            'view customers',
            'create customers',
            'edit customers',
            'view vendors',
            'create vendors',
            'edit vendors',
            'view products',
            'create products',
            'edit products',
            'view quotations',
            'create quotations',
            'view invoices',
            'create invoices',
        ])->get());

        // Inventory Manager

        $inventoryManager->syncPermissions(Permission::whereIn('name', [
            'view products',
            'create products',
            'edit products',
            'delete products',
            'view inventory',
            'manage stock',
            'post inventory',
            'approve transfers',
            'view reports',
            'export data',
        ])->get());

        // Warehouse User

        $warehouseUser->syncPermissions(Permission::whereIn('name', [
            'view products',
            'view inventory',
            'manage stock',
        ])->get());

        // HR Manager

        $hrManager->syncPermissions(Permission::where('module', 'hr')->get());
    }
}
