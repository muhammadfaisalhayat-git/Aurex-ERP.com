<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToTenant;

class Customer extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'company_id',
        'code',
        'name_en',
        'name_ar',
        'group_id',
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
        'credit_limit',
        'credit_days',
        'opening_balance',
        'current_balance',
        'status',
        'salesman_id',
        'notes',
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'opening_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
    ];

    public function group()
    {
        return $this->belongsTo(CustomerGroup::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function salesman()
    {
        return $this->belongsTo(User::class, 'salesman_id');
    }

    public function customerRequests()
    {
        return $this->hasMany(CustomerRequest::class);
    }

    public function quotations()
    {
        return $this->hasMany(Quotation::class);
    }

    public function salesContracts()
    {
        return $this->hasMany(SalesContract::class);
    }

    public function salesInvoices()
    {
        return $this->hasMany(SalesInvoice::class);
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
        $lastCustomer = self::orderByRaw('CAST(code AS INTEGER) DESC')->first();
        $nextNumber = 1;

        if ($lastCustomer && is_numeric($lastCustomer->code)) {
            $nextNumber = (int) $lastCustomer->code + 1;
        }

        return str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }
}
