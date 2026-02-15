<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToTenant;
class CompositeAssembly extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;
    protected $fillable = ['company_id', 'document_number', 'assembly_date', 'warehouse_id', 'product_id', 'quantity', 'cost_per_unit', 'total_cost', 'status', 'notes', 'created_by', 'posted_by', 'posted_at'];
    protected $casts = ['assembly_date' => 'date', 'posted_at' => 'datetime', 'quantity' => 'decimal:3', 'cost_per_unit' => 'decimal:4', 'total_cost' => 'decimal:2'];
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function components()
    {
        return $this->hasMany(CompositeAssemblyComponent::class);
    }
}
