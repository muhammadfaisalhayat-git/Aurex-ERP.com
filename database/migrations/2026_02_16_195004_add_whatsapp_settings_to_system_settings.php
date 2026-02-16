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
        \Illuminate\Support\Facades\DB::table('system_settings')->insert([
            [
                'key' => 'whatsapp_instance_id',
                'value' => '',
                'type' => 'string',
                'group' => 'whatsapp',
                'display_name_en' => 'WhatsApp Instance ID',
                'display_name_ar' => 'معرف مثيل واتساب',
                'description' => 'UltraMsg Instance ID for WhatsApp automation',
                'is_editable' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'whatsapp_token',
                'value' => '',
                'type' => 'string',
                'group' => 'whatsapp',
                'display_name_en' => 'WhatsApp Token',
                'display_name_ar' => 'رمز واتساب',
                'description' => 'UltraMsg Token for WhatsApp automation',
                'is_editable' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \Illuminate\Support\Facades\DB::table('system_settings')
            ->whereIn('key', ['whatsapp_instance_id', 'whatsapp_token'])
            ->delete();
    }
};
