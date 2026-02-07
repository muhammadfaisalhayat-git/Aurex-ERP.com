<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesInvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'sales_invoice_id',
        'product_id',
        'description',
        'quantity',
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
        'unit_price' => 'decimal:4',
        'discount_percentage' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'gross_amount' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
    ];

    public function salesInvoice()
    {
        return $this->belongsTo(SalesInvoice::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public static function calculateLineTotals($quantity, $unitPrice, $discountPercentage, $taxRate)
    {
        // Tax-inclusive pricing calculation
        // gross = (P×Q) − discount
        // net = gross / (1+r)
        // tax = gross − net

        $grossAmount = ($unitPrice * $quantity) * (1 - $discountPercentage / 100);
        $discountAmount = ($unitPrice * $quantity) * ($discountPercentage / 100);
        $netAmount = $grossAmount / (1 + $taxRate / 100);
        $taxAmount = $grossAmount - $netAmount;

        return [
            'gross_amount' => round($grossAmount, 2),
            'discount_amount' => round($discountAmount, 2),
            'net_amount' => round($netAmount, 2),
            'tax_amount' => round($taxAmount, 2),
        ];
    }
}
