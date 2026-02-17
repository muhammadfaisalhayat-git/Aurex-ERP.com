<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class BarcodeSetting extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'company_id',
        'branch_id',
        'barcode_type',
        'page_size',
        'label_width',
        'label_height',
        'labels_per_row',
        'margin_top',
        'margin_bottom',
        'margin_left',
        'margin_right',
        'show_product_name',
        'show_product_code',
        'show_product_price',
        'custom_text',
        'show_custom_text',
        'template',
        'font_size_name',
        'font_size_code',
        'font_size_price',
        'font_size_custom',
        'font_size_barcode',
        'barcode_color',
        'text_color',
        'content_alignment',
        'check_digit',
        'ucc_ean_128',
        'pos_x_name',
        'pos_y_name',
        'pos_x_code',
        'pos_y_code',
        'pos_x_price',
        'pos_y_price',
        'pos_x_custom',
        'pos_y_custom',
        'pos_x_barcode',
        'pos_y_barcode',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'show_product_name' => 'boolean',
        'show_product_code' => 'boolean',
        'show_product_price' => 'boolean',
        'show_custom_text' => 'boolean',
        'check_digit' => 'boolean',
        'ucc_ean_128' => 'boolean',
        'label_width' => 'decimal:2',
        'label_height' => 'decimal:2',
        'margin_top' => 'decimal:2',
        'margin_bottom' => 'decimal:2',
        'margin_left' => 'decimal:2',
        'margin_right' => 'decimal:2',
        'font_size_name' => 'integer',
        'font_size_code' => 'integer',
        'font_size_price' => 'integer',
        'font_size_custom' => 'integer',
        'pos_x_name' => 'decimal:2',
        'pos_y_name' => 'decimal:2',
        'pos_x_code' => 'decimal:2',
        'pos_y_code' => 'decimal:2',
        'pos_x_price' => 'decimal:2',
        'pos_y_price' => 'decimal:2',
        'pos_x_custom' => 'decimal:2',
        'pos_y_custom' => 'decimal:2',
        'pos_x_barcode' => 'decimal:2',
        'pos_y_barcode' => 'decimal:2',
    ];

    /**
     * Get the current settings for the active company and branch.
     */
    public static function current()
    {
        $companyId = session('active_company_id') ?? auth()->user()?->company_id;
        $branchId = session('active_branch_id') ?? auth()->user()?->branch_id;

        $settings = self::where('company_id', $companyId)
            ->where('branch_id', $branchId)
            ->first();

        // If no settings found with company/branch, try without filters
        if (!$settings) {
            $settings = self::first();
        }

        return $settings ?? self::defaults();
    }

    /**
     * Get default settings.
     */
    public static function defaults()
    {
        return new self([
            'barcode_type' => 'C128',
            'page_size' => 'A4',
            'label_width' => 50.00,
            'label_height' => 30.00,
            'labels_per_row' => 3,
            'margin_top' => 5.00,
            'margin_bottom' => 5.00,
            'margin_left' => 5.00,
            'margin_right' => 5.00,
            'show_product_name' => true,
            'show_product_code' => true,
            'show_product_price' => true,
            'custom_text' => 'Aurex ERP',
            'show_custom_text' => false,
            'template' => 'default',
            'font_size_name' => 10,
            'font_size_code' => 8,
            'font_size_price' => 12,
            'font_size_custom' => 8,
            'font_size_barcode' => 40,
            'barcode_color' => '#000000',
            'text_color' => '#000000',
            'content_alignment' => 'center',
            'check_digit' => false,
            'ucc_ean_128' => false,
            'pos_x_name' => 25.00,
            'pos_y_name' => 5.00,
            'pos_x_code' => 25.00,
            'pos_y_code' => 22.00,
            'pos_x_price' => 25.00,
            'pos_y_price' => 25.00,
            'pos_x_custom' => 25.00,
            'pos_y_custom' => 28.00,
            'pos_x_barcode' => 25.00,
            'pos_y_barcode' => 12.00,
        ]);
    }
}
