<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToTenant;

class TransportOrder extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;
    protected $fillable = ['company_id', 'document_number', 'order_date', 'trailer_id', 'delivery_vehicle_id', 'driver_id', 'branch_id', 'route_from', 'route_to', 'scheduled_date', 'completion_date', 'status', 'reference_type', 'reference_id', 'notes', 'created_by', 'closed_by', 'closed_at'];
    protected $casts = ['order_date' => 'date', 'scheduled_date' => 'date', 'completion_date' => 'date', 'closed_at' => 'datetime'];

    public function trailer()
    {
        return $this->belongsTo(Trailer::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(\App\Models\Logistics\DeliveryVehicle::class , 'delivery_vehicle_id');
    }

    public function driver()
    {
        return $this->belongsTo(User::class , 'driver_id');
    }

    public function items()
    {
        return $this->hasMany(TransportOrderItem::class);
    }

    public function routeStops()
    {
        return $this->hasMany(\App\Models\Logistics\RouteStop::class);
    }
}
