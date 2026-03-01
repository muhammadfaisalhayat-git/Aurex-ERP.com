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

            // Accounting integration
            app(\App\Services\AccountingService::class)->postSalesReturn($this);

            return true;
        });
    }
}
