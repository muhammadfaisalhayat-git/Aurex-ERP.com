<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockSupplyItem extends Model
{
    use HasFactory;

    protected $table = 'stock_supply_items';

    protected $fillable = [
        'stock_supply_id',
        'product_id',
        'quantity',
        'unit_cost',
        'total_cost',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'unit_cost' => 'decimal:4',
        'total_cost' => 'decimal:2',
    ];

    public function stockSupply()
    {
        return $this->belongsTo(StockSupply::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
