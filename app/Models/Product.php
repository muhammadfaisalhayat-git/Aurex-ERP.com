<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToTenant;

class Product extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'company_id',
        'branch_id',
        'code',
        'name_en',
        'name_ar',
        'name_foreign',
        'category_id',
        'description',
        'type',
        'barcode',
        'gtin',
        'hsn_code',
        'manufacturer_code',
        'ref_code',
        'sku',
        'cost_price',
        'primary_cost',
        'sale_price',
        'tax_rate',
        'unit_of_measure',
        'default_unit',
        'weight',
        'volume',
        'length',
        'width',
        'height',
        'area',
        'size_dimension',
        'decimals_count',
        'reorder_level',
        'reorder_quantity',
        'purchase_inv_no',
        'return_period',
        'item_activity',
        'level',
        'measure',
        'color',
        'season',
        'material',
        'brand',
        'manufacturer_company',
        'country_of_origin',
        'items_storage',
        'weights_base',
        'inactivation_date',
        'deactivation_reason',
        'is_active',
        'is_sellable',
        'is_purchasable',
        'is_weighted',
        'is_reserved',
        'is_not_for_sale',
        'is_controlled',
        'allow_fractions',
        'sold_in_cash',
        'is_asset',
        'use_partition',
        'is_compound',
        'is_component',
        'is_non_returnable',
        'use_expiry_date',
        'is_requirement',
        'show_in_vss',
        'use_custodians',
        'use_in_crm',
        'has_alternatives',
        'item_code_as_serial',
        'show_in_css',
        'return_period_before_expiry',
        'no_of_printing_times',
        'no_of_modifications',
    ];

    protected $casts = [
        'cost_price' => 'decimal:2',
        'primary_cost' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'weight' => 'decimal:3',
        'volume' => 'decimal:3',
        'length' => 'decimal:2',
        'width' => 'decimal:2',
        'height' => 'decimal:2',
        'area' => 'decimal:2',
        'size_dimension' => 'decimal:2',
        'is_active' => 'boolean',
        'is_sellable' => 'boolean',
        'is_purchasable' => 'boolean',
        'is_weighted' => 'boolean',
        'is_reserved' => 'boolean',
        'is_not_for_sale' => 'boolean',
        'is_controlled' => 'boolean',
        'allow_fractions' => 'boolean',
        'sold_in_cash' => 'boolean',
        'is_asset' => 'boolean',
        'use_partition' => 'boolean',
        'is_compound' => 'boolean',
        'is_component' => 'boolean',
        'is_non_returnable' => 'boolean',
        'use_expiry_date' => 'boolean',
        'is_requirement' => 'boolean',
        'show_in_vss' => 'boolean',
        'use_custodians' => 'boolean',
        'use_in_crm' => 'boolean',
        'has_alternatives' => 'boolean',
        'item_code_as_serial' => 'boolean',
        'show_in_css' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(ProductCategory::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function units()
    {
        return $this->hasMany(ProductUnit::class);
    }

    public function bomComponents()
    {
        return $this->hasMany(ProductBom::class, 'product_id');
    }

    public function bomParents()
    {
        return $this->hasMany(ProductBom::class, 'component_id');
    }

    public function stockBalances()
    {
        return $this->hasMany(StockBalance::class);
    }

    public function getAvailableStockAttribute()
    {
        if (array_key_exists('stock_balances_sum_available_quantity', $this->attributes)) {
            return $this->attributes['stock_balances_sum_available_quantity'] ?? 0;
        }
        return $this->stockBalances()->sum('available_quantity') ?? 0;
    }

    public function stockLedgers()
    {
        return $this->hasMany(StockLedger::class);
    }

    public function getStockInWarehouse($warehouseId)
    {
        $balance = $this->stockBalances()
            ->where('warehouse_id', $warehouseId)
            ->first();
        return $balance ? $balance->available_quantity : 0;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeSellable($query)
    {
        return $query->where('is_sellable', true);
    }

    public function scopePurchasable($query)
    {
        return $query->where('is_purchasable', true);
    }

    public function getNameAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->name_ar : $this->name_en;
    }

    public function isComposite()
    {
        return $this->type === 'composite';
    }
}
