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
        return DocumentNumber::generate('purchase_invoice');
    }

    public function post()
    {
        if ($this->isPosted()) {
            return false;
        }

        return \Illuminate\Support\Facades\DB::transaction(function () {
            $this->status = 'posted';
            $this->posted_by = auth()->id();
            $this->posted_at = now();
            $this->save();

            // Update vendor balance
            if ($this->vendor) {
                $this->vendor->updateBalance($this->total_amount);
            }

            // Create stock receiving
            $this->createStockReceiving();

            // Accounting integration
            app(\App\Services\AccountingService::class)->postPurchaseInvoice($this);

            return true;
        });
    }

    public function unpost()
    {
        if (!$this->isPosted()) {
            return false;
        }

        return \Illuminate\Support\Facades\DB::transaction(function () {
            // Reverse vendor balance
            if ($this->vendor) {
                $this->vendor->updateBalance(-$this->total_amount);
            }

            // Unpost associated stock receiving
            $receiving = StockReceiving::where('reference_type', 'purchase_invoice')
                ->where('reference_id', $this->id)
                ->first();

            if ($receiving) {
                $receiving->unpost();
            }

            // Reverse accounting entries
            app(\App\Services\AccountingService::class)->unpostDocument('purchase_invoice', $this->id);

            $this->status = 'draft';
            $this->posted_by = null;
            $this->posted_at = null;
            $this->save();

            return true;
        });
    }

    protected function createStockReceiving()
    {
        $receiving = StockReceiving::create([
            'company_id' => $this->company_id,
            'document_number' => DocumentNumber::generate('stock_receiving'),
            'receiving_date' => $this->invoice_date,
            'warehouse_id' => $this->warehouse_id,
            'vendor_id' => $this->vendor_id,
            'reference_type' => 'purchase_invoice',
            'reference_id' => $this->id,
            'status' => 'pending',
            'notes' => 'Auto-generated from Purchase Invoice: ' . $this->document_number,
            'created_by' => auth()->id(),
        ]);

        foreach ($this->items as $item) {
            $receiving->items()->create([
                'product_id' => $item->product_id,
                'ordered_quantity' => $item->quantity,
                'received_quantity' => $item->quantity,
                'notes' => null,
            ]);
        }

        $receiving->post();
    }
}