<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerRequestItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_request_id',
        'product_id',
        'measurement_unit_id',
        'quantity',
        'notes',
        'unit_price',
        'tax_rate',
        'tax_amount',
        'total_amount'
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'unit_price' => 'decimal:4',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2'
    ];

    public function customerRequest()
    {
        return $this->belongsTo(CustomerRequest::class);
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
