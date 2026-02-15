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
        $superAdmin = Role::create([
            'name' => 'Super Admin',
            'display_name_en' => 'Super Administrator',
            'display_name_ar' => 'مدير النظام',
            'guard_name' => 'web',
            'is_system' => true,
        ]);
        $superAdmin->syncPermissions(Permission::all());

        // Admin
        $admin = Role::create([
            'name' => 'Admin',
            'display_name_en' => 'Administrator',
            'display_name_ar' => 'مدير',
            'guard_name' => 'web',
            'is_system' => true,
        ]);
        $admin->syncPermissions(Permission::whereNotIn('name', [
            'manage roles',
            'delete users'
        ])->get());

        // Accountant
        $accountant = Role::create([
            'name' => 'Accountant',
            'display_name_en' => 'Accountant',
            'display_name_ar' => 'محاسب',
            'guard_name' => 'web',
            'is_system' => false,
        ]);
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
        ])->get());

        // Sales Manager
        $salesManager = Role::create([
            'name' => 'Sales Manager',
            'display_name_en' => 'Sales Manager',
            'display_name_ar' => 'مدير المبيعات',
            'guard_name' => 'web',
            'is_system' => false,
        ]);
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
        $salesman = Role::create([
            'name' => 'Salesman',
            'display_name_en' => 'Salesman',
            'display_name_ar' => 'مندوب مبيعات',
            'guard_name' => 'web',
            'is_system' => false,
        ]);
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
        $dataAnalyst = Role::create([
            'name' => 'Data Analyst',
            'display_name_en' => 'Data Analyst',
            'display_name_ar' => 'محلل بيانات',
            'guard_name' => 'web',
            'is_system' => false,
        ]);
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
        $dataEntry = Role::create([
            'name' => 'Data Entry',
            'display_name_en' => 'Data Entry',
            'display_name_ar' => 'مدخل بيانات',
            'guard_name' => 'web',
            'is_system' => false,
        ]);
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
        $inventoryManager = Role::create([
            'name' => 'Inventory Manager',
            'display_name_en' => 'Inventory Manager',
            'display_name_ar' => 'مدير المخزون',
            'guard_name' => 'web',
            'is_system' => false,
        ]);
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
        $warehouseUser = Role::create([
            'name' => 'Warehouse User',
            'display_name_en' => 'Warehouse User',
            'display_name_ar' => 'مستخدم المستودع',
            'guard_name' => 'web',
            'is_system' => false,
        ]);
        $warehouseUser->syncPermissions(Permission::whereIn('name', [
            'view products',
            'view inventory',
            'manage stock',
        ])->get());

        // HR Manager
        $hrManager = Role::create([
            'name' => 'HR Manager',
            'display_name_en' => 'HR Manager',
            'display_name_ar' => 'مدير الموارد البشرية',
            'guard_name' => 'web',
            'is_system' => false,
        ]);
        $hrManager->syncPermissions(Permission::where('module', 'hr')->get());
    }
}
