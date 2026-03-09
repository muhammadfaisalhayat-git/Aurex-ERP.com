<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToTenant;

class SalesReturn extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;
    protected $fillable = ['company_id', 'document_number', 'return_number', 'return_date', 'sales_invoice_id', 'return_type', 'bank_account_id', 'customer_id', 'branch_id', 'warehouse_id', 'status', 'return_reason', 'reason_description', 'subtotal', 'tax_amount', 'total_amount', 'notes', 'created_by', 'posted_by', 'posted_at'];
    protected $casts = ['return_date' => 'date', 'posted_at' => 'datetime', 'subtotal' => 'decimal:2', 'tax_amount' => 'decimal:2', 'total_amount' => 'decimal:2'];
    public function salesInvoice()
    {
        return $this->belongsTo(SalesInvoice::class);
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    public function items()
    {
        return $this->hasMany(SalesReturnItem::class);
    }
    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
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

    public function postedBy()
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    public function scopeActive($query)
    {
        return $query->where('status', '!=', 'cancelled');
    }

    public function isPosted()
    {
        return $this->status === 'posted';
    }

    public function isEditable()
    {
        return $this->status === 'draft';
    }

    public function post()
    {
        if ($this->isPosted()) {
            return false;
        }

        return \DB::transaction(function () {
            $this->status = 'posted';
            $this->posted_by = auth()->id();
            $this->posted_at = now();
            $this->save();

            // Update customer balance (Sales Return REDUCES outstanding balance if Credit)
            if ($this->return_type === 'credit' && $this->customer) {
                $this->customer->updateBalance(-$this->total_amount);
            }

            // Update bank balance if Cash
            if ($this->return_type === 'cash' && $this->bankAccount) {
                $this->bankAccount->decrement('current_balance', (float) $this->total_amount);
            }

            // Create stock receiving
            $this->createStockReceiving();

            // Accounting integration
            app(\App\Services\AccountingService::class)->postSalesReturn($this);

            return true;
        });
    }

    public function unpost()
    {
        if (!$this->isPosted()) {
            return false;
        }

        return \DB::transaction(function () {
            // Reverse customer balance
            if ($this->return_type === 'credit' && $this->customer) {
                $this->customer->updateBalance($this->total_amount); // Add back (Return reduces, so reversal adds)
            }

            // Reverse bank balance
            if ($this->return_type === 'cash' && $this->bankAccount) {
                $this->bankAccount->increment('current_balance', (float) $this->total_amount);
            }

            // Unpost associated stock receiving
            $receiving = StockReceiving::where('reference_type', 'sales_return')
                ->where('reference_id', $this->id)
                ->first();

            if ($receiving) {
                $receiving->unpost();
            }

            // Reverse accounting entries
            app(\App\Services\AccountingService::class)->unpostDocument('sales_return', $this->id);

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
            'receiving_date' => $this->return_date,
            'warehouse_id' => $this->warehouse_id,
            'customer_id' => $this->customer_id,
            'reference_type' => 'sales_return',
            'reference_id' => $this->id,
            'status' => 'pending',
            'notes' => 'Auto-generated from Sales Return: ' . $this->document_number,
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
