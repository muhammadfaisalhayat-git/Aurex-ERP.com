<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToTenant;

class StockIssueOrder extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'company_id',
        'document_number',
        'issue_date',
        'warehouse_id',
        'reference_type',
        'reference_id',
        'reference_number',
        'issue_type',
        'customer_id',
        'vendor_id',
        'status',
        'notes',
        'created_by',
        'posted_by',
        'posted_at'
    ];

    protected $casts = [
        'issue_date' => 'date',
        'posted_at' => 'datetime'
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function items()
    {
        return $this->hasMany(StockIssueOrderItem::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function poster()
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function isPosted()
    {
        return $this->status === 'posted';
    }

    public function post()
    {
        if ($this->isPosted()) {
            return false;
        }

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            $stockService = app(\App\Services\StockManagementService::class);
            $accountingService = app(\App\Services\AccountingService::class);

            // Record stock movements
            foreach ($this->items()->with('product')->get() as $item) {
                $stockService->recordMovement([
                    'product_id' => $item->product_id,
                    'warehouse_id' => $this->warehouse_id,
                    'movement_type' => 'out',
                    'quantity' => $item->quantity,
                    'unit_cost' => $item->product->average_cost ?? 0,
                    'reference_type' => 'stock_issue',
                    'reference_id' => $this->id,
                    'reference_number' => $this->document_number,
                    'notes' => 'Stock Issue: ' . $this->document_number
                ]);
            }

            // Accounting integration
            if (in_array($this->issue_type, ['wastage', 'adjustment'])) {
                $accountingService->postStockAdjustment($this);
            }

            $this->update([
                'status' => 'posted',
                'posted_by' => auth()->id(),
                'posted_at' => now(),
            ]);

            \Illuminate\Support\Facades\DB::commit();
            return true;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Stock Issue Order Posting Failed: ' . $e->getMessage());
            return false;
        }
    }

    public function unpost()
    {
        if (!$this->isPosted()) {
            return false;
        }

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            $stockService = app(\App\Services\StockManagementService::class);

            // Reverse stock movements
            $stockService->reverseMovement('stock_issue', $this->id);

            $this->update([
                'status' => 'draft',
                'posted_by' => null,
                'posted_at' => null,
            ]);

            \Illuminate\Support\Facades\DB::commit();
            return true;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Stock Issue Order Unposting Failed: ' . $e->getMessage());
            return false;
        }
    }
}
