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
        'registration_number',
        'registration_date',
        'name_en',
        'name_ar',
        'trade_name',
        'customer_type',
        'id_number',
        'group_id',
        'branch_id',
        'contact_person',
        'contact_position',
        'phone',
        'mobile',
        'email',
        'website',
        'address',
        'shipping_address',
        'city',
        'region',
        'postal_code',
        'country',
        'tax_number',
        'vat_certificate_number',
        'commercial_registration',
        'credit_limit',
        'credit_days',
        'payment_terms',
        'opening_balance',
        'current_balance',
        'status',
        'salesman_id',
        'notes',
        'submitted_by',
        'reviewed_by',
        'reviewed_at',
        'rejection_reason',
        'business_type',
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'opening_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'registration_date' => 'date',
        'reviewed_at' => 'datetime',
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
        return $this->belongsTo(User::class , 'salesman_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class , 'submitted_by');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class , 'reviewed_by');
    }

    public function documents()
    {
        return $this->hasMany(CustomerRegistrationDocument::class);
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
            $nextNumber = (int)$lastCustomer->code + 1;
        }

        return str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

    public static function generateRegistrationCode()
    {
        $prefix = 'REG-';
        $latest = self::whereNotNull('registration_number')->orderBy('id', 'desc')->first();
        $number = $latest ? (int)str_replace($prefix, '', $latest->registration_number) + 1 : 1;
        return $prefix . str_pad($number, 5, '0', STR_PAD_LEFT);
    }
}
