<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToTenant;

class LocalPurchase extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'company_id',
        'document_number',
        'invoice_number',
        'invoice_date',
        'supplier_name',
        'supplier_phone',
        'supplier_email',
        'supplier_address',
        'supplier_tax_number',
        'supplier_commercial_reg',
        'branch_id',
        'warehouse_id',
        'status',
        'subtotal',
        'discount_amount',
        'tax_rate',
        'tax_amount',
        'total_amount',
        'notes',
        'created_by',
        'posted_by',
        'posted_at',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'posted_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

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
        return $this->hasMany(LocalPurchaseItem::class);
    }

    public function isPosted()
    {
        return $this->status === 'posted';
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

        foreach ($this->items as $item) {
            $subtotal += $item->total_amount;
            $discountAmount += $item->discount_amount;
            $taxAmount += $item->tax_amount;
        }

        $this->subtotal = $subtotal;
        $this->discount_amount = $discountAmount;
        $this->tax_amount = $taxAmount;
        $this->total_amount = $subtotal;
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

        // Update stock
        foreach ($this->items as $item) {
            $stockBalance = StockBalance::firstOrCreate(
                [
                    'product_id' => $item->product_id,
                    'warehouse_id' => $this->warehouse_id,
                ],
                [
                    'quantity' => 0,
                    'reserved_quantity' => 0,
                    'available_quantity' => 0,
                    'average_cost' => 0,
                ]
            );

            // Update average cost
            $oldQty = $stockBalance->quantity;
            $newQty = $item->quantity;
            $oldAvgCost = $stockBalance->average_cost;
            $newCost = $item->unit_price;

            if ($oldQty + $newQty > 0) {
                $stockBalance->average_cost = (($oldQty * $oldAvgCost) + ($newQty * $newCost)) / ($oldQty + $newQty);
            }

            $stockBalance->quantity += $item->quantity;
            $stockBalance->available_quantity += $item->quantity;
            $stockBalance->save();

            // Create stock ledger entry
            StockLedger::create([
                'product_id' => $item->product_id,
                'warehouse_id' => $this->warehouse_id,
                'transaction_date' => $this->invoice_date,
                'reference_type' => 'local_purchase',
                'reference_id' => $this->id,
                'reference_number' => $this->document_number,
                'movement_type' => 'in',
                'quantity' => $item->quantity,
                'unit_cost' => $item->unit_price,
                'total_cost' => $item->unit_price * $item->quantity,
                'balance_quantity' => $stockBalance->quantity,
                'notes' => 'Local Purchase: ' . $this->document_number,
            ]);
        }

        // GL Posting
        app(\App\Services\AccountingService::class)->postLocalPurchase($this);

        return true;
    }

    public function unpost()
    {
        if (!$this->isPosted()) {
            return false;
        }

        // Reverse stock entries
        foreach ($this->items as $item) {
            $stockBalance = StockBalance::where('product_id', $item->product_id)
                ->where('warehouse_id', $this->warehouse_id)
                ->first();

            if ($stockBalance) {
                $stockBalance->quantity -= $item->quantity;
                $stockBalance->available_quantity -= $item->quantity;
                $stockBalance->save();
            }
        }

        $this->status = 'draft';
        $this->posted_by = null;
        $this->posted_at = null;
        $this->save();

        // GL Unposting
        app(\App\Services\AccountingService::class)->unpostLocalPurchase($this);

        return true;
    }
}
