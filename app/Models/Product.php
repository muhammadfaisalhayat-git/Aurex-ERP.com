<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name_en',
        'name_ar',
        'category_id',
        'description',
        'type',
        'barcode',
        'sku',
        'cost_price',
        'sale_price',
        'tax_rate',
        'unit_of_measure',
        'weight',
        'volume',
        'reorder_level',
        'reorder_quantity',
        'is_active',
        'is_sellable',
        'is_purchasable',
    ];

    protected $casts = [
        'cost_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'weight' => 'decimal:3',
        'volume' => 'decimal:3',
        'is_active' => 'boolean',
        'is_sellable' => 'boolean',
        'is_purchasable' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(ProductCategory::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
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
