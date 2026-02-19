<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToTenant;

class LedgerEntry extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'company_id',
        'branch_id',
        'chart_of_account_id',
        'parent_account_id',
        'transaction_date',
        'reference_type',
        'reference_id',
        'reference_number',
        'debit',
        'credit',
        'balance',
        'description',
        'cost_center_no',
        'activity_no',
        'lc_no',
        'rep',
        'collector_no',
        'promoter_code',
        'created_by',
        'customer_id',
        'vendor_id',
    ];

    protected $casts = [
        'transaction_date' => 'date',
    ];

    public function account()
    {
        return $this->belongsTo(ChartOfAccount::class, 'chart_of_account_id');
    }

    public function parentAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'parent_account_id');
    }

    public function reference()
    {
        return $this->morphTo();
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}
