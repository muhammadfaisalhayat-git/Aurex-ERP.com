<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        \DB::table('roles')->updateOrInsert(
            ['name' => 'HR Manager', 'guard_name' => 'web'],
            [
                'display_name_en' => 'HR Manager',
                'display_name_ar' => 'مدير الموارد البشرية',
                'is_system' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \DB::table('roles')->where('name', 'HR Manager')->delete();
    }
};
