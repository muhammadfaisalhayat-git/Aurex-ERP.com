<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

return new class extends Migration {
    public function up(): void
    {
        $permissions = [
            // HR Management
            ['name' => 'view employees', 'display_name_en' => 'View Employees', 'display_name_ar' => 'عرض الموظفين', 'module' => 'hr'],
            ['name' => 'create employees', 'display_name_en' => 'Create Employees', 'display_name_ar' => 'إنشاء الموظفين', 'module' => 'hr'],
            ['name' => 'edit employees', 'display_name_en' => 'Edit Employees', 'display_name_ar' => 'تعديل الموظفين', 'module' => 'hr'],
            ['name' => 'delete employees', 'display_name_en' => 'Delete Employees', 'display_name_ar' => 'حذف الموظفين', 'module' => 'hr'],

            ['name' => 'view departments', 'display_name_en' => 'View Departments', 'display_name_ar' => 'عرض الأقسام', 'module' => 'hr'],
            ['name' => 'manage departments', 'display_name_en' => 'Manage Departments', 'display_name_ar' => 'إدارة الأقسام', 'module' => 'hr'],

            ['name' => 'view designations', 'display_name_en' => 'View Designations', 'display_name_ar' => 'عرض المسميات الوظيفية', 'module' => 'hr'],
            ['name' => 'manage designations', 'display_name_en' => 'Manage Designations', 'display_name_ar' => 'إدارة المسميات الوظيفية', 'module' => 'hr'],
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

        // Create HR Manager Role
        $hrManager = Role::firstOrCreate(
            ['name' => 'HR Manager', 'guard_name' => 'web'],
            [
                'display_name_en' => 'HR Manager',
                'display_name_ar' => 'مدير الموارد البشرية',
                'is_system' => false,
            ]
        );

        // Assign HR permissions to HR Manager
        $hrManager->syncPermissions(Permission::where('module', 'hr')->get());

        // Also assign HR permissions to Super Admin and Admin if they exist
        $superAdmin = Role::where('name', 'Super Admin')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo(Permission::where('module', 'hr')->get());
        }

        $admin = Role::where('name', 'Admin')->first();
        if ($admin) {
            $admin->givePermissionTo(Permission::where('module', 'hr')->get());
        }
    }

    public function down(): void
    {
        // We don't necessarily want to delete roles/permissions on rollback as it might be destructive
        // but for completeness:
        // Role::where('name', 'HR Manager')->delete();
        // Permission::where('module', 'hr')->delete();
    }
};
