<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToTenant;

class StockSupply extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $table = 'stock_supply';

    protected $fillable = [
        'company_id',
        'document_number',
        'supply_date',
        'warehouse_id',
        'vendor_id',
        'reference_number',
        'status',
        'total_amount',
        'notes',
        'created_by',
        'posted_by',
        'posted_at',
    ];

    protected $casts = [
        'supply_date' => 'date',
        'posted_at' => 'datetime',
        'total_amount' => 'decimal:2',
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function poster()
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    public function items()
    {
        return $this->hasMany(StockSupplyItem::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function isPosted()
    {
        return $this->status === 'posted';
    }
}
