<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockReceivingItem extends Model
{
    use HasFactory;

    protected $table = 'stock_receiving_items';

    protected $fillable = [
        'stock_receiving_id',
        'product_id',
        'measurement_unit_id',
        'ordered_quantity',
        'received_quantity',
        'notes'
    ];

    protected $casts = [
        'ordered_quantity' => 'decimal:3',
        'received_quantity' => 'decimal:3'
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
