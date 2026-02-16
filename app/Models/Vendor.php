<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToTenant;

class Vendor extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'company_id',
        'code',
        'name_en',
        'name_ar',
        'branch_id',
        'contact_person',
        'phone',
        'mobile',
        'email',
        'address',
        'city',
        'region',
        'postal_code',
        'tax_number',
        'commercial_registration',
        'opening_balance',
        'current_balance',
        'status',
        'notes',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function purchaseInvoices()
    {
        return $this->hasMany(PurchaseInvoice::class);
    }

    public function stockSupplies()
    {
        return $this->hasMany(StockSupply::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function getNameAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->name_ar : $this->name_en;
    }

    public function updateBalance($amount)
    {
        $this->current_balance += $amount;
        $this->save();
    }

    public static function generateNextCode()
    {
        $lastVendor = self::orderBy('id', 'desc')->first();
        if (!$lastVendor || !preg_match('/VND-(\d+)/', $lastVendor->code, $matches)) {
            return 'VND-001';
        }

        $nextNumber = intval($matches[1]) + 1;
        return 'VND-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }
}
