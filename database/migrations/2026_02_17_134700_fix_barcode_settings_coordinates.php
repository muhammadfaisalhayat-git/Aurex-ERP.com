<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('barcode_settings')->where('pos_x_name', 0)->update(['pos_x_name' => 25.00]);
        DB::table('barcode_settings')->where('pos_y_name', 0)->update(['pos_y_name' => 5.00]);
        DB::table('barcode_settings')->where('pos_x_code', 0)->update(['pos_x_code' => 25.00]);
        DB::table('barcode_settings')->where('pos_y_code', 0)->update(['pos_y_code' => 22.00]);
        DB::table('barcode_settings')->where('pos_x_price', 0)->update(['pos_x_price' => 25.00]);
        DB::table('barcode_settings')->where('pos_y_price', 0)->update(['pos_y_price' => 25.00]);
        DB::table('barcode_settings')->where('pos_x_custom', 0)->update(['pos_x_custom' => 25.00]);
        DB::table('barcode_settings')->where('pos_y_custom', 0)->update(['pos_y_custom' => 28.00]);
        DB::table('barcode_settings')->where('pos_x_barcode', 0)->update(['pos_x_barcode' => 25.00]);
        DB::table('barcode_settings')->where('pos_y_barcode', 0)->update(['pos_y_barcode' => 12.00]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reverse logic needed for data correction
    }
};
