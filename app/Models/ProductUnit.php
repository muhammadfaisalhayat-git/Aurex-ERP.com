<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductUnit extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'measurement_unit_id',
        'package',
        'price',
        'description',
        'foreign_description',
        'barcode',
        'is_purchase_unit',
        'is_transfer_unit',
        'is_stocktaking_unit',
        'is_not_for_sale',
        'is_inactive',
        'is_production_unit',
        'is_store_unit',
        'is_customer_self_service',
        'excluded_from_discount',
    ];

    protected $casts = [
        'package' => 'decimal:4',
        'price' => 'decimal:4',
        'is_purchase_unit' => 'boolean',
        'is_transfer_unit' => 'boolean',
        'is_stocktaking_unit' => 'boolean',
        'is_not_for_sale' => 'boolean',
        'is_inactive' => 'boolean',
        'is_production_unit' => 'boolean',
        'is_store_unit' => 'boolean',
        'is_customer_self_service' => 'boolean',
        'excluded_from_discount' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function measurementUnit()
    {
        return $this->belongsTo(MeasurementUnit::class);
    }
}
