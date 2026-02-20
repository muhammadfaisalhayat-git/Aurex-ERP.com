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
        return $this->belongsTo(User::class , 'created_by');
    }

    public function workOrders()
    {
        return $this->hasMany(WorkOrder::class);
    }

    public function qualityControls()
    {
        return $this->morphMany(QualityControl::class , 'reference');
    }

    public function post()
    {
        if ($this->status === 'completed') {
            return false;
        }

        return \DB::transaction(function () {
            $this->status = 'completed';
            $this->end_date = now();
            $this->save();

            // Accounting integration
            app(\App\Services\AccountingService::class)->postProductionCompletion($this);

            return true;
        });
    }
}
