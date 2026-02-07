<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'document_number',
        'order_number',
        'order_date',
        'expected_delivery_date',
        'delivery_date',
        'customer_id',
        'branch_id',
        'warehouse_id',
        'salesman_id',
        'quotation_id',
        'status',
        'subtotal',
        'discount_amount',
        'tax_rate',
        'tax_amount',
        'shipping_amount',
        'total_amount',
        'invoiced_amount',
        'balance_amount',
        'delivery_address',
        'terms_conditions',
        'notes',
        'created_by',
        'confirmed_by',
        'confirmed_at',
        'converted_by',
        'converted_at',
    ];

    protected $casts = [
        'order_date' => 'date',
        'expected_delivery_date' => 'date',
        'delivery_date' => 'date',
        'confirmed_at' => 'datetime',
        'converted_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'shipping_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'invoiced_amount' => 'decimal:2',
        'balance_amount' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function salesman()
    {
        return $this->belongsTo(User::class, 'salesman_id');
    }

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function confirmer()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    public function converter()
    {
        return $this->belongsTo(User::class, 'converted_by');
    }

    public function items()
    {
        return $this->hasMany(SalesOrderItem::class);
    }

    public function statusHistory()
    {
        return $this->hasMany(SalesOrderStatusHistory::class);
    }

    public function salesInvoices()
    {
        return $this->hasMany(SalesInvoice::class);
    }

    public function isDraft()
    {
        return $this->status === 'draft';
    }

    public function isConfirmed()
    {
        return in_array($this->status, ['confirmed', 'processing', 'partial', 'shipped', 'delivered']);
    }

    public function isFullyInvoiced()
    {
        return $this->invoiced_amount >= $this->total_amount;
    }

    public function canBeInvoiced()
    {
        return $this->isConfirmed() && !$this->isFullyInvoiced();
    }

    public function getPendingInvoiceAmount()
    {
        return $this->total_amount - $this->invoiced_amount;
    }

    public function calculateTotals()
    {
        $subtotal = 0;
        $discountAmount = 0;
        $taxAmount = 0;
        $netAmount = 0;

        foreach ($this->items as $item) {
            $subtotal += $item->gross_amount;
            $discountAmount += $item->discount_amount;
            $taxAmount += $item->tax_amount;
            $netAmount += $item->net_amount;
        }

        $this->subtotal = $subtotal;
        $this->discount_amount = $discountAmount;
        $this->tax_amount = $taxAmount;
        $this->total_amount = $netAmount + $this->shipping_amount;
        $this->balance_amount = $this->total_amount - $this->invoiced_amount;
        $this->save();
    }

    public function confirm($userId)
    {
        if (!$this->isDraft()) {
            return false;
        }

        $this->update([
            'status' => 'confirmed',
            'confirmed_by' => $userId,
            'confirmed_at' => now(),
        ]);

        $this->statusHistory()->create([
            'status' => 'confirmed',
            'changed_by' => $userId,
            'changed_at' => now(),
        ]);

        return true;
    }

    public function updateInvoicedAmount($amount)
    {
        $this->invoiced_amount += $amount;
        $this->balance_amount = $this->total_amount - $this->invoiced_amount;
        
        if ($this->isFullyInvoiced()) {
            $this->status = 'invoiced';
        } else {
            $this->status = 'partial';
        }
        
        $this->save();
    }

    public static function createFromQuotation(Quotation $quotation, $userId)
    {
        $orderNumber = DocumentNumber::generate('sales_order');
        
        $salesOrder = self::create([
            'document_number' => $orderNumber,
            'order_number' => $orderNumber,
            'order_date' => now(),
            'expected_delivery_date' => now()->addDays(7),
            'customer_id' => $quotation->customer_id,
            'branch_id' => $quotation->branch_id,
            'warehouse_id' => $quotation->warehouse_id,
            'salesman_id' => $quotation->salesman_id,
            'quotation_id' => $quotation->id,
            'status' => 'draft',
            'tax_rate' => $quotation->tax_rate,
            'delivery_address' => $quotation->customer->address,
            'notes' => 'Created from quotation: ' . $quotation->document_number,
            'created_by' => $userId,
        ]);

        // Copy items from quotation
        foreach ($quotation->items as $quotationItem) {
            SalesOrderItem::create([
                'sales_order_id' => $salesOrder->id,
                'product_id' => $quotationItem->product_id,
                'description' => $quotationItem->description,
                'quantity' => $quotationItem->quantity,
                'unit_price' => $quotationItem->unit_price,
                'discount_percentage' => $quotationItem->discount_percentage,
                'discount_amount' => $quotationItem->discount_amount,
                'gross_amount' => $quotationItem->gross_amount,
                'tax_rate' => $quotationItem->tax_rate,
                'tax_amount' => $quotationItem->tax_amount,
                'net_amount' => $quotationItem->net_amount,
            ]);
        }

        $salesOrder->calculateTotals();

        // Update quotation status
        $quotation->update([
            'status' => 'converted',
            'converted_by' => $userId,
            'converted_at' => now(),
            'converted_to_id' => $salesOrder->id,
            'converted_to_type' => 'sales_order',
        ]);

        return $salesOrder;
    }
}
