<?php

namespace App\Models\Production;

use App\Models\Branch;
use App\Models\Product;
use App\Models\User;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductionOrder extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'company_id',
        'branch_id',
        'document_number',
        'product_id',
        'quantity',
        'start_date',
        'end_date',
        'status',
        'unit_cost',
        'total_cost',
        'notes',
        'created_by'
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function workOrders()
    {
        return $this->hasMany(WorkOrder::class);
    }

    public function qualityControls()
    {
        return $this->morphMany(QualityControl::class, 'reference');
    }

    public function post()
    {
        if ($this->status === 'completed') {
            return false;
        }

        return \Illuminate\Support\Facades\DB::transaction(function () {
            $this->status = 'completed';
            $this->end_date = now();
            $this->save();

            $stockService = app(\App\Services\StockManagementService::class);
            $warehouseId = $this->branch->warehouses()->first()?->id;

            if ($warehouseId) {
                // 1. Deduct BOM components (outgoing)
                if ($this->product && $this->product->bomComponents) {
                    foreach ($this->product->bomComponents as $bom) {
                        $stockService->recordMovement([
                            'product_id' => $bom->component_id,
                            'warehouse_id' => $warehouseId,
                            'movement_type' => 'out',
                            'quantity' => (float) $bom->quantity * (float) $this->quantity,
                            'unit_cost' => $bom->component->cost_price ?? 0,
                            'reference_type' => 'production_order',
                            'reference_id' => $this->id,
                            'reference_number' => $this->document_number,
                            'notes' => 'Production Consumption for: ' . $this->document_number
                        ]);
                    }
                }

                // 2. Add finished product (incoming)
                $stockService->recordMovement([
                    'product_id' => $this->product_id,
                    'warehouse_id' => $warehouseId,
                    'movement_type' => 'in',
                    'quantity' => $this->quantity,
                    'unit_cost' => $this->unit_cost,
                    'reference_type' => 'production_order',
                    'reference_id' => $this->id,
                    'reference_number' => $this->document_number,
                    'notes' => 'Production Output: ' . $this->document_number
                ]);
            }

            // Accounting integration
            app(\App\Services\AccountingService::class)->postProductionCompletion($this);

            return true;
        });
    }

    public function unpost()
    {
        if ($this->status !== 'completed') {
            return false;
        }

        return \Illuminate\Support\Facades\DB::transaction(function () {
            $stockService = app(\App\Services\StockManagementService::class);
            $stockService->reverseMovement('production_order', $this->id);

            $this->status = 'draft';
            $this->end_date = null;
            $this->save();

            return true;
        });
    }
}
