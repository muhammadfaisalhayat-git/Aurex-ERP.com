<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class HRRoleSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure HR Manager role exists
        Role::firstOrCreate(
            ['name' => 'HR Manager'],
            [
                'display_name_en' => 'HR Manager',
                'display_name_ar' => 'مدير الموارد البشرية',
                'guard_name' => 'web',
                'is_system' => false,
            ]
        );

        // Optional: Sync HR permissions to HR Manager
        $hrManager = Role::where('name', 'HR Manager')->first();
        $hrPermissions = Permission::where('module', 'hr')->get();
        if ($hrPermissions->count() > 0) {
            $hrManager->syncPermissions($hrPermissions);
        }
    }
}
