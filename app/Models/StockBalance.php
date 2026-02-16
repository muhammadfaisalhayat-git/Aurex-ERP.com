<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class StockBalance extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'stock_balances';

    protected $fillable = [
        'company_id',
        'product_id',
        'warehouse_id',
        'quantity',
        'reserved_quantity',
        'available_quantity',
        'average_cost',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'reserved_quantity' => 'decimal:3',
        'available_quantity' => 'decimal:3',
        'average_cost' => 'decimal:4',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function updateBalance($quantity, $cost = null)
    {
        $this->quantity += $quantity;
        $this->available_quantity = $this->quantity - $this->reserved_quantity;

        if ($cost && $quantity > 0) {
            // Update average cost using weighted average
            $totalCost = ($this->quantity - $quantity) * $this->average_cost + $quantity * $cost;
            $this->average_cost = $totalCost / $this->quantity;
        }

        $this->save();
    }
}
