<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommissionStatementDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'commission_statement_id',
        'sales_invoice_id',
        'invoice_amount',
        'commission_rate',
        'commission_amount',
    ];

    protected $casts = [
        'invoice_amount' => 'decimal:2',
        'commission_rate' => 'decimal:4',
        'commission_amount' => 'decimal:2',
    ];

    public function commissionStatement()
    {
        return $this->belongsTo(CommissionStatement::class);
    }

    public function salesInvoice()
    {
        return $this->belongsTo(SalesInvoice::class);
    }
}
