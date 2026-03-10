<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesReturnItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'sales_return_id',
        'product_id',
        'measurement_unit_id',
        'quantity',
        'unit_price',
        'tax_amount',
        'total_amount',
        'notes'
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'unit_price' => 'decimal:4',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2'
    ];

    public function salesReturn()
    {
        return $this->belongsTo(SalesReturn::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function measurementUnit()
    {
        return $this->belongsTo(MeasurementUnit::class);
    }
}
