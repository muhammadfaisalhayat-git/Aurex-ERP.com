<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToTenant;

class JournalVoucher extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'company_id',
        'branch_id',
        'voucher_number',
        'voucher_date',
        'reference_no',
        'description',
        'status',
        'created_by',
        'approved_by',
    ];

    protected $casts = [
        'voucher_date' => 'date',
    ];

    public function items()
    {
        return $this->hasMany(JournalVoucherItem::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
