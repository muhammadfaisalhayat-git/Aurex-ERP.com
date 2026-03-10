<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class CompositeAssemblyComponent extends Model
{
    use HasFactory;
    protected $fillable = ['composite_assembly_id', 'component_id', 'measurement_unit_id', 'quantity_used', 'unit_cost', 'total_cost'];
    protected $casts = ['quantity_used' => 'decimal:3', 'unit_cost' => 'decimal:4', 'total_cost' => 'decimal:2'];
    public function assembly()
    {
        return $this->belongsTo(CompositeAssembly::class);
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
