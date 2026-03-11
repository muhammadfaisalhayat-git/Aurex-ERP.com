<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class InitialSystemSeeder extends Seeder
{
    /**
     * Run the database seeds for a fresh "Zero Data" deployment.
     * This populates only the absolutely necessary data to run the application.
     */
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,     // Create all system permissions
            RoleSeeder::class,           // Create base roles (Super Admin, Admin, etc.)
            BranchSeeder::class,         // Create initial branch (Main Branch)
            WarehouseSeeder::class,      // Create initial warehouse
            UserSeeder::class,           // Create the default administrator account
            TaxSettingSeeder::class,     // Initialize tax configuration
            CurrencySeeder::class,       // Initialize base currencies
        ]);

        // Add the primary admin email to system settings
        \DB::table('system_settings')->updateOrInsert(
            ['key' => 'admin_primary_email'],
            [
                'value' => 'admin@aurex.com',
                'type' => 'string',
                'group' => 'maintenance',
                'display_name_en' => 'Admin Primary Email',
                'display_name_ar' => 'البريد الإلكتروني الأساسي للمسؤول',
                'is_editable' => 1
            ]
        );
    }
}
