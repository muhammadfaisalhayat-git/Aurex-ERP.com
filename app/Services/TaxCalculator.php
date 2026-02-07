<?php

namespace App\Services;

use App\Models\TaxSetting;

class TaxCalculator
{
    protected $taxSetting;

    public function __construct()
    {
        $this->taxSetting = TaxSetting::first();
    }

    /**
     * Calculate tax for tax-inclusive pricing
     * Formula: gross = (P×Q) − discount
     *          net = gross / (1+r)
     *          tax = gross − net
     */
    public function calculateInclusiveTax($unitPrice, $quantity, $discountPercentage = 0, $taxRate = null)
    {
        $taxRate = $taxRate ?? $this->taxSetting?->default_tax_rate ?? 0;
        
        // Calculate gross amount (price * quantity - discount)
        $lineTotal = $unitPrice * $quantity;
        $discountAmount = $lineTotal * ($discountPercentage / 100);
        $grossAmount = $lineTotal - $discountAmount;
        
        // Calculate net amount (gross / (1 + tax_rate))
        $netAmount = $grossAmount / (1 + ($taxRate / 100));
        
        // Calculate tax amount (gross - net)
        $taxAmount = $grossAmount - $netAmount;

        return [
            'gross_amount' => round($grossAmount, 2),
            'net_amount' => round($netAmount, 2),
            'tax_amount' => round($taxAmount, 2),
            'discount_amount' => round($discountAmount, 2),
            'tax_rate' => $taxRate,
        ];
    }

    /**
     * Calculate tax for tax-exclusive pricing
     */
    public function calculateExclusiveTax($netAmount, $taxRate = null)
    {
        $taxRate = $taxRate ?? $this->taxSetting?->default_tax_rate ?? 0;
        
        $taxAmount = $netAmount * ($taxRate / 100);
        $grossAmount = $netAmount + $taxAmount;

        return [
            'net_amount' => round($netAmount, 2),
            'tax_amount' => round($taxAmount, 2),
            'gross_amount' => round($grossAmount, 2),
            'tax_rate' => $taxRate,
        ];
    }

    public function isTaxEnabled()
    {
        return $this->taxSetting?->tax_enabled ?? false;
    }

    public function getDefaultTaxRate()
    {
        return $this->taxSetting?->default_tax_rate ?? 0;
    }

    public function getRoundingMode()
    {
        return $this->taxSetting?->rounding_mode ?? 'per_line';
    }
}
