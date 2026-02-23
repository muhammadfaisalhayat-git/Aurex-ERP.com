<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\Branch;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class MultiTenantSeeder extends Seeder
{
    public function run(): void
    {
        // 0. Ensure Roles Exist
        $roles = [
            'Super Admin' => 'Super Administrator',
            'Company Admin' => 'Company Administrator',
            'Branch Manager' => 'Branch Manager',
        ];

        foreach ($roles as $name => $displayName) {
            Role::firstOrCreate(
            ['name' => $name],
            [
                'display_name_en' => $displayName,
                'guard_name' => 'web',
            ]
            );
        }

        // 1. Create Companies
        $companyA = Company::updateOrCreate(
        ['registration_number' => 'REG-001'],
        [
            'name_en' => 'BIN AWF AGRICULTURAL',
            'name_ar' => 'بن عوف الزراعية',
            'tax_number' => 'TAX-001',
            'currency' => 'SAR',
            'is_active' => true,
        ]
        );

        $companyB = Company::updateOrCreate(
        ['registration_number' => 'REG-002'],
        [
            'name_en' => 'Tech Solutions B',
            'name_ar' => 'حلول التقنية ب',
            'tax_number' => 'TAX-002',
            'currency' => 'SAR',
            'is_active' => true,
        ]
        );

        // 2. Create Branches for Company A
        $branchA1 = Branch::updateOrCreate(
        ['code' => 'NY-01'],
        [
            'company_id' => $companyA->id,
            'name_en' => 'New York Branch',
            'name_ar' => 'فرع نيويورك',
            'is_active' => true,
        ]
        );

        $branchA2 = Branch::updateOrCreate(
        ['code' => 'LDN-01'],
        [
            'company_id' => $companyA->id,
            'name_en' => 'London Branch',
            'name_ar' => 'فرع لندن',
            'is_active' => true,
        ]
        );

        // 3. Create Branches for Company B
        $branchB1 = Branch::updateOrCreate(
        ['code' => 'RUH-01'],
        [
            'company_id' => $companyB->id,
            'name_en' => 'Riyadh Branch',
            'name_ar' => 'فرع الرياض',
            'is_active' => true,
        ]
        );

        $branchB2 = Branch::updateOrCreate(
        ['code' => 'DXB-01'],
        [
            'company_id' => $companyB->id,
            'name_en' => 'Dubai Branch',
            'name_ar' => 'فرع دبي',
            'is_active' => true,
        ]
        );

        // 4. Create Users
        $users = [
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@aurex.com',
                'company_id' => $companyA->id,
                'branch_id' => $branchA1->id,
                'role' => 'Super Admin'
            ],
            [
                'name' => 'Admin Company A',
                'email' => 'adminA@aurex.com',
                'company_id' => $companyA->id,
                'branch_id' => $branchA1->id,
                'role' => 'Company Admin'
            ],
            [
                'name' => 'Admin Company B',
                'email' => 'adminB@aurex.com',
                'company_id' => $companyB->id,
                'branch_id' => $branchB1->id,
                'role' => 'Company Admin'
            ],
            [
                'name' => 'Manager London',
                'email' => 'managerA2@aurex.com',
                'company_id' => $companyA->id,
                'branch_id' => $branchA2->id,
                'role' => 'Branch Manager'
            ],
        ];

        foreach ($users as $userData) {
            $user = User::updateOrCreate(
            ['email' => $userData['email']],
            [
                'name' => $userData['name'],
                'password' => Hash::make('password'),
                'company_id' => $userData['company_id'],
                'branch_id' => $userData['branch_id'],
                'is_active' => true,
            ]
            );
            $user->syncRoles([$userData['role']]);
        }
    }
}
