<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupplyOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'document_number',
        'order_number',
        'order_date',
        'expected_delivery_date',
        'vendor_id',
        'branch_id',
        'warehouse_id',
        'status',
        'subtotal',
        'discount_amount',
        'tax_rate',
        'tax_amount',
        'shipping_amount',
        'total_amount',
        'terms_conditions',
        'notes',
        'created_by',
        'sent_by',
        'sent_at',
        'converted_by',
        'converted_at',
        'purchase_invoice_id',
    ];

    protected $casts = [
        'order_date' => 'date',
        'expected_delivery_date' => 'date',
        'sent_at' => 'datetime',
        'converted_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'shipping_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
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

    public function sender()
    {
        return $this->belongsTo(User::class, 'sent_by');
    }

    public function converter()
    {
        return $this->belongsTo(User::class, 'converted_by');
    }

    public function items()
    {
        return $this->hasMany(SupplyOrderItem::class);
    }

    public function statusHistory()
    {
        return $this->hasMany(SupplyOrderStatusHistory::class);
    }

    public function purchaseInvoice()
    {
        return $this->belongsTo(PurchaseInvoice::class, 'purchase_invoice_id');
    }

    public function isDraft()
    {
        return $this->status === 'draft';
    }

    public function isSent()
    {
        return $this->status === 'sent';
    }

    public function isReceived()
    {
        return $this->status === 'received';
    }

    public function canBeInvoiced()
    {
        return in_array($this->status, ['sent', 'partial', 'received']) && !$this->purchase_invoice_id;
    }

    public function calculateTotals()
    {
        $subtotal = 0;
        $discountAmount = 0;
        $taxAmount = 0;

        foreach ($this->items as $item) {
            $subtotal += $item->total_amount;
            $discountAmount += $item->discount_amount;
            $taxAmount += $item->tax_amount;
        }

        $this->subtotal = $subtotal;
        $this->discount_amount = $discountAmount;
        $this->tax_amount = $taxAmount;
        $this->total_amount = $subtotal + $this->shipping_amount;
        $this->save();
    }

    public function markAsSent($userId)
    {
        $this->update([
            'status' => 'sent',
            'sent_by' => $userId,
            'sent_at' => now(),
        ]);

        $this->statusHistory()->create([
            'status' => 'sent',
            'changed_by' => $userId,
            'changed_at' => now(),
        ]);
    }

    public function convertToInvoice($userId)
    {
        if (!$this->canBeInvoiced()) {
            return false;
        }

        $this->update([
            'status' => 'received',
            'converted_by' => $userId,
            'converted_at' => now(),
        ]);

        return true;
    }
}
