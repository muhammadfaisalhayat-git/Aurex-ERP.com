<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@aurex.com',
                'password' => Hash::make('password'),
                'phone' => '+966 50 000 0001',
                'employee_code' => 'EMP-001',
                'branch_id' => 1,
                'default_language' => 'en',
                'is_active' => true,
                'role' => 'Super Admin',
                'warehouses' => [1, 2, 3, 4],
            ],
            [
                'name' => 'System Admin',
                'email' => 'admin@aurex.com',
                'password' => Hash::make('password'),
                'phone' => '+966 50 000 0002',
                'employee_code' => 'EMP-002',
                'branch_id' => 1,
                'default_language' => 'en',
                'is_active' => true,
                'role' => 'Admin',
                'warehouses' => [1, 2],
            ],
            [
                'name' => 'Ahmed Accountant',
                'email' => 'accountant@aurex.com',
                'password' => Hash::make('password'),
                'phone' => '+966 50 000 0003',
                'employee_code' => 'EMP-003',
                'branch_id' => 1,
                'default_language' => 'en',
                'is_active' => true,
                'role' => 'Accountant',
                'warehouses' => [1],
            ],
            [
                'name' => 'Khalid Sales Manager',
                'email' => 'sales.manager@aurex.com',
                'password' => Hash::make('password'),
                'phone' => '+966 50 000 0004',
                'employee_code' => 'EMP-004',
                'branch_id' => 1,
                'default_language' => 'en',
                'is_active' => true,
                'role' => 'Sales Manager',
                'warehouses' => [1, 2],
            ],
            [
                'name' => 'Omar Salesman',
                'email' => 'sales@aurex.com',
                'password' => Hash::make('password'),
                'phone' => '+966 50 000 0005',
                'employee_code' => 'EMP-005',
                'branch_id' => 1,
                'default_language' => 'en',
                'is_active' => true,
                'role' => 'Salesman',
                'warehouses' => [1],
            ],
            [
                'name' => 'Fatima Data Analyst',
                'email' => 'analyst@aurex.com',
                'password' => Hash::make('password'),
                'phone' => '+966 50 000 0006',
                'employee_code' => 'EMP-006',
                'branch_id' => 1,
                'default_language' => 'en',
                'is_active' => true,
                'role' => 'Data Analyst',
                'warehouses' => [1, 2, 3, 4],
            ],
            [
                'name' => 'Sara Data Entry',
                'email' => 'dataentry@aurex.com',
                'password' => Hash::make('password'),
                'phone' => '+966 50 000 0007',
                'employee_code' => 'EMP-007',
                'branch_id' => 1,
                'default_language' => 'en',
                'is_active' => true,
                'role' => 'Data Entry',
                'warehouses' => [1],
            ],
            [
                'name' => 'Mohammed Inventory Manager',
                'email' => 'inventory@aurex.com',
                'password' => Hash::make('password'),
                'phone' => '+966 50 000 0008',
                'employee_code' => 'EMP-008',
                'branch_id' => 1,
                'default_language' => 'en',
                'is_active' => true,
                'role' => 'Inventory Manager',
                'warehouses' => [1, 2],
            ],
            [
                'name' => 'Abdullah Warehouse',
                'email' => 'warehouse@aurex.com',
                'password' => Hash::make('password'),
                'phone' => '+966 50 000 0009',
                'employee_code' => 'EMP-009',
                'branch_id' => 1,
                'default_language' => 'en',
                'is_active' => true,
                'role' => 'Warehouse User',
                'warehouses' => [1],
            ],
        ];

        foreach ($users as $userData) {
            $warehouses = $userData['warehouses'] ?? [];
            unset($userData['warehouses']);
            $role = $userData['role'];
            unset($userData['role']);

            $user = User::updateOrCreate(['email' => $userData['email']], $userData);
            $user->syncRoles([$role]);

            if (!empty($warehouses)) {
                $user->warehouses()->sync($warehouses);
            }
        }
    }
}
