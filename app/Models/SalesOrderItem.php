<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'sales_order_id',
        'product_id',
        'description',
        'quantity',
        'delivered_quantity',
        'invoiced_quantity',
        'unit_price',
        'discount_percentage',
        'discount_amount',
        'gross_amount',
        'tax_rate',
        'tax_amount',
        'net_amount',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'delivered_quantity' => 'decimal:3',
        'invoiced_quantity' => 'decimal:3',
        'unit_price' => 'decimal:4',
        'discount_percentage' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'gross_amount' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
    ];

    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getPendingQuantity()
    {
        return $this->quantity - $this->delivered_quantity;
    }

    public function getPendingInvoiceQuantity()
    {
        return $this->quantity - $this->invoiced_quantity;
    }
}
