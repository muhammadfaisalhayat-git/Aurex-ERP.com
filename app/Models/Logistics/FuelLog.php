<?php

namespace App\Models\Logistics;

use App\Models\User;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FuelLog extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'company_id',
        'delivery_vehicle_id',
        'entry_date',
        'liters',
        'cost_per_liter',
        'total_cost',
        'odometer_reading',
        'fuel_station',
        'logged_by'
    ];

    protected $casts = [
        'entry_date' => 'date',
        'liters' => 'decimal:2',
        'cost_per_liter' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'odometer_reading' => 'integer'
    ];

    public function vehicle()
    {
        return $this->belongsTo(DeliveryVehicle::class , 'delivery_vehicle_id');
    }

    public function logger()
    {
        return $this->belongsTo(User::class , 'logged_by');
    }

    public function post()
    {
        return \DB::transaction(function () {
            // Accounting integration
            app(\App\Services\AccountingService::class)->postFuelLog($this);

            return true;
        });
    }
}
