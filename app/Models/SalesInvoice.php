<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToTenant;

class SalesInvoice extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'company_id',
        'document_number',
        'invoice_number',
        'invoice_date',
        'due_date',
        'customer_id',
        'branch_id',
        'warehouse_id',
        'salesman_id',
        'reference_type',
        'reference_id',
        'reference_number',
        'status',
        'payment_terms',
        'subtotal',
        'discount_amount',
        'tax_rate',
        'tax_amount',
        'total_amount',
        'paid_amount',
        'balance_amount',
        'notes',
        'created_by',
        'posted_by',
        'posted_at',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'posted_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
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
        return $this->hasMany(SalesInvoiceItem::class);
    }

    public function salesReturns()
    {
        return $this->hasMany(SalesReturn::class);
    }

    public function stockIssueOrders()
    {
        return $this->hasMany(StockIssueOrder::class, 'reference_id')
            ->where('reference_type', 'sales_invoice');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopePosted($query)
    {
        return $query->where('status', 'posted');
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', ['draft', 'posted', 'partial']);
    }

    public function isPosted()
    {
        return in_array($this->status, ['posted', 'paid', 'partial', 'overdue']);
    }

    public function isEditable()
    {
        return $this->status === 'draft';
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
        $this->total_amount = $netAmount;
        $this->balance_amount = $netAmount - $this->paid_amount;
        $this->save();
    }

    public function post()
    {
        if ($this->isPosted()) {
            return false;
        }

        $this->status = 'posted';
        $this->posted_by = auth()->id();
        $this->posted_at = now();
        $this->save();

        // Update customer balance
        if ($this->customer) {
            $this->customer->updateBalance($this->total_amount);
        }

        // Create stock issue order
        $this->createStockIssueOrder();

        return true;
    }

    public function unpost()
    {
        if (!$this->isPosted()) {
            return false;
        }

        if ($this->paid_amount > 0) {
            return false;
        }

        // Reverse customer balance
        if ($this->customer) {
            $this->customer->updateBalance(-$this->total_amount);
        }

        // Cancel related stock issue orders
        foreach ($this->stockIssueOrders as $issueOrder) {
            $issueOrder->unpost();
        }

        $this->status = 'draft';
        $this->posted_by = null;
        $this->posted_at = null;
        $this->save();

        return true;
    }

    protected function createStockIssueOrder()
    {
        $issueOrder = StockIssueOrder::create([
            'document_number' => DocumentNumber::generate('stock_issue'),
            'issue_date' => $this->invoice_date,
            'warehouse_id' => $this->warehouse_id,
            'reference_type' => 'sales_invoice',
            'reference_id' => $this->id,
            'reference_number' => $this->document_number,
            'issue_type' => 'sales',
            'status' => 'draft',
            'notes' => 'Auto-generated from Sales Invoice: ' . $this->document_number,
            'created_by' => auth()->id(),
        ]);

        foreach ($this->items as $item) {
            $issueOrder->items()->create([
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'notes' => null,
            ]);
        }

        $issueOrder->post();
    }
}
