<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReceiptVoucher extends Model
{
    use HasFactory, BelongsToTenant, SoftDeletes;

    protected $fillable = [
        'company_id',
        'voucher_number',
        'voucher_date',
        'bank_account_id',
        'payer_name',
        'payment_method',
        'reference_number',
        'amount',
        'description',
        'status',
        'customer_id',
        'chart_of_account_id',
        'created_by',
        'posted_by',
        'posted_at',
        'beneficiary_id',
        'beneficiary_type',
    ];

    public function beneficiary()
    {
        return $this->morphTo();
    }

    protected $casts = [
        'voucher_date' => 'date',
        'posted_at' => 'datetime',
        'amount' => 'decimal:2'
    ];

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function chartOfAccount()
    {
        return $this->belongsTo(ChartOfAccount::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class , 'created_by');
    }

    public function poster()
    {
        return $this->belongsTo(User::class , 'posted_by');
    }
}
