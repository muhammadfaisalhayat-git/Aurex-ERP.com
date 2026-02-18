<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // SQLite doesn't support dropping indexes easily in some versions via Schema,
        // so we use raw SQL for dropping and then recreate.
        // Also handling the unique index replacement.

        $dbType = DB::getDriverName();

        if ($dbType === 'sqlite') {
            DB::statement('DROP INDEX IF EXISTS system_settings_key_unique');
        } else {
            Schema::table('system_settings', function (Blueprint $table) {
                $table->dropUnique(['key']);
            });
        }

        Schema::table('system_settings', function (Blueprint $table) {
            $table->unique(['key', 'company_id']);
        });
    }

    public function down(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->dropUnique(['key', 'company_id']);
            $table->unique(['key']);
        });
    }
};
