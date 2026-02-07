<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommissionStatement extends Model
{
    use HasFactory;

    protected $fillable = [
        'commission_run_id',
        'salesman_id',
        'total_sales',
        'total_returns',
        'net_sales',
        'commission_amount',
        'status',
    ];

    protected $casts = [
        'total_sales' => 'decimal:2',
        'total_returns' => 'decimal:2',
        'net_sales' => 'decimal:2',
        'commission_amount' => 'decimal:2',
    ];

    public function commissionRun()
    {
        return $this->belongsTo(CommissionRun::class);
    }

    public function salesman()
    {
        return $this->belongsTo(User::class, 'salesman_id');
    }

    public function details()
    {
        return $this->hasMany(CommissionStatementDetail::class);
    }
}
