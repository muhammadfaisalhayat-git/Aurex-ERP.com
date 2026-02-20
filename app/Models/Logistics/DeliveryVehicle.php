<?php

namespace App\Models\Logistics;

use App\Models\Company;
use App\Models\TransportOrder;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeliveryVehicle extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'company_id',
        'plate_number',
        'brand',
        'model',
        'type',
        'fuel_type',
        'max_payload',
        'last_maintenance_date',
        'last_odometer_reading',
        'status'
    ];

    protected $casts = [
        'max_payload' => 'decimal:2',
        'last_maintenance_date' => 'date',
        'last_odometer_reading' => 'integer'
    ];

    public function fuelLogs()
    {
        return $this->hasMany(FuelLog::class);
    }

    public function transportOrders()
    {
        return $this->hasMany(TransportOrder::class);
    }
}
