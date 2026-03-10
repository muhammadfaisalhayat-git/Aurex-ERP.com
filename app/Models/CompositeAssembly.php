<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class CompositeAssembly extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['company_id', 'document_number', 'assembly_date', 'warehouse_id', 'product_id', 'measurement_unit_id', 'quantity', 'cost_per_unit', 'total_cost', 'status', 'notes', 'created_by', 'posted_by', 'posted_at'];
    protected $casts = ['assembly_date' => 'date', 'posted_at' => 'datetime', 'quantity' => 'decimal:3', 'cost_per_unit' => 'decimal:4', 'total_cost' => 'decimal:2'];

    public function measurementUnit()
    {
        return $this->belongsTo(MeasurementUnit::class);
    }
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

    public function post()
    {
        if ($this->status === 'posted') {
            return false;
        }

        return \Illuminate\Support\Facades\DB::transaction(function () {
            $this->status = 'posted';
            $this->posted_by = auth()->id();
            $this->posted_at = now();
            $this->save();

            $stockService = app(\App\Services\StockManagementService::class);

            // 1. Deduct components (outgoing)
            foreach ($this->components as $component) {
                $stockService->recordMovement([
                    'product_id' => $component->component_id,
                    'measurement_unit_id' => $component->measurement_unit_id,
                    'warehouse_id' => $this->warehouse_id,
                    'movement_type' => 'out',
                    'quantity' => $component->quantity_used,
                    'unit_cost' => $component->unit_cost ?? 0,
                    'reference_type' => 'composite_assembly',
                    'reference_id' => $this->id,
                    'reference_number' => $this->document_number,
                    'notes' => 'Assembly Component for: ' . $this->document_number
                ]);
            }

            // 2. Add finished product (incoming)
            $stockService->recordMovement([
                'product_id' => $this->product_id,
                'measurement_unit_id' => $this->measurement_unit_id,
                'warehouse_id' => $this->warehouse_id,
                'movement_type' => 'in',
                'quantity' => $this->quantity,
                'unit_cost' => $this->cost_per_unit,
                'reference_type' => 'composite_assembly',
                'reference_id' => $this->id,
                'reference_number' => $this->document_number,
                'notes' => 'Assembly Finished Good: ' . $this->document_number
            ]);

            return true;
        });
    }

    public function unpost()
    {
        if ($this->status !== 'posted') {
            return false;
        }

        return \Illuminate\Support\Facades\DB::transaction(function () {
            $stockService = app(\App\Services\StockManagementService::class);
            $stockService->reverseMovement('composite_assembly', $this->id);

            $this->status = 'draft';
            $this->posted_by = null;
            $this->posted_at = null;
            $this->save();

            return true;
        });
    }
}
