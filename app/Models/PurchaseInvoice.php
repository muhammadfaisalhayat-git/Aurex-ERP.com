<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToTenant;

class PurchaseInvoice extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'company_id',
        'document_number',
        'invoice_number',
        'invoice_date',
        'due_date',
        'vendor_id',
        'branch_id',
        'warehouse_id',
        'purchase_order_number',
        'status',
        'payment_terms',
        'subtotal',
        'discount_amount',
        'tax_rate',
        'tax_amount',
        'shipping_amount',
        'total_amount',
        'paid_amount',
        'balance_amount',
        'notes',
        'created_by',
        'posted_by',
        'posted_at'
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'posted_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'shipping_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'balance_amount' => 'decimal:2'
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
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
        return $this->hasMany(PurchaseInvoiceItem::class);
    }

    public function isPosted()
    {
        return $this->status === 'posted';
    }

    public static function generateNextDocumentNumber()
    {
        $lastInvoice = self::orderBy('id', 'desc')->first();
        if (!$lastInvoice || !preg_match('/PINV-(\d+)/', $lastInvoice->document_number, $matches)) {
            return 'PINV-001';
        }

        $nextNumber = intval($matches[1]) + 1;
        return 'PINV-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }
}