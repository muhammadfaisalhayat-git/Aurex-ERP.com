<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration {
    public function up(): void
    {
        $permissions = [
            'view accounting',
            'manage chart of accounts',
            'view journal vouchers',
            'create journal vouchers',
            'edit journal vouchers',
            'delete journal vouchers',
            'post journal vouchers',
            'reverse journal vouchers',
            'view accounting reports',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        // Roles
        $accountant = Role::findOrCreate('Accountant', 'web');
        $accountant->givePermissionTo([
            'view accounting',
            'manage chart of accounts',
            'view journal vouchers',
            'create journal vouchers',
            'edit journal vouchers',
            'post journal vouchers',
            'reverse journal vouchers',
            'view accounting reports',
        ]);

        $dataEntry = Role::findOrCreate('Data Entry', 'web');
        $dataEntry->givePermissionTo([
            'view accounting',
            'view journal vouchers',
            'create journal vouchers',
            'edit journal vouchers',
        ]);

        $dataAnalyst = Role::findOrCreate('Data Analyst', 'web');
        $dataAnalyst->givePermissionTo([
            'view accounting',
            'view accounting reports',
        ]);

        $superAdmin = Role::where('name', 'Super Admin')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo($permissions);
        }
    }

    public function down(): void
    {
        // Not strictly necessary as roles/permissions usually stay
    }
};
