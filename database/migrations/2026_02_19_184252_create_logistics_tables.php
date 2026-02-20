<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration 
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('delivery_vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('plate_number')->unique();
            $table->string('brand');
            $table->string('model');
            $table->string('type'); // truck, van, trailer
            $table->string('fuel_type')->default('diesel');
            $table->decimal('max_payload', 10, 2)->nullable();
            $table->date('last_maintenance_date')->nullable();
            $table->integer('last_odometer_reading')->default(0);
            $table->string('status')->default('available'); // available, in_transit, maintenance, retired
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('fuel_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('delivery_vehicle_id')->constrained()->onDelete('cascade');
            $table->date('entry_date');
            $table->decimal('liters', 8, 2);
            $table->decimal('cost_per_liter', 10, 2);
            $table->decimal('total_cost', 12, 2);
            $table->integer('odometer_reading');
            $table->string('fuel_station')->nullable();
            $table->foreignId('logged_by')->constrained('users');
            $table->timestamps();
        });

        Schema::create('route_stops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transport_order_id')->constrained()->onDelete('cascade');
            $table->integer('sequence')->default(1);
            $table->string('location_name');
            $table->string('address')->nullable();
            $table->dateTime('planned_arrival')->nullable();
            $table->dateTime('actual_arrival')->nullable();
            $table->dateTime('actual_departure')->nullable();
            $table->string('status')->default('pending'); // pending, arrived, departed, skipped
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::table('transport_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('transport_orders', 'delivery_vehicle_id')) {
                $table->foreignId('delivery_vehicle_id')->after('trailer_id')->nullable()->constrained();
            }
            if (!Schema::hasColumn('transport_orders', 'driver_id')) {
                $table->foreignId('driver_id')->after('delivery_vehicle_id')->nullable()->constrained('users');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_vehicles');
    }
};
