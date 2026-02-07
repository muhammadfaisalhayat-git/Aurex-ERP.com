<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'tax_enabled',
        'default_tax_rate',
        'rounding_mode',
        'tax_name_en',
        'tax_name_ar',
        'tax_number',
    ];

    protected $casts = [
        'tax_enabled' => 'boolean',
        'default_tax_rate' => 'decimal:2',
    ];

    public static function getCurrent()
    {
        return self::first() ?? self::create([
            'tax_enabled' => true,
            'default_tax_rate' => 15.00,
            'rounding_mode' => 'per_line',
            'tax_name_en' => 'VAT',
            'tax_name_ar' => 'ضريبة القيمة المضافة',
        ]);
    }
}
