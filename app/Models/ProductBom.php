<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class ProductBom extends Model
{
    use HasFactory;
    protected $table = 'product_bom';
    protected $fillable = ['product_id', 'component_id', 'measurement_unit_id', 'quantity', 'waste_percentage', 'notes'];
    protected $casts = ['quantity' => 'decimal:3', 'waste_percentage' => 'decimal:2'];
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function component()
    {
        return $this->belongsTo(Product::class, 'component_id');
    }
    public function measurementUnit()
    {
        return $this->belongsTo(MeasurementUnit::class);
    }
}
