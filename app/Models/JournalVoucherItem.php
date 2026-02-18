<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JournalVoucherItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'journal_voucher_id',
        'main_account_id',
        'chart_of_account_id',
        'debit',
        'credit',
        'tax_amount',
        'notes',
        'customer_id',
        'vendor_id',
    ];

    public function journalVoucher()
    {
        return $this->belongsTo(JournalVoucher::class);
    }

    public function mainAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'main_account_id');
    }

    public function account()
    {
        return $this->belongsTo(ChartOfAccount::class, 'chart_of_account_id');
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
