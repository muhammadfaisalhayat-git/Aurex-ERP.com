<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocalPurchaseItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'local_purchase_id',
        'product_id',
        'measurement_unit_id',
        'description',
        'quantity',
        'unit_price',
        'discount_percentage',
        'discount_amount',
        'tax_rate',
        'tax_amount',
        'total_amount',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'unit_price' => 'decimal:4',
        'discount_percentage' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function localPurchase()
    {
        return $this->belongsTo(LocalPurchase::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function measurementUnit()
    {
        return $this->belongsTo(MeasurementUnit::class);
    }

    public static function calculateLineTotals($quantity, $unitPrice, $discountPercentage, $taxRate)
    {
        $lineTotal = $quantity * $unitPrice;
        $discountAmount = $lineTotal * ($discountPercentage / 100);
        $taxableAmount = $lineTotal - $discountAmount;
        $taxAmount = $taxableAmount * ($taxRate / 100);
        $totalAmount = $taxableAmount + $taxAmount;

        return [
            'discount_amount' => round($discountAmount, 2),
            'tax_amount' => round($taxAmount, 2),
            'total_amount' => round($totalAmount, 2),
        ];
    }
}
