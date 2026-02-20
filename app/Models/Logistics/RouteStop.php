<?php

namespace App\Models\Logistics;

use App\Models\TransportOrder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RouteStop extends Model
{
    use HasFactory;

    protected $fillable = [
        'transport_order_id',
        'sequence',
        'location_name',
        'address',
        'planned_arrival',
        'actual_arrival',
        'actual_departure',
        'status',
        'notes'
    ];

    protected $casts = [
        'planned_arrival' => 'datetime',
        'actual_arrival' => 'datetime',
        'actual_departure' => 'datetime'
    ];

    public function transportOrder()
    {
        return $this->belongsTo(TransportOrder::class);
    }
}
