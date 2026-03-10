<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class TransportOrderItem extends Model
{
    use HasFactory;
    protected $fillable = ['transport_order_id', 'product_id', 'measurement_unit_id', 'quantity', 'notes'];
    protected $casts = ['quantity' => 'decimal:3'];
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function measurementUnit()
    {
        return $this->belongsTo(MeasurementUnit::class);
    }
}
