<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class StockLedger extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'stock_ledger';

    protected $fillable = [
        'company_id',
        'product_id',
        'measurement_unit_id',
        'warehouse_id',
        'transaction_date',
        'reference_type',
        'reference_id',
        'reference_number',
        'movement_type',
        'quantity',
        'unit_cost',
        'total_cost',
        'balance_quantity',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'transaction_date' => 'datetime',
        'quantity' => 'decimal:3',
        'unit_cost' => 'decimal:4',
        'total_cost' => 'decimal:2',
        'balance_quantity' => 'decimal:3',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function measurementUnit()
    {
        return $this->belongsTo(MeasurementUnit::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
